<?php

namespace App\Kio\Technicas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TechnicaTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return $this->responseSuccess(TechnicaTypeModel::all(), 'success');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function grouplist($id=null){
        try {
            return $this->responseSuccess(TechnicaGroupModel::when($id, function ($query) use ($id){
                $query->where('id_technica_type', '=', $id);
            })->get(), 'success');
        }catch (\Exception $e){
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kio\Technicas\TechnicaModel  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = TechnicaTypeModel::find($id)->load('technica_groups');
            return $this->responseSuccess($data, 'success');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Technicas\TechnicaModel  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Technicas\TechnicaModel  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Technicas\TechnicaModel  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            return $this->responseSuccess(TechnicaTypeModel::destroy($id), 'success');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
