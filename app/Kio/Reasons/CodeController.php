<?php

namespace App\Kio\Reasons;

use App\Http\Controllers\Controller;
use App\Kio\Technicas\TechnicaGroupModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $per_page = isset($request->per_page) ? intval($request->per_page) : 5;
            $keyword = isset($request->search) ? $request->search : null;
            $technica_type = isset($request->technica_type) ? $request->technica_type : null;
            $technica_group = isset($request->technica_group) ? $request->technica_group : null;
            $id_reason = isset($request->id_reason) ? $request->id_reason : null;
            $data = CodeModel::select('codes.*', 'reasons.reason', 'technica_groups.name as technica_group_name')->when($keyword, function ($query) use ($keyword){
                $query->where(function ($query) use ($keyword){
                    $query->where('codes.title', 'like', '%'.$keyword.'%')->orWhere('codes.code', 'like', '%'.$keyword.'%');
                });
            })->when($technica_type, function ($query) use ($technica_type){
                $query->whereIn('codes.id_technica_group', TechnicaGroupModel::select(['id'])->where('id_technica_type', '=', $technica_type)->pluck('id'));
            })->when($technica_group, function ($query) use ($technica_group){
                $query->where('codes.id_technica_group', '=', $technica_group);
            })->when($id_reason, function ($query) use ($id_reason){
                $query->where('codes.id_reason', '=', $id_reason);
            })->join('reasons', function ($join){
                $join->on('codes.id_reason', '=', 'reasons.id')->whereNull('reasons.deleted_at');
            })->join('technica_groups', function ($join){
                $join->on('codes.id_technica_group', '=', 'technica_groups.id')->whereNull('technica_groups.deleted_at');
            })->orderBy('codes.id_technica_group')->orderBy('code')->paginate($per_page);
            return $this->responseSuccessPaginate($data, 'All!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_for_select(Request $request)
    {
        try {
            $keyword = isset($request->search) ? $request->search : null;
            $technica_group = isset($request->id_technica_group) ? $request->id_technica_group : null;
            $data = CodeModel::select('codes.*', 'reasons.reason', 'reasons.type as reason_type', 'technica_groups.name as technica_group_name')->when($keyword, function ($query) use ($keyword){
                $query->where(function ($query) use ($keyword){
                    $query->where('codes.title', 'like', '%'.$keyword.'%')->orWhere('codes.code', 'like', '%'.$keyword.'%')->orWhere('reasons.reason', 'like', '%'.$keyword.'%');
                });
            })->when($technica_group, function ($query) use ($technica_group){
                $query->where('codes.id_technica_group', '=', $technica_group);
            })->join('reasons', function ($join){
                $join->on('codes.id_reason', '=', 'reasons.id')->whereNull('reasons.deleted_at');
            })->join('technica_groups', function ($join){
                $join->on('codes.id_technica_group', '=', 'technica_groups.id')->whereNull('technica_groups.deleted_at');
            })->orderBy('codes.id_technica_group')->orderBy('code')->get();
            return $this->responseSuccess($data, 'All!');
        } catch (\Exception $e) {
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
            $ret_val = CodeModel::create([
                'id_reason' => $request->id_reason,
                'id_technica_group' => $request->id_technica_group,
                'code' => $request->code,
                'title' => $request->title,
                'creator_id' => $request->user()->id
            ]);
            return $this->responseSuccess($ret_val, 'Updated successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kio\Reasons\CodeModel  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $code = CodeModel::find($id);
            return $this->responseSuccess($code, 'Updated successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Reasons\CodeModel  $id
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
     * @param  \App\Kio\Reasons\CodeModel $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $code = CodeModel::find($id);
            $code->id_reason = $request->id_reason;
            $code->id_technica_group = $request->id_technica_group;
            $code->code = $request->code;
            $code->title = $request->title;
            $code->updater_id = $request->user()->id;
            $code->save();
            return $this->responseSuccess($code, 'Updated successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Reasons\CodeModel $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $code = CodeModel::find($id);
            $code->deleter_id = $request->user()->id;
            $code->save();
            return $this->responseSuccess($code->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
