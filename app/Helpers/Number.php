<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class Number {

    public static function calculatorPriceBeforeSaleoff($price, $saleOff){
        $result         = 0;
        if(!empty($price)&&!empty($saleOff)){
            $result     = ($price*100)/(100 - $saleOff);
        }
        return $result;
    }

    // public static function getFormatPriceByLanguage($number, $language, $showCurrency = true){
    //     $result         = null;
    //     $tmp            = self::getPriceByLanguage($number, $language);
    //     if($showCurrency==true){
    //         $result     = $tmp['number'].$tmp['currency_code'];
    //     }else {
    //         $result     = $tmp['number'];
    //     }
    //     return $result;
    // }

    public static function getFormatPriceByLanguage($number, $language, $showCurrency = true){
        $result = null;
        $tmp = self::getPriceByLanguage($number, $language);

        // Format số với số chữ số thập phân thích hợp
        $formattedNumber = number_format($tmp['number'], $tmp['decimal_places'], '.', ',');
        
        if ($showCurrency) {
            $result = $formattedNumber . ' ' . $tmp['currency_code'];
        } else {
            $result = $formattedNumber;
        }
        
        return $result;
    }

    public static function getPriceByLanguage($number, $language){
        /* ghi chú: ở hàm này không xử lý việc */
        $result         = [
            'number'            => 0,
            'currency'          => null,
            'currency_code'     => null,
        ];
        $exchangeRate               = config('language.'.$language.'.money_value');
        $calculator                 = $number * $exchangeRate;
        $result['number']           = $calculator;
        $result['currency']         = config('language.'.$language.'.currency');
        $result['currency_code']    = config('language.'.$language.'.currency_code');
        $result['decimal_places']    = config('language.'.$language.'.decimal_places');
        return $result;
    }

    public static function getPriceOriginByCountry($number){
        // /* hệ số giảm giá theo khu vực (nằm trong session) */
        // $percentDiscount            = session()->get('info_gps')['percent_discount']  
        //                                 ?? session()->get('info_timezone')['percent_discount']
                                        // ?? Cache::get('info_timezone')['percent_discount'] 
                                        // ?? Cache::get('info_gps')['percent_discount'] 
        //                                 /* ip chỉ là phương án cuối cùng */
        //                                 ?? session()->get('info_ip')['percent_discount']
        //                                 ?? Cache::get('info_ip')['percent_discount'] 
        //                                 ?? config('main_'.env('APP_NAME').'.percent_discount_default');
        // Get cookies from the request
        $infoGps       = json_decode(request()->cookie('info_gps'), true);
        $infoTimezone  = json_decode(request()->cookie('info_timezone'), true);
        $infoIp        = json_decode(request()->cookie('info_ip'), true);
        // Determine the discount factor from available cookies or fallback
        $percentDiscount            = $infoGps['percent_discount'] 
                                        ?? $infoTimezone['percent_discount'] 
                                        ?? Cache::get('info_timezone')['percent_discount'] 
                                        ?? Cache::get('info_gps')['percent_discount'] 
                                        ?? $infoIp['percent_discount'] 
                                        ?? Cache::get('info_ip')['percent_discount'] 
                                        ?? config('main_' . env('APP_NAME') . '.percent_discount_default');
        /* kết quả */
        $number                     = $number * $percentDiscount;
        return $number;
    }

    public static function calculatorSaleOffByPriceMaxAndPriceOriginByCountry($priceMax, $priceOriginByCountry){
        $saleOff                    = 0;
        if(!empty($priceMax)&&!empty($priceOriginByCountry)){
            $saleOff                = number_format((($priceMax - $priceOriginByCountry)/$priceMax)*100, 0);
        }
        return $saleOff;
    }
}