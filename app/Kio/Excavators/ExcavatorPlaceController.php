<?php

namespace App\Kio\Excavators;

use App\Http\Controllers\Controller;
use App\Kio\Excavators\Downtimes\ExcavatorDowntimeModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExcavatorPlaceController extends Controller
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
            $dontime_exists = ExcavatorDowntimeModel::where('id_excavator', '=', $request->id_excavator)->where(function ($query) use ($request){
                $query->where('end_time', '>', $request->date_from)->orWhereNull('end_time');
            })->whereNot('id_place', '=', $request->id_place)->exists();
            if($dontime_exists){
                return $this->responseError(null, 'В настоящее время транспорт находится в простое!', Response::HTTP_BAD_REQUEST);
            }else{
                $ret_val =  ExcavatorPlaceModel::create([
                    'id_place' => $request->id_place,
                    'id_excavator'=> $request->id_excavator,
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
     * @param  \App\Kio\Excavators\ExcavatorPlaceModel  $excavatorDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $excavatorPlace = ExcavatorPlaceModel::find($id);
            return $this->responseSuccess($excavatorPlace, 'success!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Excavators\ExcavatorPlaceModel  $excavatorDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function edit(ExcavatorPlaceModel $excavatorDowntimeModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Excavators\ExcavatorPlaceModel  $excavatorDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $excavatorPlace = ExcavatorPlaceModel::find($id);
            $excavatorPlace->id_excavator = $request->id_excavator;
            $excavatorPlace->id_place = $request->id_place;
            $excavatorPlace->date_from = $request->date_from;
            $excavatorPlace->date_to = $request->date_to;
            $excavatorPlace->updater_id = $request->user()->id;
            $excavatorPlace->save();
            return $this->responseSuccess($excavatorPlace, 'updated successfully');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Excavators\ExcavatorPlaceModel  $excavatorDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $excavator_place = ExcavatorPlaceModel::find($id);
            $excavator_place->deleter_id = $request->user()->id;
            $excavator_place->save();
            return $this->responseSuccess($excavator_place->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
