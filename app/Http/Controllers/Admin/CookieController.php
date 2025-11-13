<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CookieController extends Controller {

    public static function set($name = null, $value = null, $time = 86400){
        $flag       = false;
        if(!empty($name)&&!empty($value)){
            Cookie::queue($name, $value, $time);
            $flag   = true;
        }
        return $flag;
    }
    
}
