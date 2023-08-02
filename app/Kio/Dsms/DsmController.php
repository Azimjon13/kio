<?php

namespace App\Kio\Dsms;

use App\Http\Controllers\Controller;
use App\Kio\Dsms\Downtimes\DsmDowntimeModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DsmController extends Controller
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
            $data = DsmModel::whereHas('latest_dsm_place', function($query) use ($garage){
                $query->when($garage, function ($query) use ($garage){
                    $query->where('garage','like', '%'.$garage.'%');
                });
            })->whereHas('latest_dsm_place.place', function($query) use ($place){
                $query->when($place, function ($query) use ($place){
                    $query->where('title','like', '%'.$place.'%');
                });
            })->with(['latest_dsm_place' => function($query) use ($garage){
                $query->when($garage, function ($query) use ($garage){
                    $query->where('garage','like', '%'.$garage.'%');
                });
            },
                'latest_dsm_place.place' => function($query) use ($place){
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
            $data = DsmModel::select('dsms.*', 'places_dsms.*')
                ->join('places_dsms', function ($join){
                    $join->on('places_dsms.id_dsm', '=', 'dsms.id')
                    ->whereNull('places_dsms.deleted_at');
                })
                ->when($keyword, function ($query) use ($keyword){
                    $query->where(function ($query) use ($keyword){
                        $query->where('dsms.title', 'like', '%'.$keyword.'%')
                            ->orWhere('places_dsms.garage', 'like', '%'.$keyword.'%')
                            ->orWhere('dsms.id', '=', $keyword);
                    });
                })->where('dsms.id_group', '=', $id_group)->where('places_dsms.id_place', '=',$id_place)
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
            $ret_val = DsmModel::create([
                'title' => $request->title,
                'id_group' => $request->id_group,
                'status' => $request->status,
                'creator_id' =>$request->user()->id
            ]);

            DsmPlaceModel::create([
                'id_place' => $request->id_place,
                'id_dsm'=> $ret_val->id,
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
     * @param  \App\Kio\Dsms\DsmModel  $dsmModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $dsm = DsmModel::find($id)->load(['dsm_places.place:id,id_parent,id_manager,title','dsm_places.place.parent:id,title', 'dsm_places.place.manager:id,title']);
            return $this->responseSuccess($dsm, 'success!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Dsms\DsmModel  $dsmModel
     * @return \Illuminate\Http\Response
     */
    public function edit(DsmModel $dsmModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Dsms\DsmModel  $dsmModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $dsm = DsmModel::find($id);
            $dsm->title = $request->title;
            $dsm->id_group = $request->id_group;
            $dsm->status = $request->status;
            $dsm->updater_id = $request->user()->id;
            $dsm->save();

            $insert_places= [];
            foreach ($request->dsm_places as $dsm_place) {
                $dontime_exists = DsmDowntimeModel::where('id_dsm', '=', $dsm->id)->where(function ($query) use ($dsm_place){
                    $query->where('end_time', '>', $dsm_place['date_from'])->orWhereNull('end_time');
                })->whereNot('id_place', '=', $dsm_place['id_place'])->exists();
                if($dontime_exists){
                    return $this->responseError('В настоящее время транспорт находится в простое!', 'В настоящее время транспорт находится в простое!', Response::HTTP_BAD_REQUEST);
                }
                $insert_places[] = array(
                    'id' => @$dsm_place['id'],
                    'id_dsm' => $dsm->id,
                    'id_place' => $dsm_place['id_place'],
                    'garage' => $dsm_place['garage'],
                    'date_from' => $dsm_place['date_from'],
                    'date_to' => @$dsm_place['date_to'],
                    'updater_id' => $request->user()->id
                );
            }
            $arr_size = count($insert_places);
            if($arr_size >= 2){
                $last = $insert_places[$arr_size - 1];
                $pre_last = $insert_places[$arr_size - 2];
                if($last['id_dsm'] == $pre_last['id_dsm'] && $last['id_place'] == $pre_last['id_place'] && $last['garage'] == $pre_last['garage'] && !is_null($pre_last['date_to'])){
                    return $this->responseError('Транспорт на том же месте!', 'Транспорт на том же месте!', Response::HTTP_BAD_REQUEST);
                }
            }
            $dsm->dsm_places()->upsert($insert_places, ['id_dsm', 'id_place', 'garage', 'date_from', 'date_to', 'updater_id']);
            DB::commit();
            return $this->responseSuccess($dsm, 'updated successfully');
        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Dsms\DsmModel  $dsmModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $dsm = DsmModel::find($id);
            $user_id = $request->user()->id;
            $dsm->deleter_id = $user_id;
            $dsm->save();
            foreach ($dsm->dsm_places as $dsm_place){
                $dsm_place->deleter_id = $user_id;
                $dsm_place->save();
            }
            $dsm->dsm_places()->delete();
            DB::commit();
            return $this->responseSuccess($dsm->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
