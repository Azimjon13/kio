<?php

namespace App\Kio\Managers;

use App\Http\Controllers\Controller;
use App\Laravue\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = ManagerModel::with(['places'=> function ($query){
                $query->whereNull('id_parent')->with('places');
            }])->get();
            return $this->responseSuccess($data, 'success!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function all_list()
    {
        try {
            $data = ManagerModel::all();
            return $this->responseSuccess($data, 'success!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function roles_list()
    {
        try {
            $data = Role::select(['id','name'])->where('id', '>', 2)->get();
            return $this->responseSuccess($data, 'success!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function places_by_manager($manager_id, $aggregate=null)
    {
        try {
            $data = ManagerModel::where('id', '=', $manager_id)->with(['places'=> function ($query) use ($aggregate){
                $query->whereNull('id_parent')->whereHas('places', function ($q) use ($aggregate){
                    $q->when(!Auth::user()->isAdmin(),function ($q){
                        $q->whereIn('id', Auth::user()->user_places()->pluck('id_place')->toArray());
                    });
                    $q->when($aggregate=='excavator', function ($q){
                        $q->where('excavator', '=', '1');
                    })->when($aggregate=='drill', function ($q){
                        $q->where('drill', '=', '1');
                    })->when($aggregate=='cpt', function ($q){
                        $q->where('cpt', '=', '1');
                    })->when($aggregate=='truck', function ($q){
                        $q->where('truck', '=', '1');
                    })->when($aggregate=='dsm', function ($q){
                        $q->where('dsm', '=', '1');
                    })->when($aggregate=='locomotive', function ($q){
                        $q->where('locomotive', '=', '1');
                    });
                })->with(['places' => function($q) use ($aggregate){
                    $q->when(!Auth::user()->isAdmin(), function ($q){
                        $q->whereIn('id', Auth::user()->user_places()->pluck('id_place')->toArray());
                    });
                    $q->when($aggregate=='excavator', function ($q){
                        $q->where('excavator', '=', '1');
                    })->when($aggregate=='drill', function ($q){
                        $q->where('drill', '=', '1');
                    })->when($aggregate=='cpt', function ($q){
                        $q->where('cpt', '=', '1');
                    })->when($aggregate=='truck', function ($q){
                        $q->where('truck', '=', '1');
                    })->when($aggregate=='dsm', function ($q){
                        $q->where('dsm', '=', '1');
                    })->when($aggregate=='locomotive', function ($q){
                        $q->where('locomotive', '=', '1');
                    });
                }]);
            }])->first();
            return $this->responseSuccess($data, 'success!');
        }catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function places_list_by_manager()
    {
        try {
            $data = ManagerModel::with(['places'=> function ($query){
                $query->whereNull('id_parent')->with('places');
            }])->get();
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
//        try {
//            $ret_val = AccidentModel::create([
//                'creator_id' => $request->user()->id,
//            ]);
//            return $this->responseSuccess($ret_val, 'created successfully');
//        }catch (\Exception $e){
//            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
//        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kio\Managers\ManagerModel  $managerModel
     * @return \Illuminate\Http\Response
     */
    public function show(ManagerModel $managerModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Managers\ManagerModel  $managerModel
     * @return \Illuminate\Http\Response
     */
    public function edit(ManagerModel $managerModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Managers\ManagerModel  $managerModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ManagerModel $managerModel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Managers\ManagerModel  $managerModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(ManagerModel $managerModel)
    {
        //
    }
}
