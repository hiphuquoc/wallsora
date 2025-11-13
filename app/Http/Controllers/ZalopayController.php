<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Order;

class ZalopayController extends Controller{

    public static function create($infoOrder){
        $urlRedirect    = null;
        if(!empty($infoOrder)){
            $embeddata  = [
                "redirecturl" => route('main.handlePaymentZalopay', ['code' => $infoOrder->code])
            ];
            $reqtime    = round(microtime(true) * 1000); // miliseconds
            /* truyển thông tin sản phẩm trong order vào */
            $dataItem   = [];
            /* ngôn ngữ mặc định của thanh toán Zalo là tiếng việt - đơn vị tiền đồng */
            $language   = 'vi';
            $i          = 0;
            foreach($infoOrder->products as $product){
                $dataItem[$i]['code']       = $product->infoProduct->code ?? null;
                $dataItem[$i]['name']       = $product->infoProduct->name ?? null;
                $dataItem[$i]['option']     = $product->infoPrice->name ?? null;
                $dataItem[$i]['price']      =  'all';
                if(!empty($product->infoPrice->price)){
                    $tmp                    = \App\Helpers\Number::getPriceByLanguage($product->infoPrice->price, $language); /* hàm trả ra array */
                    $dataItem[$i]['price']  =  $tmp['number'];
                }
                $dataItem[$i]['sale_off']   = $product->infoPrice->sale_off ?? null;
                ++$i;
            }
            /* bankcode */
            $bankcode   = self::getBankcode();
            /* tổng tiền (không có phí thanh toán) */
            $tmp        = \App\Helpers\Number::getPriceByLanguage($infoOrder->total, $language);
            $total      = $tmp['number'] ?? 0;
            if($total>0){
                $order  = [
                    "app_id"        => config('payment.zalopay.app_id'),
                    "app_time"      => $reqtime,
                    "app_trans_id"  => date("ymd")."_".$infoOrder->code, // translation missing: vi.docs.shared.sample_code.comments.app_trans_id
                    "app_user"      => config('payment.zalopay.app_user'),
                    "item"          => json_encode($dataItem, JSON_UNESCAPED_UNICODE),
                    "embed_data"    => json_encode($embeddata, JSON_UNESCAPED_UNICODE),
                    "amount"        => $total,
                    "description"   => ucfirst(env('APP_NAME'))." - Thanh toán Đơn hàng $infoOrder->code",
                    "bank_code"     => array_merge(["zalopayapp"], $bankcode)
                ];
                $data           = $order["app_id"] . "|" . $order["app_trans_id"] . "|" . $order["app_user"] . "|" . $order["amount"]
                . "|" . $order["app_time"] . "|" . $order["embed_data"] . "|" . $order["item"];
                $order["mac"]   = hash_hmac("sha256", $data, config('payment.zalopay.key_1'));

                $context = stream_context_create([
                    "http" => [
                        "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                        "method" => "POST",
                        "content" => http_build_query($order)
                    ]
                ]);
                
                $resp   = file_get_contents(config('payment.zalopay.endpoint'), false, $context);
                $result = json_decode($resp, true);
                if(!empty($result['order_url'])) $urlRedirect = $result['order_url'];
                //   foreach ($result as $key => $value) {
                //     echo "$key: $value<br>";
                //   }
            }
        }
        return $urlRedirect;
    }

    private static function getBankcode(){
        $result     = [];
        $config     = [
            "appid"     => config('payment.zalopay.app_id'),
            "key1"      => config('payment.zalopay.key_1'),
            "key2"      => config('payment.zalopay.key_2'),
            "endpoint"  => config('payment.zalopay.api.getbanklist'),
        ];
        $reqtime = round(microtime(true) * 1000); // miliseconds
        $params = [
            "appid"     => config('payment.zalopay.app_id'),
            "reqtime"   => $reqtime,
            "mac"       => hash_hmac("sha256", config('payment.zalopay.app_id')."|".$reqtime, config('payment.zalopay.key_1')) // appid|reqtime
        ];
        
        $resp       = file_get_contents($config["endpoint"]."?".http_build_query($params));
        $response   = json_decode($resp, true);
        if(!empty($response['banks'][39])){
            foreach($response['banks'][39] as $bank){
                $result[] = $bank['bankcode'];
            }
        }
        return $result;
    }

}
