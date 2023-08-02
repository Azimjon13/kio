<?php

namespace App\Kio\Drills;

use App\Http\Controllers\Controller;
use App\Kio\Drills\Downtimes\DrillDowntimeModel;
use App\Kio\Excavators\ExcavatorPlaceModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DrillPlaceController extends Controller
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
            $dontime_exists = DrillDowntimeModel::where('id_drill', '=', $request->id_drill)->where(function ($query) use ($request){
                $query->where('end_time', '>', $request->date_from)->orWhereNull('end_time');
            })->whereNot('id_place', '=', $request->id_place)->exists();
            if($dontime_exists){
                return $this->responseError(null, 'В настоящее время транспорт находится в простое!', Response::HTTP_BAD_REQUEST);
            }else{
                $ret_val =  DrillPlaceModel::create([
                    'id_place' => $request->id_place,
                    'id_drill'=> $request->id_drill,
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
     * @param  \App\Kio\Drills\DrillPlaceModel  $drillDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $drillPlace = DrillPlaceModel::find($id);
            return $this->responseSuccess($drillPlace, 'success!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Drills\DrillPlaceModel  $drillDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function edit(DrillPlaceModel $drillDowntimeModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Drills\DrillPlaceModel  $drillDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $drillPlace = DrillPlaceModel::find($id);
            $drillPlace->id_drill = $request->id_drill;
            $drillPlace->id_place = $request->id_place;
            $drillPlace->date_from = $request->date_from;
            $drillPlace->date_to = $request->date_to;
            $drillPlace->updater_id = $request->user()->id;
            $drillPlace->save();
            return $this->responseSuccess($drillPlace, 'updated successfully');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Drills\DrillPlaceModel  $drillDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $drill_place = DrillPlaceModel::find($id);
            $drill_place->deleter_id = $request->user()->id;
            $drill_place->save();
            return $this->responseSuccess($drill_place->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
