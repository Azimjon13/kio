<?php

namespace App\Kio\Places;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $place = PlaceModel::find(1);
            $data = [
                'parent' => $place,
                'places' => $place->places()->get()
            ];
            return $this->responseSuccess($data, 'Parent Default: '.$place->id);
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
        try {
            $ret_val = PlaceModel::create([
                'id_manager' => $request->id_manager,
                'id_parent' => @$request->id_parent,
                'title' => $request->title,
                'excavator' => $request->excavator,
                'drill' => $request->drill,
                'cpt' => $request->cpt,
                'truck' => $request->truck,
                'dsm' => $request->dsm,
                'locomotive' => $request->locomotive,
                'sort' => $request->sort,
            ]);
            PlaceModel::where('id_manager', '=', $ret_val->id_manager)->where('sort', '>=', $ret_val->sort)->whereNot('id', '=', $ret_val->id)->increment('sort');
            return $this->responseSuccess($ret_val, 'created successfully');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kio\Places\PlaceModel  $placeModel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $place = PlaceModel::find($id);
            $data = [
                'parent' => $place,
                'places' => $place->places()->get()
            ];
            return $this->responseSuccess($data, 'success!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function place_by_id($id)
    {
        try {
            $place = PlaceModel::select('places.title as place_title', 'p.title as parent_title', 'm.title as manager_title')->where('places.id', '=', $id)
                ->leftJoin('places as p', 'places.id_parent', '=', 'p.id')
                ->leftJoin('managers as m', 'places.id_manager', '=', 'm.id')->first();
//            $data = [
//                'parent' => $place,
//                'places' => $place->places()->get()
//            ];
            return $this->responseSuccess($place, 'success!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kio\Places\PlaceModel  $placeModel
     * @return \Illuminate\Http\Response
     */
    public function edit(PlaceModel $placeModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kio\Places\PlaceModel  $placeModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {
            $place = PlaceModel::find($id);
            $place->title = $request->title;
            $place->excavator = $request->excavator;
            $place->drill = $request->drill;
            $place->cpt = $request->cpt;
            $place->truck = $request->truck;
            $place->dsm = $request->dsm;
            $place->locomotive = $request->locomotive;
            $place->sort = $request->sort;
            $place->save();
            PlaceModel::where('id_manager', '=', $place->id_manager)->where('sort', '>=', $place->sort)->whereNot('id', '=', $place->id)->increment('sort');
            return $this->responseSuccess($place, 'updated successfully');
        }catch (\Exception $e){
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kio\Places\PlaceModel  $placeModel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $place = PlaceModel::find($id);
            PlaceModel::where('id_manager', '=', $place->id_manager)->where('sort', '>', $place->sort)->decrement('sort');
            return $this->responseSuccess($place->delete(), 'deleted id: '.$id);
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function change_order($id, $sort){
        $ret_val = PlaceModel::where('id', '=', $id)->update(['sort' => $sort]);
        PlaceModel::where('id_manager', '=', $ret_val->id_manager)->where('sort', '>=', $ret_val->sort)->whereNot('id', '=', $ret_val->id)->increment('sort');
    }
}
