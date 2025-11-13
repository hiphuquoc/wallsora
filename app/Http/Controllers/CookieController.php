<?php

namespace App\Http\Controllers;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Cookie;

class CookieController extends Controller {

    public static function setCookie($name, $value, $time=null){
        $response        = null;
        if(!empty($name)){
            $response   = Cookie::queue($name, $value, $time);
        }
        return $response;
    }

    public static function removeCookie($name){
        $flag = Cookie::queue($name, null, -3600);
        return $flag;
    }

    public function setCsrfFirstTime(){
        if(empty($_COOKIE['XSRF-TOKEN'])){
            Cookie::queue('XSRF-TOKEN', csrf_token(), 86400);
            return json_encode(true);
        }
        return json_encode(false);
    }

}
