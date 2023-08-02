<?php

namespace App\Kio\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportDowntimeCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = ReportDowntimeCodeModel::all();
            return $this->responseSuccess($data, 'Loaded!');
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
            $ret_val= ReportModel::create(array([
                'id_report' => $request->id_report,
                'id_code' => $request->id_code
            ]));
            return $this->responseSuccess($ret_val, 'Updated successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kio\Reports\ReportDowntimeCodeModel  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $report = ReportDowntimeCodeModel::find($id);
            return $this->responseSuccess($report, 'Loaded!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Reports\ReportDowntimeCodeModel  $id
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
     * @param  \App\Kio\Reports\ReportDowntimeCodeModel  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $report_downtime_code = ReportModel::find($id);
            $report_downtime_code->id_report = $request->id_report;
            $report_downtime_code->id_code = $request->id_code;
            $report_downtime_code->save();
            return $this->responseSuccess($report_downtime_code, 'Updated successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Reports\ReportDowntimeCodeModel  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $report_downtime_code = ReportDowntimeCodeModel::find($id);
            return $this->responseSuccess($report_downtime_code->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
