<?php

namespace App\Kio\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
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
            $id_technica_type = isset($request->id_technica_type) ? $request->id_technica_type : null;
            $id_place = isset($request->id_place) ? $request->id_place : null;
            $data = ReportModel::select('reports.*', 'tt.title as technica_type', 'p.title as place', 'p.id_parent', 'p.id_manager')
                ->when($keyword, function ($query) use ($keyword){
                    $query->where('description', 'like', '%'.$keyword.'%');
                })->when($id_place, function ($query) use ($id_place){
                    $query->where('id_place', '=', $id_place);
                })
                ->when($id_technica_type, function ($query) use ($id_technica_type){
                    $query->where('id_technica_type', '=', $id_technica_type);
                })
                ->join('technica_types as tt', function ($join){
                    $join->on('tt.id', '=', 'reports.id_technica_type')->whereNull('tt.deleted_at');
                })->join('places as p', function ($join){
                    $join->on('p.id', '=', 'reports.id_place')->whereNull('p.deleted_at');
                })->paginate($per_page);
            return $this->responseSuccessPaginate($data, 'success!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function reports_without_codes(Request $request)
    {
        try {
            $id_place = $request->id_place;
            $id_technica_type = $request->id_technica_type;
            $data = ReportModel::select('reports.*', 'tt.title as technica_type', 'p.title as place', 'p.id_parent', 'p.id_manager')
                ->where('id_place', '=', $id_place)
                ->where('id_technica_type', '=', $id_technica_type)
                ->join('technica_types as tt', function ($join){
                    $join->on('tt.id', '=', 'reports.id_technica_type')->whereNull('tt.deleted_at');
                })->join('places as p', function ($join){
                    $join->on('p.id', '=', 'reports.id_place')->whereNull('p.deleted_at');
                })->get();
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
        DB::beginTransaction();
        try {
            $ret_val = ReportModel::create(array(
                'id_place' => $request->id_place,
                'id_technica_type' => $request->id_technica_type,
                'description' => $request->description,
                'creator_id' => $request->user()->id,
            ));
            $insert_data = [];
            foreach ($request->codes as $code){
                $insert_data[] = array(
                   'id_report' => $ret_val->id,
                   'id_code' => $code
                );
            }
            $ret_val->downtime_codes()->delete();
            $ret_val->downtime_codes()->createMany($insert_data);
            DB::commit();
            return $this->responseSuccess($ret_val, 'success!');
        }catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kio\Reports\ReportModel  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $report = ReportModel::select('reports.*', 'tt.title as technica_type', 'p.title as place', 'p.id_parent', 'p.id_manager')
                ->where('reports.id', '=', $id)
                ->join('technica_types as tt', function ($join){
                    $join->on('tt.id', '=', 'reports.id_technica_type')->whereNull('tt.deleted_at');
                })->join('places as p', function ($join){
                    $join->on('p.id', '=', 'reports.id_place')->whereNull('p.deleted_at');
                })->with('downtime_codes')->first();;
            return $this->responseSuccess($report, 'Loaded!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Reports\ReportModel  $id
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
     * @param  \App\Kio\Reports\ReportModel  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $report = ReportModel::find($id);
            $report->id_place = $request->id_place;
            $report->id_technica_type = $request->id_technica_type;
            $report->description = $request->description;
            $report->updater_id = $request->user()->id;
            $report->save();

            $insert_data = [];
            foreach ($request->codes as $code){
                $insert_data[] = array(
                    'id_report' => $report->id,
                    'id_code' => $code
                );
            }
            $report->downtime_codes()->delete();
            $report->downtime_codes()->createMany($insert_data);
            DB::commit();
            return $this->responseSuccess($report, 'Updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Reports\ReportModel  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $report = ReportModel::find($id);
            $report->deleter_id = $request->user()->id;
            $report->save();
            return $this->responseSuccess($report->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
