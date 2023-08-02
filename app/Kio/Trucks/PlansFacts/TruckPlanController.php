<?php

namespace App\Kio\Trucks\PlansFacts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TruckPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return  $this->responseSuccess(TruckPlanModel::all(), 'Successful!');
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
            $day = new Carbon($request->date_plan);
            $end_of_month = new Carbon($request->date_plan); $end_of_month->endOfMonth();
            $start_of_month = new Carbon($request->date_plan); $start_of_month->startOfMonth();
            $start_of_year = new Carbon($request->date_plan); $start_of_year->startOfYear();

            $before_day_plans = TruckPlanModel::where('id_truck', $request->id_truck)
                ->where('id_place', $request->id_place)
                ->where('id_place_truck', $request->id_place_truck)
                ->where('date_plan', '<', $day)->where('date_plan', '>=', $start_of_year)->orderByDesc('date_plan')->first(['plan_month', 'plan_year', 'date_plan']);
            if(!empty($before_day_plans->date_plan)){
                if($start_of_month->lt($day) && $start_of_month->lte(new Carbon($before_day_plans->date_plan))){
                    $plan_month = $before_day_plans->plan_month + $request->plan;
                }else{
                    $plan_month = $request->plan;
                }
                if($start_of_year->lt($day)){
                    $plan_year = $before_day_plans->plan_year + $request->plan;
                }else{
                    $plan_year = $request->plan;
                }
            }else{
                $plan_month = $request->plan;
                $plan_year = $request->plan;
            }

            $insert_data = [];
            while($end_of_month->gte($day)){
                $insert_data[] = array(
                    'id_truck' => $request->id_truck,
                    'id_place' => $request->id_place,
                    'id_place_truck' =>$request->id_place_truck,
                    'plan' => $request->plan,
                    'date_plan' => new Carbon($day),
                    'plan_month' => $plan_month,
                    'plan_year' => $plan_year,
                    'creator_id' => $request->user()->id
                );
                $plan_month += $request->plan;
                $plan_year +=$request->plan;
                $day->addDay();
            }
            TruckPlanModel::where('id_truck', $request->id_truck)
                ->where('id_place', $request->id_place)
                ->where('id_place_truck', $request->id_place_truck)
                ->whereBetween('date_plan', [$request->date_plan, $end_of_month])->forceDelete();
            TruckPlanModel::insert($insert_data);


            $end_of_year = new Carbon($end_of_month); $end_of_year->endOfYear();

            $update_year_data = TruckPlanModel::where('id_truck', $request->id_truck)
                ->where('id_place', $request->id_place)
                ->where('id_place_truck', $request->id_place_truck)
                ->whereBetween('date_plan', [$end_of_month, $end_of_year])->orderBy('date_plan')->get();
            $change = 0;
            $plan_year -=$request->plan;
            foreach ($update_year_data as $key => $update_data){
                if($key==0){
                    $change = $update_data->plan_year - $update_data->plan_month - $plan_year;
                }
                $update_data->plan_year -= $change;
                $update_data->updater_id = $request->user()->id;
                $update_data->save();
            }
            DB::commit();
            return  $this->responseSuccess($end_of_year,'Inserted!');
        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kio\Trucks\PlansFacts\TruckPlanModel  $truckPlanModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $truck_plan = TruckPlanModel::find($id);
        return  $this->responseSuccess($truck_plan, 'Updated!');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Trucks\PlansFacts\TruckPlanModel  $truckPlanModel
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
     * @param  \App\Kio\Trucks\PlansFacts\TruckPlanModel  $truckPlanModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $truck_plan = TruckPlanModel::find($id);
            return  $this->responseSuccess($truck_plan, 'Updated!');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Trucks\PlansFacts\TruckPlanModel  $truckPlanModel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $truck_plan = TruckPlanModel::find($id);
            $end_of_month = new Carbon($truck_plan->date_plan); $end_of_month->endOfMonth();
            TruckPlanModel::where('id_truck', $truck_plan->id_truck)
                ->where('id_place', $truck_plan->id_place)
                ->where('id_place_truck', $truck_plan->id_place_truck)
                ->whereBetween('date_plan', [$truck_plan->date_plan, $end_of_month])->forceDelete();
            return  $this->responseSuccess($truck_plan, 'Deleted!');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
