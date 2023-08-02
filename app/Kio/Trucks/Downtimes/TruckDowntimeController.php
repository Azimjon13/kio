<?php

namespace App\Kio\Trucks\Downtimes;

use App\Http\Controllers\Controller;
use App\Kio\Trucks\PlansFacts\TruckPlanModel;
use App\Kio\Trucks\PlansFacts\TruckFactModel;
use App\Kio\Places\Shifts\PlaceShiftModel;
use App\Kio\Reports\ReportModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TruckDowntimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $per_page = isset($request->per_page) ? intval($request->per_page) : 5;
            $keyword = isset($request->search) ? $request->search : null;
            $id_group = isset($request->id_group) ? $request->id_group : null;
            $id_place = isset($request->id_place) ? $request->id_place : null;
            $date = isset($request->date) ? $request->date : null;
            $data = TruckDowntimeModel::with(['place_truck' => function($query){
               $query->select('places_trucks.*', 'places.title as place', 'trucks.title as truck')
                   ->join('places', function ($join){
                       $join->on('places_trucks.id_place', '=', 'places.id')->whereNull('places.deleted_at');
                   })
                   ->join('trucks', function ($join){
                    $join->on('places_trucks.id_truck', '=', 'trucks.id')->whereNull('trucks.deleted_at');
                   });
            }])->paginate($per_page);
            return $this->responseSuccessPaginate($data, 'success!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_downtime_daily(Request $request){
        try {
            $date = $request->date;
            $id_place = $request->id_place;
            if(!isset($date) || !isset($id_place)){
                return $this->responseError('Required!', 'date and place are required', Response::HTTP_BAD_REQUEST);
            }
            $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 4)->orderBy('shift')->get();
            $start_difference = $shifts?->first()?->difference;
            $end_difference = $shifts?->last()?->difference + $shifts?->last()?->duration? : 1440 - $shifts?->first()?->difference;
            $shift_start = new Carbon($date); $shift_start->addMinutes($start_difference);
            $shift_end = new Carbon($date); $shift_end->addMinutes($end_difference);

            $data['shifts'] = array(
                'shift_start' => $shift_start,
                'shift_end' => $shift_end
            );
            $data['downtimes'] = TruckDowntimeModel::downtime_daily($date, $id_place);
            return $this->responseSuccess($data, 'success');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_downtime_plan_fact(Request $request){
        try {
            $date = $request->date;
            $id_place = $request->id_place;
            if(!isset($date) || !isset($id_place)){
                return $this->responseError('Required!', 'date and place are required', Response::HTTP_BAD_REQUEST);
            }
            $data['plans_facts'] = collect(TruckDowntimeModel::downtime_plan_fact($date, $id_place));

            $id_place_trucks = $data['plans_facts']->pluck('id_place_truck')->toArray();
            $data['downtimes'] = collect(TruckDowntimeModel::downtime_daily_by_plan_fact($date, $id_place, $id_place_trucks));

            $excluded_facts = collect(TruckFactModel::excluded_facts_inmonth_inyear($date, $id_place))->first();
            $excluded_plans = collect(TruckPlanModel::excluded_plans_inmonth_inyear($date, $id_place))->first();
            $data['main']['plans_facts'] = array_merge((array) $excluded_facts,(array) $excluded_plans);
            $data['main']['downtimes'] = collect(TruckDowntimeModel::downtime_exluded($date, $id_place));
            $data['main']['hours'] = collect(TruckDowntimeModel::downtime_exluded_hours($date, $id_place))->first();

            return $this->responseSuccess($data, 'success');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_downtime_plan_fact_by_report(Request $request){
        try {
            $date = $request->date;
            $id_report = $request->id_report;
            if(!isset($date) || !isset($id_report)){
                return $this->responseError('Required!', 'date and report are required', Response::HTTP_BAD_REQUEST);
            }
            $id_place = ReportModel::find($id_report)->id_place;

            $data['plans_facts'] = collect(TruckDowntimeModel::downtime_plan_fact($date, $id_place));

            $id_place_trucks = $data['plans_facts']->pluck('id_place_truck')->toArray();
            $data['downtimes'] = collect(TruckDowntimeModel::downtime_daily_by_plan_fact($date, $id_place, $id_place_trucks));
            $data['downtimes_by_report'] = collect(TruckDowntimeModel::downtime_daily_by_plan_fact_by_report($date, $id_report, $id_place, $id_place_trucks));

            $excluded_facts = collect(TruckFactModel::excluded_facts_inmonth_inyear($date, $id_place))->first();
            $excluded_plans = collect(TruckPlanModel::excluded_plans_inmonth_inyear($date, $id_place))->first();
            $data['main']['plans_facts'] = array_merge((array) $excluded_facts,(array) $excluded_plans);
            $data['main']['downtimes'] = collect(TruckDowntimeModel::downtime_exluded($date, $id_place));
            $data['main']['downtimes_by_report'] = collect(TruckDowntimeModel::downtime_exluded_by_report($date,$id_report, $id_place));
            $data['main']['hours'] = collect(TruckDowntimeModel::downtime_exluded_hours($date, $id_place))->first();

            return $this->responseSuccess($data, 'success');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_downtime_by_truck(Request $request){
        try {
            $date = $request->date;
            $id_place_truck = $request->id_place_truck;
            if(!isset($date) || !isset($id_place_truck)){
                return $this->responseError('Required!', 'date and id_place_truck are required', Response::HTTP_BAD_REQUEST);
            }

            $info = DB::table('places_trucks as pe')->select('pe.garage', 'pe.id_place', 'pe.id_truck', 'e.title as truck', 'p.title as place')
                ->join('trucks as e', function ($join){
                    $join->on('pe.id_truck', '=', 'e.id')->whereNull('e.deleted_at');
                })
                ->join('places as p', function ($join){
                    $join->on('pe.id_place', '=', 'p.id')->whereNull('p.deleted_at');
                })
                ->where('pe.id', '=', $id_place_truck)->first();
            $data['place'] = $info;
            $shifts = PlaceShiftModel::where('id_place', '=', $info->id_place)->where('id_technica_type', '=', 4)->orderBy('shift')->get();
            $start_difference = $shifts?->first()?->difference;
            $end_difference = $shifts?->last()?->difference + $shifts?->last()?->duration? : 1440 - $shifts?->first()?->difference;
            $shift_month_start = new Carbon($date); $shift_month_start->startOfMonth()->addMinutes($start_difference);
            $shift_end = new Carbon($date); $shift_end->addMinutes($end_difference);

            $data['shifts'] = array(
                'shift_month_start' => $shift_month_start,
                'shift_end' => $shift_end
            );

            $data['downtimes'] = collect(TruckDowntimeModel::downtime_by_truck($date, $id_place_truck));

            return $this->responseSuccess($data, 'success');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_downtime_by_reason(Request $request){
        try {
            $date = $request->date;
            $id_place = $request->id_place;
            $id_reason = $request->id_reason;
            if(!isset($date) || !isset($id_place) || !isset($id_reason)){
                return $this->responseError('Required!', 'date, id_place and id_reason are required', Response::HTTP_BAD_REQUEST);
            }

            $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 4)->orderBy('shift')->get();
            $start_difference = $shifts?->first()?->difference;
            $end_difference = $shifts?->last()?->difference + $shifts?->last()?->duration? : 1440 - $shifts?->first()?->difference;
            $shift_month_start = new Carbon($date); $shift_month_start->startOfMonth()->addMinutes($start_difference);
            $shift_end = new Carbon($date); $shift_end->addMinutes($end_difference);

            $data['shifts'] = array(
                'shift_month_start' => $shift_month_start,
                'shift_end' => $shift_end
            );

            $data['downtimes'] = collect(TruckDowntimeModel::downtime_by_reason($date, $id_place, $id_reason));

            return $this->responseSuccess($data, 'success');
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
            if($request->start_time >= $request->end_time && !is_null($request->end_time)){
                return $this->responseError('Время окончания меньше времени начала!', 'Время окончания меньше времени начала!', Response::HTTP_BAD_REQUEST);
            }
            $exists  = TruckDowntimeModel::where('id_place', '=', $request->id_place)
                ->where('id_truck', '=', $request->id_truck)
                ->where('id_place_truck', '=', $request->id_place_truck)
                ->where(function ($query) use ($request){
                    $query->where(function ($query) use ($request) {
                        $query->whereNull('end_time')->when(!is_null($request->end_time), function ($query) use ($request){
                            $query->where('start_time', '<', date('Y-m-d H:i:s', round($request->end_time / 1000)));
                            }
                        );
                    });
                    $query->orWhere(function ($query) use ($request){
                        $query->whereNotNull('end_time')
                            ->where(function($query) use ($request){
                                $query->when(!is_null($request->end_time), function ($query) use ($request){
                                    $query->where('start_time', '<', date('Y-m-d H:i:s', round($request->end_time / 1000)));
                                });
                                $query->where('end_time', '>', date('Y-m-d H:i:s', round($request->start_time / 1000)));
                            });
                    });
                })->join('codes', function ($join){
                    $join->on('downtimes_trucks.id_code', '=', 'codes.id')->whereNull('codes.deleted_at');
                })->join('reasons', function ($join){
                    $join->on('codes.id_reason', '=', 'reasons.id')->whereNull('reasons.deleted_at');
                })->exists();
                if($exists){
                    DB::rollBack();
                    return $this->responseError("В этом интервале уже есть время простоя!", 'В этом интервале уже есть время простоя!', Response::HTTP_BAD_REQUEST);

                }else{
                    $ret_val = TruckDowntimeModel::create([
                        'id_place' => $request->id_place,
                        'id_truck' => $request->id_truck,
                        'id_code' => $request->id_code,
                        'id_place_truck' => $request->id_place_truck,
                        'start_time' => date('Y-m-d H:i:s', round($request->start_time/1000)),
                        'end_time' => $request->end_time ? date('Y-m-d H:i:s', round($request->end_time/1000)) : null,
                        'details' => @$request->details,
                        'creator_id' => $request->user()->id
                    ]);
                    DB::commit();
                    return $this->responseSuccess($ret_val, 'created');
                }
        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kio\Trucks\Downtimes\TruckDowntimeModel  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = TruckDowntimeModel::where('downtimes_trucks.id', '=', $id)->with([
                'place_truck' => function($query){
                $query->select('places_trucks.*', 'trucks.title', 'trucks.id_group', 'trucks.status')
                    ->join('trucks', function ($join){
                        $join->on('places_trucks.id_truck', '=', 'trucks.id')->whereNull('trucks.deleted_at');
                    });
                },
                'code' =>function($query){
                $query->select('codes.*', 'reasons.type', 'reasons.reason', 'tg.id as id_technica_group', 'tg.name')
                    ->join('reasons', function ($join){
                        $join->on('codes.id_reason', '=', 'reasons.id')->whereNull('reasons.deleted_at');
                    })
                    ->join('technica_groups as tg', function ($join){
                        $join->on('codes.id_technica_group', '=', 'tg.id')->whereNull('tg.deleted_at');
                    });
                }
            ])->first();
            return $this->responseSuccess($data, 'success');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Trucks\Downtimes\TruckDowntimeModel  $id
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
     * @param  \App\Kio\Trucks\Downtimes\TruckDowntimeModel  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            if($request->start_time >= $request->end_time  && !is_null($request->end_time)){
                return $this->responseError('Ошибка!', 'Время окончания меньше времени начала!', Response::HTTP_BAD_REQUEST);
            }
            $truck_downtime = TruckDowntimeModel::find($id);
            $truck_downtime->id_place = $request->id_place;
            $truck_downtime->id_truck = $request->id_truck;
            $truck_downtime->id_code = $request->id_code;
            $truck_downtime->id_place_truck = $request->id_place_truck;
            $truck_downtime->start_time = date('Y-m-d H:i:s', round($request->start_time/1000));
            $truck_downtime->end_time = $request->end_time ? date('Y-m-d H:i:s', round($request->end_time/1000)) : null;
            $truck_downtime->details = $request->details;
            $truck_downtime->updater_id = $request->user()->id;
            $truck_downtime->save();
            return $this->responseSuccess($truck_downtime, 'updated');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Trucks\Downtimes\TruckDowntimeModel  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $truck_downtime = TruckDowntimeModel::find($id);
            $truck_downtime->deleter_id = $request->user()->id;
            $truck_downtime->save();
            return $this->responseSuccess($truck_downtime->delete(), 'deleted');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
