<?php

namespace App\Kio\Trucks;

use App\Http\Controllers\Controller;
use App\Kio\Trucks\Downtimes\TruckDowntimeModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TruckPlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $dontime_exists = TruckDowntimeModel::where('id_truck', '=', $request->id_truck)->where(function ($query) use ($request){
                $query->where('end_time', '>', $request->date_from)->orWhereNull('end_time');
            })->whereNot('id_place', '=', $request->id_place)->exists();
            if($dontime_exists){
                return $this->responseError(null, 'В настоящее время транспорт находится в простое!', Response::HTTP_BAD_REQUEST);
            }else{
                $ret_val =  TruckPlaceModel::create([
                    'id_place' => $request->id_place,
                    'id_truck'=> $request->id_truck,
                    'garage'=> $request->garage,
                    'date_from'=> $request->date_from,
                    'date_to' => @$request->date_to,
                    'creator_id' => $request->user()->id,
                ]);
                return $this->responseSuccess($ret_val, 'created successfully');
            }
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kio\Trucks\TruckPlaceModel  $truckDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $truckPlace = TruckPlaceModel::find($id);
            return $this->responseSuccess($truckPlace, 'success!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Trucks\TruckPlaceModel  $truckDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function edit(TruckPlaceModel $truckDowntimeModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Trucks\TruckPlaceModel  $truckDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $truckPlace = TruckPlaceModel::find($id);
            $truckPlace->id_truck = $request->id_truck;
            $truckPlace->id_place = $request->id_place;
            $truckPlace->date_from = $request->date_from;
            $truckPlace->date_to = $request->date_to;
            $truckPlace->updater_id = $request->user()->id;
            $truckPlace->save();
            return $this->responseSuccess($truckPlace, 'updated successfully');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Trucks\TruckPlaceModel  $truckDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $truck_place = TruckPlaceModel::find($id);
            $truck_place->deleter_id = $request->user()->id;
            $truck_place->save();
            return $this->responseSuccess($truck_place->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
