<?php

namespace App\Kio\Trucks;

use App\Http\Controllers\Controller;
use App\Kio\Trucks\Downtimes\TruckDowntimeModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TruckController extends Controller
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
            $data = TruckModel::whereHas('latest_truck_place', function($query) use ($garage){
                $query->when($garage, function ($query) use ($garage){
                    $query->where('garage','like', '%'.$garage.'%');
                });
            })->whereHas('latest_truck_place.place', function($query) use ($place){
                $query->when($place, function ($query) use ($place){
                    $query->where('title','like', '%'.$place.'%');
                });
            })->with(['latest_truck_place' => function($query) use ($garage){
                $query->when($garage, function ($query) use ($garage){
                    $query->where('garage','like', '%'.$garage.'%');
                });
            },
                'latest_truck_place.place' => function($query) use ($place){
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
            $data = TruckModel::select('trucks.*', 'places_trucks.*')
                ->join('places_trucks', function ($join){
                    $join->on('places_trucks.id_truck', '=', 'trucks.id')
                    ->whereNull('places_trucks.deleted_at');
                })
                ->when($keyword, function ($query) use ($keyword){
                    $query->where(function ($query) use ($keyword){
                        $query->where('trucks.title', 'like', '%'.$keyword.'%')
                            ->orWhere('places_trucks.garage', 'like', '%'.$keyword.'%')
                            ->orWhere('trucks.id', '=', $keyword);
                    });
                })->where('trucks.id_group', '=', $id_group)->where('places_trucks.id_place', '=',$id_place)
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
            $ret_val = TruckModel::create([
                'title' => $request->title,
                'id_group' => $request->id_group,
                'status' => $request->status,
                'creator_id' =>$request->user()->id
            ]);

            TruckPlaceModel::create([
                'id_place' => $request->id_place,
                'id_truck'=> $ret_val->id,
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
     * @param  \App\Kio\Trucks\TruckModel  $truckModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $truck = TruckModel::find($id)->load(['truck_places.place:id,id_parent,id_manager,title','truck_places.place.parent:id,title', 'truck_places.place.manager:id,title']);
            return $this->responseSuccess($truck, 'success!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Trucks\TruckModel  $truckModel
     * @return \Illuminate\Http\Response
     */
    public function edit(TruckModel $truckModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Trucks\TruckModel  $truckModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $truck = TruckModel::find($id);
            $truck->title = $request->title;
            $truck->id_group = $request->id_group;
            $truck->status = $request->status;
            $truck->updater_id = $request->user()->id;
            $truck->save();

            $insert_places= [];
            foreach ($request->truck_places as $truck_place) {
                $dontime_exists = TruckDowntimeModel::where('id_truck', '=', $truck->id)->where(function ($query) use ($truck_place){
                    $query->where('end_time', '>', $truck_place['date_from'])->orWhereNull('end_time');
                })->whereNot('id_place', '=', $truck_place['id_place'])->exists();
                if($dontime_exists){
                    return $this->responseError('В настоящее время транспорт находится в простое!', 'В настоящее время транспорт находится в простое!', Response::HTTP_BAD_REQUEST);
                }
                $insert_places[] = array(
                    'id' => @$truck_place['id'],
                    'id_truck' => $truck->id,
                    'id_place' => $truck_place['id_place'],
                    'garage' => $truck_place['garage'],
                    'date_from' => $truck_place['date_from'],
                    'date_to' => @$truck_place['date_to'],
                    'updater_id' => $request->user()->id
                );
            }
            $arr_size = count($insert_places);
            if($arr_size >= 2){
                $last = $insert_places[$arr_size - 1];
                $pre_last = $insert_places[$arr_size - 2];
                if($last['id_truck'] == $pre_last['id_truck'] && $last['id_place'] == $pre_last['id_place'] && $last['garage'] == $pre_last['garage'] && !is_null($pre_last['date_to'])){
                    return $this->responseError('Транспорт на том же месте!', 'Транспорт на том же месте!', Response::HTTP_BAD_REQUEST);
                }
            }
            $truck->truck_places()->upsert($insert_places, ['id_truck', 'id_place', 'garage', 'date_from', 'date_to', 'updater_id']);
            DB::commit();
            return $this->responseSuccess($truck, 'updated successfully');
        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Trucks\TruckModel  $truckModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $truck = TruckModel::find($id);
            $user_id = $request->user()->id;
            $truck->deleter_id = $user_id;
            $truck->save();
            foreach ($truck->truck_places as $truck_place){
                $truck_place->deleter_id = $user_id;
                $truck_place->save();
            }
            $truck->truck_places()->delete();
            DB::commit();
            return $this->responseSuccess($truck->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
