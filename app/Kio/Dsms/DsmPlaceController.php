<?php

namespace App\Kio\Dsms;

use App\Http\Controllers\Controller;
use App\Kio\Dsms\Downtimes\DsmDowntimeModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DsmPlaceController extends Controller
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
            $dontime_exists = DsmDowntimeModel::where('id_dsm', '=', $request->id_dsm)->where(function ($query) use ($request){
                $query->where('end_time', '>', $request->date_from)->orWhereNull('end_time');
            })->whereNot('id_place', '=', $request->id_place)->exists();
            if($dontime_exists){
                return $this->responseError(null, 'В настоящее время транспорт находится в простое!', Response::HTTP_BAD_REQUEST);
            }else{
                $ret_val =  DsmPlaceModel::create([
                    'id_place' => $request->id_place,
                    'id_dsm'=> $request->id_dsm,
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
     * @param  \App\Kio\Dsms\DsmPlaceModel  $dsmDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $dsmPlace = DsmPlaceModel::find($id);
            return $this->responseSuccess($dsmPlace, 'success!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Dsms\DsmPlaceModel  $dsmDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function edit(DsmPlaceModel $dsmDowntimeModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Dsms\DsmPlaceModel  $dsmDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $dsmPlace = DsmPlaceModel::find($id);
            $dsmPlace->id_dsm = $request->id_dsm;
            $dsmPlace->id_place = $request->id_place;
            $dsmPlace->date_from = $request->date_from;
            $dsmPlace->date_to = $request->date_to;
            $dsmPlace->updater_id = $request->user()->id;
            $dsmPlace->save();
            return $this->responseSuccess($dsmPlace, 'updated successfully');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Dsms\DsmPlaceModel  $dsmDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $dsm_place = DsmPlaceModel::find($id);
            $dsm_place->deleter_id = $request->user()->id;
            $dsm_place->save();
            return $this->responseSuccess($dsm_place->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
