<?php

namespace App\Kio\Places\Shifts;

use App\Http\Controllers\Controller;
use App\Kio\Places\Shifts\PlaceShiftModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlaceShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = PlaceShiftModel::all();
            return $this->responseSuccess($data, 'All');
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
            $ret_val = PlaceShiftModel::create([
                'id_place' => $request->id_place,
                'id_technica_type' => @$request->id_technica_type,
                'difference' => $request->difference,
                'duration' => $request->duration,
                'shift' => $request->shift,
            ]);
            return $this->responseSuccess($ret_val, 'created successfully');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kio\Places\PlaceShiftModel  $place_shiftModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $place_shift = PlaceShiftModel::find($id);
            return $this->responseSuccess($place_shift, 'success!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Places\PlaceShiftModel  $place_shiftModel
     * @return \Illuminate\Http\Response
     */
    public function edit(PlaceShiftModel $place_shiftModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Places\PlaceShiftModel  $place_shiftModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {
            $place_shift = PlaceShiftModel::find($id);
            $place_shift->id_place = $request->id_place;
            $place_shift->id_technica_type = $request->id_technica_type;
            $place_shift->difference = $request->difference;
            $place_shift->duration = $request->duration;
            $place_shift->shift = $request->shift;
            $place_shift->save();
            return $this->responseSuccess($place_shift, 'updated successfully');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Places\PlaceShiftModel  $place_shiftModel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            return $this->responseSuccess(PlaceShiftModel::destroy($id), 'deleted id: '.$id);
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
