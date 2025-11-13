<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;


class PaypalController extends Controller{

    public static function create($infoOrder){
        $provider       = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        
        /* thanh toán paypal đang mặc định đơn vị tiền tệ USD */
        $amount         = $infoOrder->total;
        $currency       = 'USD';

        $response       = $provider->createOrder([
            'intent'                => 'CAPTURE',
            'invoice_id'            => $infoOrder->code,
            'invoice_description'   => "Order ".$infoOrder->code." Invoice",
            'total'                 => $amount,
            'application_context'   => [
                'return_url'        => route('main.handlePaymentPaypal', [
                    'code'  => $infoOrder->code
                ]),
                'cancel_url'        => env('APP_URL')
            ],
            'purchase_units'        => [
                0 => [
                    'amount'        => [
                        'currency_code' => $currency,
                        'value'     => $amount
                    ]
                ]
            ]
        ]);
        /* xủ lý response để lấy url redirect */
        $linkRedirect               = env('APP_URL');
        if(!empty($response['id'])){
            foreach($response['links'] as $link){
                if($link['rel']=='approve') {
                    $linkRedirect   = $link['href'];
                    break;
                }
            }
        }
       return $linkRedirect;       
    }

}
