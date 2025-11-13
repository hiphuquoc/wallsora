<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Jobs\BuildScss;
use App\Models\Theme;

class SettingController extends Controller {

    public function view(Request $request){
        if(!empty($request->get('name'))&&!empty($request->get('default'))){
            $flag = \App\Http\Controllers\Admin\CookieController::set($request->get('name'), $request->get('default'), 86400);
        }
    }

    public static function slider(Request $request){
        return view('admin.setting.slider');
    }
}
