<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Response;
use App\Models\Seo;
use App\Models\ISO3166;
use App\Helpers\GeoIP;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\CarbonTimeZone;

class SettingController extends Controller {

    public static function settingIpVisitor(){
        $infoCountry    = GeoIP::getLocation();
        // Lấy hệ số giá từ bảng ISO3166
        $tmp    = ISO3166::select('percent_discount')
                    ->where('alpha_2', $infoCountry['iso_code'])
                    ->first();
        $percentDiscount = $tmp->percent_discount ?? 1;
        if(!empty($infoCountry['country_name'])&&!empty($infoCountry['iso_code'])){
            $infoSave    = [
                'country_name'      => $infoCountry['country_name'],
                'iso_code'          => $infoCountry['iso_code'],
                'percent_discount'  => $percentDiscount,
            ];
            // // Thiết lập session
            // $flag = session()->put('info_ip', $infoSave);
            // // Ghi session ngay lập tức
            // session()->save();

            // lưu Cache để dùng ngay
            Cache::put('info_ip', $infoSave, now()->addMinutes(1));
            // Lưu thông tin vào cookie
            $cookie = cookie('info_ip', json_encode($infoSave), 3600); // Lưu trong 3600 phút

            return response()->json(['flag' => true])->cookie($cookie);
        }
        return response()->json(['flag' => false]);
    }

    public static function settingGPSVisitor(Request $request){
        $infoCountry = self::getCountryFromNominatim($request->get('latitude'), $request->get('longitude'));
        // Lấy hệ số giá từ bảng ISO3166
        $tmp    = ISO3166::select('percent_discount')
                    ->where('alpha_2', $infoCountry['iso_code'])
                    ->first();
        $percentDiscount = $tmp->percent_discount ?? 1;
        if(!empty($infoCountry['country_name'])&&!empty($infoCountry['iso_code'])){
            $infoSave = [
                'country_name'      => $infoCountry['country_name'],
                'iso_code'          => $infoCountry['iso_code'],
                'percent_discount'  => $percentDiscount,
            ];
            // // Thiết lập session
            // session()->put('info_gps', $infoSave);
            // // Ghi session ngay lập tức
            // session()->save();

            // lưu Cache để dùng ngay
            Cache::put('info_gps', $infoSave, now()->addMinutes(1));
            // Lưu thông tin vào cookie
            $cookie = cookie('info_gps', json_encode($infoSave), 3600); // Lưu trong 3600 phút

            return response()->json(['flag' => true])->cookie($cookie);
        }
        return response()->json(['flag' => false]);
    }

    public static function settingTimezoneVisitor(Request $request){
        $timezone   = $request->get('timezone');

        $tmp        = ISO3166::select('*')
                        ->whereHas('timezones', function($query) use($timezone){
                            $query->where('timezone_lower', strtolower($timezone));
                        })
                        ->with('timezones')
                        ->first();
        if(!empty($tmp)){
            $infoSave = [
                'country_name'      => $tmp['name'],
                'iso_code'          => $tmp['alpha_2'],
                'percent_discount'  => $tmp['percent_discount'],
            ];
            // // Thiết lập session
            // session()->put('info_timezone', $infoSave);
            // // Ghi session ngay lập tức
            // session()->save();
            // lưu Cache để dùng ngay
            Cache::put('info_timezone', $infoSave, now()->addMinutes(1));
            // Lưu thông tin vào cookie
            $cookie = cookie('info_timezone', json_encode($infoSave), 3600); // Lưu trong 3600 phút

            return response()->json(['flag' => true])->cookie($cookie);
        }
        return response()->json(['flag' => false]);
    }

    private static function getCountryFromNominatim($latitude, $longitude){
        $url = "https://nominatim.openstreetmap.org/reverse";

        $response = Http::withHeaders([
            'User-Agent' => env('DOMAIN_NAME').'/1.0' // Thêm User-Agent để tránh bị từ chối
        ])->get($url, [
            'lat'    => $latitude,
            'lon'    => $longitude,
            'format' => 'json',
            'addressdetails' => 1
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['address']['country_code'])) {
                return [
                    'country_name' => $data['address']['country'],
                    'iso_code'     => strtoupper($data['address']['country_code']),
                ];
            }
        }

        return ['error' => 'Unable to determine country'];
    }

    public static function settingLanguage($language = 'vi'){
        Session::put('language', $language);
        return true;
    }

    public static function getLanguage(){
        $language   = request()->session()->get('language') ?? null;
        /* trường hợp truy cập lần đầu chưa ghi session -> xác định language thông qua slug */
        if(empty($language)){
            $referer = request()->headers->get('referer');
            if ($referer) {
                /* Lấy đường dẫn cuối cùng của referer */
                $urlParts   = explode('/', $referer);
                $slug       = end($urlParts) ?? null;
                $language   = self::getLanguageBySlug($slug);
            } else {
                $language   = 'vi';
            }
        }
        return $language;
    }

    public static function getLanguageBySlug($slug){
        $language = 'vi';
        if(!empty($slug)){
            $infoPage   = Seo::select('language')
                                ->where('slug', $slug)
                                ->first();
            $language   = $infoPage->language ?? 'vi';
        }
        return $language;
    }

    public static function settingCollapsedMenu(Request $request){
        $action = $request->get('action');
        
        // Lưu giá trị vào session
        Session::put('view_menu', $action);
    
        // Có thể trả về JSON hoặc true nếu cần cho AJAX response
        return response()->json(['success' => true]);
    }
    
    public static function getStatusCollapse(){
        $viewMenu = request()->session()->get('view_menu', 'off');
        return response()->json(['status' => $viewMenu]);
    }
}
