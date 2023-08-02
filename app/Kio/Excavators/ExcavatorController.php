<?php

namespace App\Kio\Excavators;

use App\Http\Controllers\Controller;
use App\Kio\Excavators\Downtimes\ExcavatorDowntimeModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExcavatorController extends Controller
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
            $data = ExcavatorModel::whereHas('latest_excavator_place', function($query) use ($garage){
                $query->when($garage, function ($query) use ($garage){
                    $query->where('garage','like', '%'.$garage.'%');
                });
            })->whereHas('latest_excavator_place.place', function($query) use ($place){
                $query->when($place, function ($query) use ($place){
                    $query->where('title','like', '%'.$place.'%');
                });
            })->with(['latest_excavator_place' => function($query) use ($garage){
                $query->when($garage, function ($query) use ($garage){
                    $query->where('garage','like', '%'.$garage.'%');
                });
            },
                'latest_excavator_place.place' => function($query) use ($place){
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
            $data = ExcavatorModel::select('excavators.*', 'places_excavators.*')
                ->join('places_excavators', function ($join){
                    $join->on('places_excavators.id_excavator', '=', 'excavators.id')
                    ->whereNull('places_excavators.deleted_at');
                })
                ->when($keyword, function ($query) use ($keyword){
                    $query->where(function ($query) use ($keyword){
                        $query->where('excavators.title', 'like', '%'.$keyword.'%')
                            ->orWhere('places_excavators.garage', 'like', '%'.$keyword.'%')
                            ->orWhere('excavators.id', '=', $keyword);
                    });
                })->where('excavators.id_group', '=', $id_group)->where('places_excavators.id_place', '=',$id_place)
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
            $ret_val = ExcavatorModel::create([
                'title' => $request->title,
                'id_group' => $request->id_group,
                'status' => $request->status,
                'creator_id' =>$request->user()->id
            ]);

            ExcavatorPlaceModel::create([
                'id_place' => $request->id_place,
                'id_excavator'=> $ret_val->id,
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
     * @param  \App\Kio\Excavators\ExcavatorModel  $excavatorModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $excavator = ExcavatorModel::find($id)->load(['excavator_places.place:id,id_parent,id_manager,title','excavator_places.place.parent:id,title', 'excavator_places.place.manager:id,title']);
            return $this->responseSuccess($excavator, 'success!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Excavators\ExcavatorModel  $excavatorModel
     * @return \Illuminate\Http\Response
     */
    public function edit(ExcavatorModel $excavatorModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Excavators\ExcavatorModel  $excavatorModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $excavator = ExcavatorModel::find($id);
            $excavator->title = $request->title;
            $excavator->id_group = $request->id_group;
            $excavator->status = $request->status;
            $excavator->updater_id = $request->user()->id;
            $excavator->save();

            $insert_places= [];
            foreach ($request->excavator_places as $excavator_place) {
                $dontime_exists = ExcavatorDowntimeModel::where('id_excavator', '=', $excavator->id)->where(function ($query) use ($excavator_place){
                    $query->where('end_time', '>', $excavator_place['date_from'])->orWhereNull('end_time');
                })->whereNot('id_place', '=', $excavator_place['id_place'])->exists();
                if($dontime_exists){
                    return $this->responseError('В настоящее время транспорт находится в простое!', 'В настоящее время транспорт находится в простое!', Response::HTTP_BAD_REQUEST);
                }
                $insert_places[] = array(
                    'id' => @$excavator_place['id'],
                    'id_excavator' => $excavator->id,
                    'id_place' => $excavator_place['id_place'],
                    'garage' => $excavator_place['garage'],
                    'date_from' => $excavator_place['date_from'],
                    'date_to' => @$excavator_place['date_to'],
                    'updater_id' => $request->user()->id
                );
            }
            $arr_size = count($insert_places);
            if($arr_size >= 2){
                $last = $insert_places[$arr_size - 1];
                $pre_last = $insert_places[$arr_size - 2];
                if($last['id_excavator'] == $pre_last['id_excavator'] && $last['id_place'] == $pre_last['id_place'] && $last['garage'] == $pre_last['garage'] && !is_null($pre_last['date_to'])){
                    return $this->responseError('Транспорт на том же месте!', 'Транспорт на том же месте!', Response::HTTP_BAD_REQUEST);
                }
            }
            $excavator->excavator_places()->upsert($insert_places, ['id_excavator', 'id_place', 'garage', 'date_from', 'date_to', 'updater_id']);
            DB::commit();
            return $this->responseSuccess($excavator, 'updated successfully');
        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Excavators\ExcavatorModel  $excavatorModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $excavator = ExcavatorModel::find($id);
            $user_id = $request->user()->id;
            $excavator->deleter_id = $user_id;
            $excavator->save();
            foreach ($excavator->excavator_places as $excavator_place){
                $excavator_place->deleter_id = $user_id;
                $excavator_place->save();
            }
            $excavator->excavator_places()->delete();
            DB::commit();
            return $this->responseSuccess($excavator->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
