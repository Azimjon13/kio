<?php

namespace App\Kio\Drills;

use App\Http\Controllers\Controller;
use App\Kio\Drills\Downtimes\DrillDowntimeModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DrillController extends Controller
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
            $data = DrillModel::whereHas('latest_drill_place', function($query) use ($garage){
                $query->when($garage, function ($query) use ($garage){
                    $query->where('garage','like', '%'.$garage.'%');
                });
            })->whereHas('latest_drill_place.place', function($query) use ($place){
                $query->when($place, function ($query) use ($place){
                    $query->where('title','like', '%'.$place.'%');
                });
            })->with(['latest_drill_place' => function($query) use ($garage){
                $query->when($garage, function ($query) use ($garage){
                    $query->where('garage','like', '%'.$garage.'%');
                });
            },
                'latest_drill_place.place' => function($query) use ($place){
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
            $data = DrillModel::select('drills.*', 'places_drills.*')
                ->join('places_drills', function ($join){
                    $join->on('places_drills.id_drill', '=', 'drills.id')
                    ->whereNull('places_drills.deleted_at');
                })
                ->when($keyword, function ($query) use ($keyword){
                    $query->where(function ($query) use ($keyword){
                        $query->where('drills.title', 'like', '%'.$keyword.'%')
                            ->orWhere('places_drills.garage', 'like', '%'.$keyword.'%')
                            ->orWhere('drills.id', '=', $keyword);
                    });
                })->where('drills.id_group', '=', $id_group)->where('places_drills.id_place', '=',$id_place)
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
            $ret_val = DrillModel::create([
                'title' => $request->title,
                'id_group' => $request->id_group,
                'status' => $request->status,
                'creator_id' =>$request->user()->id
            ]);

            DrillPlaceModel::create([
                'id_place' => $request->id_place,
                'id_drill'=> $ret_val->id,
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
     * @param  \App\Kio\Drills\DrillModel  $drillModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $drill = DrillModel::find($id)->load(['drill_places.place:id,id_parent,id_manager,title','drill_places.place.parent:id,title', 'drill_places.place.manager:id,title']);
            return $this->responseSuccess($drill, 'success!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Drills\DrillModel  $drillModel
     * @return \Illuminate\Http\Response
     */
    public function edit(DrillModel $drillModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Drills\DrillModel  $drillModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $drill = DrillModel::find($id);
            $drill->title = $request->title;
            $drill->id_group = $request->id_group;
            $drill->status = $request->status;
            $drill->updater_id = $request->user()->id;
            $drill->save();

            $insert_places= [];
            foreach ($request->drill_places as $drill_place) {
                $dontime_exists = DrillDowntimeModel::where('id_drill', '=', $drill->id)->where(function ($query) use ($drill_place){
                    $query->where('end_time', '>', $drill_place['date_from'])->orWhereNull('end_time');
                })->whereNot('id_place', '=', $drill_place['id_place'])->exists();
                if($dontime_exists){
                    return $this->responseError('В настоящее время транспорт находится в простое!', 'В настоящее время транспорт находится в простое!', Response::HTTP_BAD_REQUEST);
                }
                $insert_places[] = array(
                    'id' => @$drill_place['id'],
                    'id_drill' => $drill->id,
                    'id_place' => $drill_place['id_place'],
                    'garage' => $drill_place['garage'],
                    'date_from' => $drill_place['date_from'],
                    'date_to' => @$drill_place['date_to'],
                    'updater_id' => $request->user()->id
                );
            }
            $arr_size = count($insert_places);
            if($arr_size >= 2){
                $last = $insert_places[$arr_size - 1];
                $pre_last = $insert_places[$arr_size - 2];
                if($last['id_drill'] == $pre_last['id_drill'] && $last['id_place'] == $pre_last['id_place'] && $last['garage'] == $pre_last['garage'] && !is_null($pre_last['date_to'])){
                    return $this->responseError('Транспорт на том же месте!', 'Транспорт на том же месте!', Response::HTTP_BAD_REQUEST);
                }
            }
            $drill->drill_places()->upsert($insert_places, ['id_drill', 'id_place', 'garage', 'date_from', 'date_to', 'updater_id']);
            DB::commit();
            return $this->responseSuccess($drill, 'updated successfully');
        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Drills\DrillModel  $drillModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $drill = DrillModel::find($id);
            $user_id = $request->user()->id;
            $drill->deleter_id = $user_id;
            $drill->save();
            foreach ($drill->drill_places as $drill_place){
                $drill_place->deleter_id = $user_id;
                $drill_place->save();
            }
            $drill->drill_places()->delete();
            DB::commit();
            return $this->responseSuccess($drill->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
