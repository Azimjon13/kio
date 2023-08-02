<?php

namespace App\Kio\Cpts;

use App\Http\Controllers\Controller;
use App\Kio\Cpts\Downtimes\CptDowntimeModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CptPlaceController extends Controller
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
            $dontime_exists = CptDowntimeModel::where('id_cpt', '=', $request->id_cpt)->where(function ($query) use ($request){
                $query->where('end_time', '>', $request->date_from)->orWhereNull('end_time');
            })->whereNot('id_place', '=', $request->id_place)->exists();
            if($dontime_exists){
                return $this->responseError(null, 'В настоящее время транспорт находится в простое!', Response::HTTP_BAD_REQUEST);
            }else{
                $ret_val =  CptPlaceModel::create([
                    'id_place' => $request->id_place,
                    'id_cpt'=> $request->id_cpt,
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
     * @param  \App\Kio\Cpts\CptPlaceModel  $cptDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $cptPlace = CptPlaceModel::find($id);
            return $this->responseSuccess($cptPlace, 'success!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Cpts\CptPlaceModel  $cptDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function edit(CptPlaceModel $cptDowntimeModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Cpts\CptPlaceModel  $cptDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $cptPlace = CptPlaceModel::find($id);
            $cptPlace->id_cpt = $request->id_cpt;
            $cptPlace->id_place = $request->id_place;
            $cptPlace->date_from = $request->date_from;
            $cptPlace->date_to = $request->date_to;
            $cptPlace->updater_id = $request->user()->id;
            $cptPlace->save();
            return $this->responseSuccess($cptPlace, 'updated successfully');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Cpts\CptPlaceModel  $cptDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $cpt_place = CptPlaceModel::find($id);
            $cpt_place->deleter_id = $request->user()->id;
            $cpt_place->save();
            return $this->responseSuccess($cpt_place->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
