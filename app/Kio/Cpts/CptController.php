<?php

namespace App\Kio\Cpts;

use App\Http\Controllers\Controller;
use App\Kio\Cpts\Downtimes\CptDowntimeModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $per_page = isset($request->per_page) ? intval($request->per_page) : 10;
            $keyword = isset($request->search) ? $request->search : null;
            $garage = isset($request->garage) ? $request->garage : null;
            $place = isset($request->place) ? $request->place : null;
            $data = CptModel::whereHas('latest_cpt_place', function($query) use ($garage){
                $query->when($garage, function ($query) use ($garage){
                    $query->where('garage','like', '%'.$garage.'%');
                });
            })->whereHas('latest_cpt_place.place', function($query) use ($place){
                $query->when($place, function ($query) use ($place){
                    $query->where('title','like', '%'.$place.'%');
                });
            })->with(['latest_cpt_place' => function($query) use ($garage){
                $query->when($garage, function ($query) use ($garage){
                    $query->where('garage','like', '%'.$garage.'%');
                });
            },
                'latest_cpt_place.place' => function($query) use ($place){
                    $query->select('id','title');
                    $query->when($place, function ($query) use ($place){
                        $query->where('title','like', '%'.$place.'%');
                    });
                }
            ])->when($keyword, function ($query) use ($keyword){
                $query->where('title', 'like', '%'.$keyword.'%');
            })->paginate($per_page);
            return $this->responseSuccessPaginate($data, 'success!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function search_by_group(Request $request)
    {
        try {
            $keyword = isset($request->search) ? $request->search : null;
            $id_group = isset($request->id_group) ? $request->id_group : null;
            $id_place = isset($request->id_place) ? $request->id_place : null;
            $date = isset($request->date) ? $request->date : null;
            $data = CptModel::select('cpts.*', 'places_cpts.*')
                ->join('places_cpts', function ($join){
                    $join->on('places_cpts.id_cpt', '=', 'cpts.id')
                    ->whereNull('places_cpts.deleted_at');
                })
                ->when($keyword, function ($query) use ($keyword){
                    $query->where(function ($query) use ($keyword){
                        $query->where('cpts.title', 'like', '%'.$keyword.'%')
                            ->orWhere('places_cpts.garage', 'like', '%'.$keyword.'%')
                            ->orWhere('cpts.id', '=', $keyword);
                    });
                })->where('cpts.id_group', '=', $id_group)->where('places_cpts.id_place', '=',$id_place)
                ->whereRaw('? BETWEEN date_from AND COALESCE(date_to, ?)', [$date, $date])->get();
            return $this->responseSuccess($data, 'success!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $ret_val = CptModel::create([
                'title' => $request->title,
                'id_group' => $request->id_group,
                'status' => $request->status,
                'creator_id' =>$request->user()->id
            ]);

            CptPlaceModel::create([
                'id_place' => $request->id_place,
                'id_cpt'=> $ret_val->id,
                'garage'=> $request->garage,
                'date_from'=> $request->date_from,
                'date_to' => @$request->date_to,
                'creator_id' => $request->user()->id,
            ]);
            DB::commit();
            return $this->responseSuccess($ret_val, 'created successfully');
        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kio\Cpts\CptModel  $cptModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $cpt = CptModel::find($id)->load(['cpt_places.place:id,id_parent,id_manager,title','cpt_places.place.parent:id,title', 'cpt_places.place.manager:id,title']);
            return $this->responseSuccess($cpt, 'success!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Cpts\CptModel  $cptModel
     * @return \Illuminate\Http\Response
     */
    public function edit(CptModel $cptModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Cpts\CptModel  $cptModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $cpt = CptModel::find($id);
            $cpt->title = $request->title;
            $cpt->id_group = $request->id_group;
            $cpt->status = $request->status;
            $cpt->updater_id = $request->user()->id;
            $cpt->save();

            $insert_places= [];
            foreach ($request->cpt_places as $cpt_place) {
                $dontime_exists = CptDowntimeModel::where('id_cpt', '=', $cpt->id)->where(function ($query) use ($cpt_place){
                    $query->where('end_time', '>', $cpt_place['date_from'])->orWhereNull('end_time');
                })->whereNot('id_place', '=', $cpt_place['id_place'])->exists();
                if($dontime_exists){
                    return $this->responseError('В настоящее время транспорт находится в простое!', 'В настоящее время транспорт находится в простое!', Response::HTTP_BAD_REQUEST);
                }
                $insert_places[] = array(
                    'id' => @$cpt_place['id'],
                    'id_cpt' => $cpt->id,
                    'id_place' => $cpt_place['id_place'],
                    'garage' => $cpt_place['garage'],
                    'date_from' => $cpt_place['date_from'],
                    'date_to' => @$cpt_place['date_to'],
                    'updater_id' => $request->user()->id
                );
            }
            $arr_size = count($insert_places);
            if($arr_size >= 2){
                $last = $insert_places[$arr_size - 1];
                $pre_last = $insert_places[$arr_size - 2];
                if($last['id_cpt'] == $pre_last['id_cpt'] && $last['id_place'] == $pre_last['id_place'] && $last['garage'] == $pre_last['garage'] && !is_null($pre_last['date_to'])){
                    return $this->responseError('Транспорт на том же месте!', 'Транспорт на том же месте!', Response::HTTP_BAD_REQUEST);
                }
            }
            $cpt->cpt_places()->upsert($insert_places, ['id_cpt', 'id_place', 'garage', 'date_from', 'date_to', 'updater_id']);
            DB::commit();
            return $this->responseSuccess($cpt, 'updated successfully');
        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Cpts\CptModel  $cptModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $cpt = CptModel::find($id);
            $user_id = $request->user()->id;
            $cpt->deleter_id = $user_id;
            $cpt->save();
            foreach ($cpt->cpt_places as $cpt_place){
                $cpt_place->deleter_id = $user_id;
                $cpt_place->save();
            }
            $cpt->cpt_places()->delete();
            DB::commit();
            return $this->responseSuccess($cpt->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
