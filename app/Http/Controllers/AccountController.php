<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

use App\Models\Order;

class AccountController extends Controller {

    public static function orders(Request $request){
        // $item       = new \Illuminate\Database\Eloquent\Collection;
        // $emailUser  = Auth::user()->email;
        // $language   = SettingController::getLanguage();
        // $orders     = Order::select('*')
        //                 ->where('email', $emailUser)
        //                 ->where('payment_status', 1)
        //                 ->with('products', 'wallpapers')
        //                 ->get();
        // return view('wallpaper.account.myDownload', compact('item', 'orders', 'language'));

        // chưa dùng
    }

}
