<?php

namespace App\Kio\Reasons;

use App\Http\Controllers\Controller;
use App\Kio\Technicas\TechnicaGroupModel;
use App\Kio\Technicas\TechnicaTypeModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReasonController extends Controller
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
            $type = isset($request->type) ? $request->type : null;
            $technica_type = isset($request->technica_type) ? $request->technica_type : null;
            $data = ReasonModel::when($keyword, function ($query) use ($keyword){
                $query->where(function ($query) use ($keyword){
                    $query->where('reason', 'like', '%'.$keyword.'%')->orWhere('id', '=', $keyword);
                });
            })->when($type, function ($query) use ($type){
                $query->where('type', '=', $type);
            })->when($technica_type=='excavator', function ($query){
                $query->where('excavator', '=', '1');
            })->when($technica_type=='drill', function ($query){
                $query->where('drill', '=', '1');
            })->when($technica_type=='cpt', function ($query){
                $query->where('cpt', '=', '1');
            })->when($technica_type=='truck', function ($query){
                $query->where('truck', '=', '1');
            })->when($technica_type=='dsm', function ($query){
                $query->where('dsm', '=', '1');
            })->when($technica_type=='locomotive', function ($query){
                $query->where('locomotive', '=', '1');
            })->orderBy('type')->paginate($per_page);
            return $this->responseSuccessPaginate($data, 'All!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_with_codes_grouped($technica_type)
    {
        try {
            $technica_groups = TechnicaGroupModel::select(['id'])->whereHas('technica_type', function ($query) use ($technica_type){
                $query->where('aggregate', '=',  $technica_type);
            })->get()->pluck('id')->toArray();
            $data = ReasonModel::select(['id', 'type', 'reason', 'is_reported'])->with(['codes'=>function($query) use ($technica_groups){
                $query->select('codes.*','technica_groups.name as title_technica_group')
                    ->join('technica_groups', function ($join) use ($technica_groups){
                    $join->on('codes.id_technica_group', '=', 'technica_groups.id')
                        ->whereIn('codes.id_technica_group', $technica_groups)->whereNull('codes.deleted_at');
                });
            }])->when($technica_type=='excavator', function ($query){
                $query->where('excavator', '=', '1');
            })->when($technica_type=='drill', function ($query){
                $query->where('drill', '=', '1');
            })->when($technica_type=='cpt', function ($query){
                $query->where('cpt', '=', '1');
            })->when($technica_type=='truck', function ($query){
                $query->where('truck', '=', '1');
            })->when($technica_type=='dsm', function ($query){
                $query->where('dsm', '=', '1');
            })->when($technica_type=='locomotive', function ($query){
                $query->where('locomotive', '=', '1');
            })->orderBy('type')->get();
            return $this->responseSuccess($data, 'All!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_for_select_by_technica_type($id_technica_type)
    {
        try {
            $technica_type = TechnicaTypeModel::find($id_technica_type)->aggregate;
            $data = ReasonModel::select(['id','type','reason'])
            ->when($technica_type=='excavator', function ($query){
                $query->where('excavator', '=', '1');
            })->when($technica_type=='drill', function ($query){
                $query->where('drill', '=', '1');
            })->when($technica_type=='cpt', function ($query){
                $query->where('cpt', '=', '1');
            })->when($technica_type=='truck', function ($query){
                $query->where('truck', '=', '1');
            })->when($technica_type=='dsm', function ($query){
                $query->where('dsm', '=', '1');
            })->when($technica_type=='locomotive', function ($query){
                $query->where('locomotive', '=', '1');
            })->orderBy('type')->get();
            return $this->responseSuccess($data, 'All!');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_for_select(Request $request)
    {
        try {
            $type = isset($request->type) ? $request->type : null;
            $technica_type = isset($request->technica_type) ? $request->technica_type : null;
            $data = ReasonModel::select(['id','type','reason'])->when($type, function ($query) use ($type){
                $query->where('type', '=', $type);
            })->when($technica_type=='excavator', function ($query){
                $query->where('excavator', '=', '1');
            })->when($technica_type=='drill', function ($query){
                $query->where('drill', '=', '1');
            })->when($technica_type=='cpt', function ($query){
                $query->where('cpt', '=', '1');
            })->when($technica_type=='truck', function ($query){
                $query->where('truck', '=', '1');
            })->when($technica_type=='dsm', function ($query){
                $query->where('dsm', '=', '1');
            })->when($technica_type=='locomotive', function ($query){
                $query->where('locomotive', '=', '1');
            })->orderBy('type')->get();
            return $this->responseSuccess($data, 'All!');
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $ret_val = ReasonModel::create([
                'type' => $request->type,
                'reason' => $request->reason,
                'is_reported' => $request->is_reported,
                'excavator' => $request->excavator,
                'drill' => $request->drill,
                'cpt' => $request->cpt,
                'truck' => $request->truck,
                'dsm' => $request->dsm,
                'locomotive' => $request->locomotive,
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
     * @param  \App\Kio\Reasons\ReasonModel $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $reason = ReasonModel::find($id);
            return $this->responseSuccess($reason, 'Loaded!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Reasons\ReasonModel $id
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
     * @param  \App\Kio\Reasons\ReasonModel $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $reason = ReasonModel::find($id);
            $reason->type = $request->type;
            $reason->reason = $request->reason;
            $reason->is_reported = $request->is_reported;
            $reason->excavator = $request->excavator;
            $reason->drill = $request->drill;
            $reason->cpt = $request->cpt;
            $reason->truck = $request->truck;
            $reason->dsm = $request->dsm;
            $reason->locomotive = $request->locomotive;
            $reason->updater_id = $request->user()->id;
            $reason->save();
            return $this->responseSuccess($reason, 'Updated successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Reasons\$id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $reason = ReasonModel::find($id);
            $reason->deleter_id = $request->user()->id;
            $reason->save();
            return $this->responseSuccess($reason->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
