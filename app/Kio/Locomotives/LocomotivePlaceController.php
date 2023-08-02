<?php

namespace App\Kio\Locomotives;

use App\Http\Controllers\Controller;
use App\Kio\Locomotives\Downtimes\LocomotiveDowntimeModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LocomotivePlaceController extends Controller
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
            $dontime_exists = LocomotiveDowntimeModel::where('id_locomotive', '=', $request->id_locomotive)->where(function ($query) use ($request){
                $query->where('end_time', '>', $request->date_from)->orWhereNull('end_time');
            })->whereNot('id_place', '=', $request->id_place)->exists();
            if($dontime_exists){
                return $this->responseError(null, 'В настоящее время транспорт находится в простое!', Response::HTTP_BAD_REQUEST);
            }else{
                $ret_val =  LocomotivePlaceModel::create([
                    'id_place' => $request->id_place,
                    'id_locomotive'=> $request->id_locomotive,
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
     * @param  \App\Kio\Locomotives\LocomotivePlaceModel  $locomotiveDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $locomotivePlace = LocomotivePlaceModel::find($id);
            return $this->responseSuccess($locomotivePlace, 'success!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Locomotives\LocomotivePlaceModel  $locomotiveDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function edit(LocomotivePlaceModel $locomotiveDowntimeModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Locomotives\LocomotivePlaceModel  $locomotiveDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $locomotivePlace = LocomotivePlaceModel::find($id);
            $locomotivePlace->id_locomotive = $request->id_locomotive;
            $locomotivePlace->id_place = $request->id_place;
            $locomotivePlace->date_from = $request->date_from;
            $locomotivePlace->date_to = $request->date_to;
            $locomotivePlace->updater_id = $request->user()->id;
            $locomotivePlace->save();
            return $this->responseSuccess($locomotivePlace, 'updated successfully');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Locomotives\LocomotivePlaceModel  $locomotiveDowntimeModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $locomotive_place = LocomotivePlaceModel::find($id);
            $locomotive_place->deleter_id = $request->user()->id;
            $locomotive_place->save();
            return $this->responseSuccess($locomotive_place->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
