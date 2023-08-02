<?php

namespace App\Kio\Dsms\PlansFacts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DsmFactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return  $this->responseSuccess(DsmFactModel::all(), 'Successful!');
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
        DB::beginTransaction();
        try {
            $day = new Carbon($request->date_fact);
            $end_of_month = new Carbon($request->date_fact); $end_of_month->endOfMonth();
            $start_of_month = new Carbon($request->date_fact); $start_of_month->startOfMonth();
            $start_of_year = new Carbon($request->date_fact); $start_of_year->startOfYear();

            $before_day_facts = DsmFactModel::where('id_dsm', $request->id_dsm)
                ->where('id_place', $request->id_place)
                ->where('id_place_dsm', $request->id_place_dsm)
                ->where('date_fact', '<', $day)->where('date_fact', '>=', $start_of_year)->orderByDesc('date_fact')->first(['fact_month', 'fact_year', 'date_fact']);

            if(!empty($before_day_facts->date_fact)){
                if($start_of_month->lt($day) && $start_of_month->lte(new Carbon($before_day_facts->date_fact))){
                    $fact_month = $before_day_facts->fact_month + $request->fact;
                }else{
                    $fact_month = $request->fact;
                }
                if($start_of_year->lt($day)){
                    $fact_year = $before_day_facts->fact_year + $request->fact;
                }else{
                    $fact_year = $request->fact;
                }
            }else{
                $fact_month = $request->fact;
                $fact_year = $request->fact;
            }

            DsmFactModel::updateOrCreate(
                ['id_dsm' => $request->id_dsm, 'id_place' => $request->id_place, 'id_place_dsm' =>$request->id_place_dsm, 'date_fact' => $request->date_fact],
                ['fact' => $request->fact, 'fact_month' => $fact_month, 'fact_year' => $fact_year, 'creator_id' => $request->user()->id]
            );

            $update_dates = DsmFactModel::where('id_dsm', $request->id_dsm)
                ->where('id_place', $request->id_place)
                ->where('id_place_dsm', $request->id_place_dsm)
                ->where('date_fact', '>', $request->date_fact)->orderBy('date_fact')->get();

            foreach ($update_dates as $update_date){
                if($end_of_month->gte(new Carbon($update_date->date_fact))){
                    $fact_month += $update_date->fact;
                    $update_date->fact_month = $fact_month;
                }
                $fact_year += $update_date->fact;
                $update_date->fact_year = $fact_year;
                $update_date->updater_id = $request->user()->id;
                $update_date->save();
            }
            DB::commit();
            return  $this->responseSuccess($update_dates,'Created!');
        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kio\Dsms\PlansFacts\DsmFactModel  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $dsm_fact = DsmFactModel::find($id);
            return  $this->responseSuccess($dsm_fact, 'Updated!');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Dsms\PlansFacts\DsmFactModel  $id
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
     * @param  \App\Kio\Dsms\PlansFacts\DsmFactModel  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $dsm_fact = DsmFactModel::find($id);
            return  $this->responseSuccess($dsm_fact, 'Updated!');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Dsms\PlansFacts\DsmFactModel  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $dsm_fact = DsmFactModel::find($id);
            $dsm_fact->deleter_id = $request->user()->id;
            $dsm_fact->save();
            return  $this->responseSuccess($dsm_fact->delete(), 'Deleted!');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
