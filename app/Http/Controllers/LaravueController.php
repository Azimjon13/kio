<?php
/**
 * File LaravelController.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

/**
 * Class LaravueController
 *
 * @package App\Http\Controllers
 */
class LaravueController extends Controller
{
    /**
     * Entry point for Laravue Dashboard
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
//        if((\Request::ip() == '172.17.110.43' || \Request::ip() == '172.17.110.48') && is_null(\Request::user())){
//            $user_id = 1;//
//            $user = Auth::loginUsingId($user_id);
//            echo '<script>
//                document.cookie = "isLogged=1; expires=Session";
//                </script>';
//        }
        return view('laravue');
    }
}
