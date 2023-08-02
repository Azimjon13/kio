<?php

namespace App\Kio\Locomotives;

use App\Http\Controllers\Controller;
use App\Kio\Locomotives\Downtimes\LocomotiveDowntimeModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LocomotiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $per_page = isset($request->per_page) ? intval($request->per_page) : 10;
            $keyword = isset($request->search) ? $request->search : null;
            $serial_number = isset($request->serial_number) ? $request->serial_number : null;
            $place = isset($request->place) ? $request->place : null;
            $data = LocomotiveModel::whereHas('latest_locomotive_place')
                ->whereHas('latest_locomotive_place.place', function($query) use ($place){
                $query->when($place, function ($query) use ($place){
                    $query->where('title','like', '%'.$place.'%');
                });
            })->with(['latest_locomotive_place',
                'latest_locomotive_place.place' => function($query) use ($place){
                    $query->select('id','title');
                    $query->when($place, function ($query) use ($place){
                        $query->where('title','like', '%'.$place.'%');
                    });
                }
            ])->when($keyword, function ($query) use ($keyword){
                $query->where('title', 'like', '%'.$keyword.'%');
            })->when($serial_number, function ($query) use ($serial_number){
                $query->where('serial_number', 'like', '%'.$serial_number.'%');
            })->paginate($per_page);
            return $this->responseSuccessPaginate($data, 'success!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function search_by_group(Request $request)
    {
        try {
            $keyword = isset($request->search) ? $request->search : null;
            $id_group = isset($request->id_group) ? $request->id_group : null;
            $id_place = isset($request->id_place) ? $request->id_place : null;
            $date = isset($request->date) ? $request->date : null;
            $data = LocomotiveModel::select('locomotives.*', 'places_locomotives.*')
                ->join('places_locomotives', function ($join){
                    $join->on('places_locomotives.id_locomotive', '=', 'locomotives.id')
                    ->whereNull('places_locomotives.deleted_at');
                })
                ->when($keyword, function ($query) use ($keyword){
                    $query->where(function ($query) use ($keyword){
                        $query->where('locomotives.title', 'like', '%'.$keyword.'%')
                            ->orWhere('locomotives.serial_number', 'like', '%'.$keyword.'%')
                            ->orWhere('locomotives.id', '=', $keyword);
                    });
                })->where('locomotives.id_group', '=', $id_group)->where('places_locomotives.id_place', '=',$id_place)
                ->whereRaw('? BETWEEN date_from AND COALESCE(date_to, ?)', [$date, $date])->get();
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
            $ret_val = LocomotiveModel::create([
                'title' => $request->title,
                'id_group' => $request->id_group,
                'serial_number' =>$request->serial_number,
                'status' => $request->status,
                'creator_id' =>$request->user()->id
            ]);

            LocomotivePlaceModel::create([
                'id_place' => $request->id_place,
                'id_locomotive'=> $ret_val->id,
                'date_from'=> $request->date_from,
                'date_to' => @$request->date_to,
                'creator_id' => $request->user()->id,
            ]);
            DB::commit();
            return $this->responseSuccess($ret_val, 'created successfully');
        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kio\Locomotives\LocomotiveModel  $locomotiveModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $locomotive = LocomotiveModel::find($id)->load(['locomotive_places.place:id,id_parent,id_manager,title','locomotive_places.place.parent:id,title', 'locomotive_places.place.manager:id,title']);
            return $this->responseSuccess($locomotive, 'success!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Locomotives\LocomotiveModel  $locomotiveModel
     * @return \Illuminate\Http\Response
     */
    public function edit(LocomotiveModel $locomotiveModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Locomotives\LocomotiveModel  $locomotiveModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $locomotive = LocomotiveModel::find($id);
            $locomotive->title = $request->title;
            $locomotive->id_group = $request->id_group;
            $locomotive->serial_number = $request->serial_number;
            $locomotive->status = $request->status;
            $locomotive->updater_id = $request->user()->id;
            $locomotive->save();

            $insert_places= [];
            foreach ($request->locomotive_places as $locomotive_place) {
                $dontime_exists = LocomotiveDowntimeModel::where('id_locomotive', '=', $locomotive->id)->where(function ($query) use ($locomotive_place){
                    $query->where('end_time', '>', $locomotive_place['date_from'])->orWhereNull('end_time');
                })->whereNot('id_place', '=', $locomotive_place['id_place'])->exists();
                if($dontime_exists){
                    return $this->responseError('В настоящее время транспорт находится в простое!', 'В настоящее время транспорт находится в простое!', Response::HTTP_BAD_REQUEST);
                }
                $insert_places[] = array(
                    'id' => @$locomotive_place['id'],
                    'id_locomotive' => $locomotive->id,
                    'id_place' => $locomotive_place['id_place'],
                    'date_from' => $locomotive_place['date_from'],
                    'date_to' => @$locomotive_place['date_to'],
                    'updater_id' => $request->user()->id
                );
            }
            $arr_size = count($insert_places);
            if($arr_size >= 2){
                $last = $insert_places[$arr_size - 1];
                $pre_last = $insert_places[$arr_size - 2];
                if($last['id_locomotive'] == $pre_last['id_locomotive'] && $last['id_place'] == $pre_last['id_place'] && !is_null($pre_last['date_to'])){
                    return $this->responseError('Транспорт на том же месте!', 'Транспорт на том же месте!', Response::HTTP_BAD_REQUEST);
                }
            }
            $locomotive->locomotive_places()->upsert($insert_places, ['id_locomotive', 'id_place', 'date_from', 'date_to', 'updater_id']);
            DB::commit();
            return $this->responseSuccess($locomotive, 'updated successfully');
        }catch (\Exception $e){
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Locomotives\LocomotiveModel  $locomotiveModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $locomotive = LocomotiveModel::find($id);
            $user_id = $request->user()->id;
            $locomotive->deleter_id = $user_id;
            $locomotive->save();
            foreach ($locomotive->locomotive_places as $locomotive_place){
                $locomotive_place->deleter_id = $user_id;
                $locomotive_place->save();
            }
            $locomotive->locomotive_places()->delete();
            DB::commit();
            return $this->responseSuccess($locomotive->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
