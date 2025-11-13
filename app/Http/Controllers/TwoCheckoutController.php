<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

class TwoCheckoutController extends Controller {
    public static function create($infoOrder) {
        $language           = session()->get('language') ?? 'vi';
        $merchantCode       = '255288914119';
        $secretKey          = 'X~Vsedu|QH8#&2xS@E]T';
        $merchantReference  = (string) Str::uuid();  // Đảm bảo sử dụng UUID

        $tmp                = \App\Helpers\Number::getPriceByLanguage($infoOrder->total, $language);
        $amount             = $tmp['number'] ?? 0;
        $currency           = $tmp['currency'];

        $infoCustomer       = [];
        if (!empty($infoOrder->customer)) {
            $infoCustomer = [
                'email' => $infoOrder->customer->email,
            ];
        }

        $redirectUrl = route('main.handlePaymentTwoCheckout');

        $data = [
            'payment_provider_contract' => 'your_contract_id',
            'redirect_url'              => $redirectUrl,
            'amount'                    => $amount,
            'customer'                  => $infoCustomer,
            'merchant_reference'        => $merchantReference,
            'currency_code'             => $currency,
        ];

        $url        = 'https://cst.test-gsc.vfims.com/oidc/api/v2/transactions/mobilepay';
        $authHeader = 'Basic ' . base64_encode($merchantCode . ':' . $secretKey);

        $result     = self::execPostRequest($url, json_encode($data), $authHeader);

        dd($result);

        return json_decode($result, true);
    }

    public static function execPostRequest($url, $data, $authHeader){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $authHeader,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception("cURL Error: $error");
        }

        curl_close($ch);
        return $result;
    }
}