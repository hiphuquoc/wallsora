<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MomoController extends Controller{

    public static function create($infoOrder){
        $urlRedirect            = null;
        if(!empty($infoOrder)){
            /* ngôn ngữ mặc định của thanh toán Zalo là tiếng việt - đơn vị tiền đồng */
            $language           = 'vi';
            /* tổng tiền (không có phí thanh toán) */
            $tmp                = \App\Helpers\Number::getPriceByLanguage($infoOrder->total, $language);
            $amount             = $tmp['number'] ?? 0;
            if($amount>0){
                $endpoint       = config('payment.momo.endpoint_create');
                $partnerCode    = config('payment.momo.partner_code');
                $accessKey      = config('payment.momo.access_key');
                $secretKey      = config('payment.momo.secret_key');
                $orderInfo      = ucfirst(env('APP_NAME')).' - Thanh toán đơn hàng '.$infoOrder->code;
                $orderId        = $infoOrder->code ?? time();
                /* đường dẫn sau khi thanh toán thành công */
                $redirectUrl    = route('main.handlePaymentMomo');
                $ipnUrl         = $_SERVER['HTTP_REFERER'] ?? route('main.home');
                $extraData      = $infoOrder->note ?? '';
                $requestId      = time().'';
                $requestType    = "captureWallet";
                // $requestType    = "payWithATM";
                //before sign HMAC SHA256 signature
                $rawHash        = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
                $signature      = hash_hmac("sha256", $rawHash, $secretKey);
                $data           = array(
                    'partnerCode'   => $partnerCode,
                    'partnerName'   => config('payment.momo.partner_name'),
                    'storeId'       => config('payment.momo.store_id'),
                    'requestId'     => $requestId,
                    'amount'        => $amount,
                    'orderId'       => $orderId,
                    'orderInfo'     => $orderInfo,
                    'redirectUrl'   => $redirectUrl,
                    'ipnUrl'        => $ipnUrl,
                    'lang'          => 'vi',
                    'extraData'     => $extraData,
                    'requestType'   => $requestType,
                    'signature'     => $signature
                );
                $result         = self::execPostRequest($endpoint, json_encode($data));
                $jsonResult     = json_decode($result, true);  // decode json
                $urlRedirect    = $jsonResult['payUrl'];
                // header('Location: ' . $jsonResult['payUrl']);
            }
        }
        return $urlRedirect;
    }

    public static function execPostRequest($url, $data){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }

}
