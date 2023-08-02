<?php

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Laravue\Faker;
use \App\Laravue\JsonResponse;
use \App\Laravue\Acl;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::namespace('App\Kio')->group(function(){
    Route::group(['prefix'=>'tests'], function(){
        // Api KIO resource routes for locomotives
        Route::apiResource('locomotives', 'Locomotives\LocomotiveController');
        Route::get('locomotivesbygroup', 'Locomotives\LocomotiveController@search_by_group');
        Route::apiResource('locomotiveplaces', 'Locomotives\LocomotivePlaceController');
        Route::apiResource('locomotiveplaces', 'Locomotives\LocomotivePlaceController');
        Route::apiResource('locomotivedowntimes', 'Locomotives\Downtimes\LocomotiveDowntimeController');
        Route::get('locomotivedowntimesdaily', 'Locomotives\Downtimes\LocomotiveDowntimeController@get_downtime_daily');
        Route::get('locomotiveplansfactsdowntimes', 'Locomotives\Downtimes\LocomotiveDowntimeController@get_downtime_plan_fact');
        Route::get('locomotiveplansfactsdowntimesbyreport', 'Locomotives\Downtimes\LocomotiveDowntimeController@get_downtime_plan_fact_by_report');
        Route::get('locomotivedowntimesbyone', 'Locomotives\Downtimes\LocomotiveDowntimeController@get_downtime_by_locomotive');
        Route::get('locomotivedowntimesbyreason', 'Locomotives\Downtimes\LocomotiveDowntimeController@get_downtime_by_reason');
        Route::apiResource('locomotiveplans', 'Locomotives\PlansFacts\LocomotivePlanController');
        Route::apiResource('locomotivefacts', 'Locomotives\PlansFacts\LocomotiveFactController');
    });
});
Route::namespace('App\Http\Controllers\Api')->group(function() {
    Route::post('auth/login', 'AuthController@login');
    Route::group(['middleware' => 'auth:sanctum'], function () {
        // Auth routes
        Route::get('auth/user', 'AuthController@user');
        Route::post('auth/logout', 'AuthController@logout');

        Route::get('/user', function (Request $request) {
            return new UserResource($request->user());
        });

        // Api resource routes
        Route::apiResource('roles', 'RoleController')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::apiResource('users', 'UserController')->middleware('permission:' . Acl::PERMISSION_USER_MANAGE);
        Route::apiResource('permissions', 'PermissionController')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);

        // Custom routes
        Route::put('users/{user}', 'UserController@update');
        Route::put('users/{user}/updatepassword', 'UserController@updatePassword');
        Route::get('users/{user}/permissions', 'UserController@permissions')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::put('users/{user}/permissions', 'UserController@updatePermissions')->middleware('permission:' .Acl::PERMISSION_PERMISSION_MANAGE);
        Route::get('roles/{role}/permissions', 'RoleController@permissions')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
    });
});

Route::namespace('App\Kio')->group(function() {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('roleslist', 'Managers\ManagerController@roles_list');

        Route::apiResource('technicatypes', 'Technicas\TechnicaTypeController');
        Route::get('technicagrouplist/{id}', 'Technicas\TechnicaTypeController@grouplist')->whereNumber('id');
        Route::get('technicagrouplist', 'Technicas\TechnicaTypeController@grouplist');
        Route::apiResource('managers', 'Managers\ManagerController');
        Route::get('managerslist', 'Managers\ManagerController@all_list');
        Route::get('placesbymanager/{id_manager}/{aggregate?}', 'Managers\ManagerController@places_by_manager')->whereNumber('id_manager')->whereAlpha('aggregate');
        Route::apiResource('places', 'Places\PlaceController');
        Route::get('placebyid/{id}', 'Places\PlaceController@place_by_id')->whereNumber('id');
        Route::apiResource('placesshifts', 'Places\Shifts\PlaceShiftController');
        //Route::patch('places/changeorder/{id}/{sort}', 'Places\PlaceController@change_order')->whereNumber('id')->whereNumber('sort');

        Route::apiResource('reasons', 'Reasons\ReasonController');
        Route::get('reasonsselect', 'Reasons\ReasonController@get_for_select'); //for select
        Route::get('reasonswithcodes/{aggregate}', 'Reasons\ReasonController@get_with_codes_grouped')->whereAlpha('aggregate'); //for select
        Route::get('reasonsselectbytechnicatype/{id}', 'Reasons\ReasonController@get_for_select_by_technica_type')->whereNumber('id'); //for select
        Route::apiResource('codes', 'Reasons\CodeController');
        Route::get('codesselect', 'Reasons\CodeController@get_for_select');//for select

        Route::apiResource('reports', 'Reports\ReportController');
        Route::apiResource('reportswithoutcodes', 'Reports\ReportController@reports_without_codes');
        Route::apiResource('reportdowntimecodes', 'Reports\ReportDwontimeCodeController');

        // Api KIO resource routes for excavators
        Route::apiResource('excavators', 'Excavators\ExcavatorController');
        Route::get('excavatorsbygroup', 'Excavators\ExcavatorController@search_by_group');
        Route::apiResource('excavatorplaces', 'Excavators\ExcavatorPlaceController');
        Route::apiResource('excavatorplaces', 'Excavators\ExcavatorPlaceController');
        Route::apiResource('excavatordowntimes', 'Excavators\Downtimes\ExcavatorDowntimeController');
        Route::get('excavatordowntimesdaily', 'Excavators\Downtimes\ExcavatorDowntimeController@get_downtime_daily');
        Route::get('excavatorplansfactsdowntimes', 'Excavators\Downtimes\ExcavatorDowntimeController@get_downtime_plan_fact');
        Route::get('excavatorplansfactsdowntimesbyreport', 'Excavators\Downtimes\ExcavatorDowntimeController@get_downtime_plan_fact_by_report');
        Route::get('excavatordowntimesbyone', 'Excavators\Downtimes\ExcavatorDowntimeController@get_downtime_by_excavator');
        Route::get('excavatordowntimesbyreason', 'Excavators\Downtimes\ExcavatorDowntimeController@get_downtime_by_reason');
        Route::apiResource('excavatorplans', 'Excavators\PlansFacts\ExcavatorPlanController');
        Route::apiResource('excavatorfacts', 'Excavators\PlansFacts\ExcavatorFactController');

        // Api KIO resource routes for drills
        Route::apiResource('drills', 'Drills\DrillController');
        Route::get('drillsbygroup', 'Drills\DrillController@search_by_group');
        Route::apiResource('drillplaces', 'Drills\DrillPlaceController');
        Route::apiResource('drillplaces', 'Drills\DrillPlaceController');
        Route::apiResource('drilldowntimes', 'Drills\Downtimes\DrillDowntimeController');
        Route::get('drilldowntimesdaily', 'Drills\Downtimes\DrillDowntimeController@get_downtime_daily');
        Route::get('drillplansfactsdowntimes', 'Drills\Downtimes\DrillDowntimeController@get_downtime_plan_fact');
        Route::get('drillplansfactsdowntimesbyreport', 'Drills\Downtimes\DrillDowntimeController@get_downtime_plan_fact_by_report');
        Route::get('drilldowntimesbyone', 'Drills\Downtimes\DrillDowntimeController@get_downtime_by_drill');
        Route::get('drilldowntimesbyreason', 'Drills\Downtimes\DrillDowntimeController@get_downtime_by_reason');
        Route::apiResource('drillplans', 'Drills\PlansFacts\DrillPlanController');
        Route::apiResource('drillfacts', 'Drills\PlansFacts\DrillFactController');

        // Api KIO resource routes for dsms
        Route::apiResource('dsms', 'Dsms\DsmController');
        Route::get('dsmsbygroup', 'Dsms\DsmController@search_by_group');
        Route::apiResource('dsmplaces', 'Dsms\DsmPlaceController');
        Route::apiResource('dsmplaces', 'Dsms\DsmPlaceController');
        Route::apiResource('dsmdowntimes', 'Dsms\Downtimes\DsmDowntimeController');
        Route::get('dsmdowntimesdaily', 'Dsms\Downtimes\DsmDowntimeController@get_downtime_daily');
        Route::get('dsmplansfactsdowntimes', 'Dsms\Downtimes\DsmDowntimeController@get_downtime_plan_fact');
        Route::get('dsmplansfactsdowntimesbyreport', 'Dsms\Downtimes\DsmDowntimeController@get_downtime_plan_fact_by_report');
        Route::get('dsmdowntimesbyone', 'Dsms\Downtimes\DsmDowntimeController@get_downtime_by_dsm');
        Route::get('dsmdowntimesbyreason', 'Dsms\Downtimes\DsmDowntimeController@get_downtime_by_reason');
        Route::apiResource('dsmplans', 'Dsms\PlansFacts\DsmPlanController');
        Route::apiResource('dsmfacts', 'Dsms\PlansFacts\DsmFactController');

        // Api KIO resource routes for cpts
        Route::apiResource('cpts', 'Cpts\CptController');
        Route::get('cptsbygroup', 'Cpts\CptController@search_by_group');
        Route::apiResource('cptplaces', 'Cpts\CptPlaceController');
        Route::apiResource('cptplaces', 'Cpts\CptPlaceController');
        Route::apiResource('cptdowntimes', 'Cpts\Downtimes\CptDowntimeController');
        Route::get('cptdowntimesdaily', 'Cpts\Downtimes\CptDowntimeController@get_downtime_daily');
        Route::get('cptplansfactsdowntimes', 'Cpts\Downtimes\CptDowntimeController@get_downtime_plan_fact');
        Route::get('cptplansfactsdowntimesbyreport', 'Cpts\Downtimes\CptDowntimeController@get_downtime_plan_fact_by_report');
        Route::get('cptdowntimesbyone', 'Cpts\Downtimes\CptDowntimeController@get_downtime_by_cpt');
        Route::get('cptdowntimesbyreason', 'Cpts\Downtimes\CptDowntimeController@get_downtime_by_reason');
        Route::apiResource('cptplans', 'Cpts\PlansFacts\CptPlanController');
        Route::apiResource('cptfacts', 'Cpts\PlansFacts\CptFactController');

        // Api KIO resource routes for locomotives
        Route::apiResource('locomotives', 'Locomotives\LocomotiveController');
        Route::get('locomotivesbygroup', 'Locomotives\LocomotiveController@search_by_group');
        Route::apiResource('locomotiveplaces', 'Locomotives\LocomotivePlaceController');
        Route::apiResource('locomotiveplaces', 'Locomotives\LocomotivePlaceController');
        Route::apiResource('locomotivedowntimes', 'Locomotives\Downtimes\LocomotiveDowntimeController');
        Route::get('locomotivedowntimesdaily', 'Locomotives\Downtimes\LocomotiveDowntimeController@get_downtime_daily');
        Route::get('locomotiveplansfactsdowntimes', 'Locomotives\Downtimes\LocomotiveDowntimeController@get_downtime_plan_fact');
        Route::get('locomotiveplansfactsdowntimesbyreport', 'Locomotives\Downtimes\LocomotiveDowntimeController@get_downtime_plan_fact_by_report');
        Route::get('locomotivedowntimesbyone', 'Locomotives\Downtimes\LocomotiveDowntimeController@get_downtime_by_locomotive');
        Route::get('locomotivedowntimesbyreason', 'Locomotives\Downtimes\LocomotiveDowntimeController@get_downtime_by_reason');
        Route::apiResource('locomotiveplans', 'Locomotives\PlansFacts\LocomotivePlanController');
        Route::apiResource('locomotivefacts', 'Locomotives\PlansFacts\LocomotiveFactController');

        // Api KIO resource routes for trucks
        Route::apiResource('trucks', 'Trucks\TruckController');
        Route::get('trucksbygroup', 'Trucks\TruckController@search_by_group');
        Route::apiResource('truckplaces', 'Trucks\TruckPlaceController');
        Route::apiResource('truckplaces', 'Trucks\TruckPlaceController');
        Route::apiResource('truckdowntimes', 'Trucks\Downtimes\TruckDowntimeController');
        Route::get('truckdowntimesdaily', 'Trucks\Downtimes\TruckDowntimeController@get_downtime_daily');
        Route::get('truckplansfactsdowntimes', 'Trucks\Downtimes\TruckDowntimeController@get_downtime_plan_fact');
        Route::get('truckplansfactsdowntimesbyreport', 'Trucks\Downtimes\TruckDowntimeController@get_downtime_plan_fact_by_report');
        Route::get('truckdowntimesbyone', 'Trucks\Downtimes\TruckDowntimeController@get_downtime_by_truck');
        Route::get('truckdowntimesbyreason', 'Trucks\Downtimes\TruckDowntimeController@get_downtime_by_reason');
        Route::apiResource('truckplans', 'Trucks\PlansFacts\TruckPlanController');
        Route::apiResource('truckfacts', 'Trucks\PlansFacts\TruckFactController');

    });
});

// Fake APIs
Route::get('/table/list', function () {
    $rowsNumber = mt_rand(20, 30);
    $data = [];
    for ($rowIndex = 0; $rowIndex < $rowsNumber; $rowIndex++) {
        $row = [
            'author' => Faker::randomString(mt_rand(5, 10)),
            'display_time' => Faker::randomDateTime()->format('Y-m-d H:i:s'),
            'id' => mt_rand(100000, 100000000),
            'pageviews' => mt_rand(100, 10000),
            'status' => Faker::randomInArray(['deleted', 'published', 'draft']),
            'title' => Faker::randomString(mt_rand(20, 50)),
        ];

        $data[] = $row;
    }

    return response()->json(new JsonResponse(['items' => $data]));
});

Route::get('/orders', function () {
    $rowsNumber = 8;
    $data = [];
    for ($rowIndex = 0; $rowIndex < $rowsNumber; $rowIndex++) {
        $row = [
            'order_no' => 'LARAVUE' . mt_rand(1000000, 9999999),
            'price' => mt_rand(10000, 999999),
            'status' => Faker::randomInArray(['success', 'pending']),
        ];

        $data[] = $row;
    }

    return response()->json(new JsonResponse(['items' => $data]));
});

Route::get('/articles', function () {
    $rowsNumber = 10;
    $data = [];
    for ($rowIndex = 0; $rowIndex < $rowsNumber; $rowIndex++) {
        $row = [
            'id' => mt_rand(100, 10000),
            'display_time' => Faker::randomDateTime()->format('Y-m-d H:i:s'),
            'title' => Faker::randomString(mt_rand(20, 50)),
            'author' => Faker::randomString(mt_rand(5, 10)),
            'comment_disabled' => Faker::randomBoolean(),
            'content' => Faker::randomString(mt_rand(100, 300)),
            'content_short' => Faker::randomString(mt_rand(30, 50)),
            'status' => Faker::randomInArray(['deleted', 'published', 'draft']),
            'forecast' => mt_rand(100, 9999) / 100,
            'image_uri' => 'https://via.placeholder.com/400x300',
            'importance' => mt_rand(1, 3),
            'pageviews' => mt_rand(10000, 999999),
            'reviewer' => Faker::randomString(mt_rand(5, 10)),
            'timestamp' => Faker::randomDateTime()->getTimestamp(),
            'type' => Faker::randomInArray(['US', 'VI', 'JA']),

        ];

        $data[] = $row;
    }

    return response()->json(new JsonResponse(['items' => $data, 'total' => mt_rand(1000, 10000)]));
});

Route::get('articles/{id}', function ($id) {
    $article = [
        'id' => $id,
        'display_time' => Faker::randomDateTime()->format('Y-m-d H:i:s'),
        'title' => Faker::randomString(mt_rand(20, 50)),
        'author' => Faker::randomString(mt_rand(5, 10)),
        'comment_disabled' => Faker::randomBoolean(),
        'content' => Faker::randomString(mt_rand(100, 300)),
        'content_short' => Faker::randomString(mt_rand(30, 50)),
        'status' => Faker::randomInArray(['deleted', 'published', 'draft']),
        'forecast' => mt_rand(100, 9999) / 100,
        'image_uri' => 'https://via.placeholder.com/400x300',
        'importance' => mt_rand(1, 3),
        'pageviews' => mt_rand(10000, 999999),
        'reviewer' => Faker::randomString(mt_rand(5, 10)),
        'timestamp' => Faker::randomDateTime()->getTimestamp(),
        'type' => Faker::randomInArray(['US', 'VI', 'JA']),

    ];

    return response()->json(new JsonResponse($article));
});

Route::get('articles/{id}/pageviews', function ($id) {
    $pageviews = [
        'PC' => mt_rand(10000, 999999),
        'Mobile' => mt_rand(10000, 999999),
        'iOS' => mt_rand(10000, 999999),
        'android' => mt_rand(10000, 999999),
    ];
    $data = [];
    foreach ($pageviews as $device => $pageview) {
        $data[] = [
            'key' => $device,
            'pv' => $pageview,
        ];
    }

    return response()->json(new JsonResponse(['pvData' => $data]));
});
