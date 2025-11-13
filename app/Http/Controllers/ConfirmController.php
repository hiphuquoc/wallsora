<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\Order;
use App\Jobs\SendEmailOrder;
use Illuminate\Support\Facades\Session;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendProductMail;

class ConfirmController extends Controller {

    public static function confirm(Request $request, $slug){
        $language   = SettingController::getLanguageBySlug($slug);
        SettingController::settingLanguage($language);
        $item       = Page::select("*")
                        ->whereHas('seos.infoSeo', function($query) use($slug){
                            $query->where('slug', $slug);
                        })
                        ->first();
        /* lấy item seo theo ngôn ngữ được chọn */
        $itemSeo            = [];
        if (!empty($item->seos)) {
            foreach ($item->seos as $s) {
                if ($s->infoSeo->language == $language) {
                    $itemSeo = $s->infoSeo;
                    break;
                }
            }
        }
        $code       = $request->get('code') ?? 0;
        // $code       = 'FU3J5S0NLRCVAD2';
        $order      = Order::select('*')
                        ->where('code', $code)
                        ->with('wallpapers.infoWallpaper')
                        ->first();
        $language   = session('language') ?? 'vi';
        $breadcrumb = \App\Helpers\Url::buildBreadcrumb($itemSeo->slug_full);
        // if(!empty($order)&&$order->payment_status==1){
            return view('wallpaper.confirm.index', compact('item', 'itemSeo', 'order', 'breadcrumb', 'language'));
        // }
        // return redirect()->route('main.home');
    }

    public static function handlePaymentMomo(Request $request){
        /* lấy thông tin của Order trong CSDL */
        $code           = $request->get('orderId');
        $orderInfo      = Order::select('*')
                            ->where('code', $code)
                            ->first();
        $flagPayment    = false;
        /* Trường hợp MOMO: có mã đơn hàng => xử lý tiếp */
        if(!empty($request->get('orderId'))){
            /* cập nhật trans_id (id thanh toán của momo) */
            $transId    = $request->get('transId') ?? '';
            if(!empty($transId)) Order::updateItem($orderInfo->id, ['trans_id' => $transId]);
            /* kiểm tra đã thanh toán chưa */
            $resultCode = $request->get('resultCode');
            /* nếu đã thanh toán thành công */
            if(in_array($resultCode, config('payment.momo.payment_success_code'))) $flagPayment = true;
        }
        /* xử lý sau khi đã thanh toán thành công */
        if($flagPayment==true) {
            $language   = SettingController::getLanguage();
            self::handleAfterPayment($orderInfo, $language);
            /* lấy slug theo ngôn ngữ của trang xác nhận */
            $slug       = self::getSlugPageConfirmByLanguage($language);
            /* chuyển hướng sang trang nhận ảnh */
            if(!empty($slug)) return redirect()->route('main.confirm', ['slug' => $slug, 'code' => $code]);
        }
        /* thanh toán không thành công */
        return redirect()->route('main.home');
    }  

    public static function handlePaymentZalopay(Request $request){
        if(!empty($request->get('code'))){
            /* lấy thông tin của Order trong CSDL */
            $code           = $request->get('code');
            $orderInfo      = Order::select('*')
                                ->where('code', $code)
                                ->first();
            $flagPayment    = false;
            /* cập nhật trans_id (id thanh toán của zalopay) */
            if(!empty($request->get('apptransid'))){ /* tồn tại transid và đã thanh toán > 0 => xử lý tiếp */
                $transId        = $request->get('apptransid');
                Order::updateItem($orderInfo->id, ['trans_id' => $transId]);
                /* nếu đã thanh toán thành công */
                if(!empty($request->get('status'))&&$request->get('status')==1) $flagPayment = true;
                /* xử lý sau khi đã thanh toán thành công */
                if($flagPayment==true) {
                    $language   = SettingController::getLanguage();
                    self::handleAfterPayment($orderInfo, $language);
                    /* lấy slug theo ngôn ngữ của trang xác nhận */
                    $slug       = self::getSlugPageConfirmByLanguage($language);
                    /* chuyển hướng sang trang nhận ảnh */
                    if(!empty($slug)) return redirect()->route('main.confirm', ['slug' => $slug, 'code' => $code]);
                }
            }
        }
        /* thanh toán không thành công */
        return redirect()->route('main.home');
    }

    public static function handlePaymentVNPay(Request $request){
        if(!empty($request->get('code'))){
            /* lấy thông tin của Order trong CSDL */
            $code           = $request->get('code');
            $orderInfo      = Order::select('*')
                                ->where('code', $code)
                                ->first();
            $flagPayment    = false;
            /* cập nhật trans_no */
            if(!empty($request->get('vnp_TransactionNo'))){ /* tồn tại trans_no và đã thanh toán > 0 => xử lý tiếp */
                $transId        = $request->get('vnp_TransactionNo');
                Order::updateItem($orderInfo->id, ['trans_id' => $transId]);
                /* nếu đã thanh toán thành công */
                $arrayCodeSuccess    = [
                    '00', /* Giao dịch thành công */
                    // '04', /* Giao dịch đảo (Khách hàng đã bị trừ tiền tại Ngân hàng nhưng GD chưa thành công ở VNPAY) */
                ];
                $codeStatus     = $request->get('vnp_TransactionStatus') ?? '';
                if(in_array($codeStatus, $arrayCodeSuccess)) $flagPayment = true;
                /* xử lý sau khi đã thanh toán thành công */
                if($flagPayment==true) {
                    $language   = SettingController::getLanguage();
                    self::handleAfterPayment($orderInfo, $language, false); /* không cập nhật trạng thái thanh toán để test */
                    /* lấy slug theo ngôn ngữ của trang xác nhận */
                    
                    $slug       = self::getSlugPageConfirmByLanguage($language);
                    /* chuyển hướng sang trang nhận ảnh */
                    if(!empty($slug)) return redirect()->route('main.confirm', ['slug' => $slug, 'code' => $code]);
                }
            }
        }
        /* thanh toán không thành công */
        return redirect()->route('main.home');
    }

    public static function handlePaymentTwoCheckout(Request $request){

        dd($request->all());
        // if(!empty($request->get('code'))){
        //     /* lấy thông tin của Order trong CSDL */
        //     $code           = $request->get('code');
        //     $orderInfo      = Order::select('*')
        //                         ->where('code', $code)
        //                         ->first();
        //     $flagPayment    = false;
        //     /* cập nhật trans_id (id thanh toán của zalopay) */
        //     if(!empty($request->get('apptransid'))){ /* tồn tại transid và đã thanh toán > 0 => xử lý tiếp */
        //         $transId        = $request->get('apptransid');
        //         Order::updateItem($orderInfo->id, ['trans_id' => $transId]);
        //         /* nếu đã thanh toán thành công */
        //         if(!empty($request->get('status'))&&$request->get('status')==1) $flagPayment = true;
        //         /* xử lý sau khi đã thanh toán thành công */
        //         if($flagPayment==true) {
            // $language   = SettingController::getLanguage();
        //             self::handleAfterPayment($orderInfo, $language);
        //             /* lấy slug theo ngôn ngữ của trang xác nhận */
        //             
        //             $slug       = self::getSlugPageConfirmByLanguage($language);
        //             /* chuyển hướng sang trang nhận ảnh */
        //             if(!empty($slug)) return redirect()->route('main.confirm', ['slug' => $slug, 'code' => $code]);
        //         }
        //     }
        // }
        // /* thanh toán không thành công */
        // return redirect()->route('main.home');
    }

    public static function handlePaymentPaypal(Request $request){
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);
        if (!empty($response['status'])&&$response['status']=='COMPLETED') {
             /* lấy thông tin của Order trong CSDL */
            $code           = $request->get('code');
            $orderInfo      = Order::select('*')
                                ->where('code', $code)
                                ->first();
            $flagPayment    = false;
            /* cập nhật trans_id (id thanh toán của zalopay) */
            $transId        = $request->get('PayerID');
            Order::updateItem($orderInfo->id, ['trans_id' => $transId]);
            /* nếu đã thanh toán thành công */
            $flagPayment = true;
            /* xử lý sau khi đã thanh toán thành công */
            if($flagPayment==true) {
                $language   = SettingController::getLanguage();
                self::handleAfterPayment($orderInfo, $language);
                /* lấy slug theo ngôn ngữ của trang xác nhận */
                $slug       = self::getSlugPageConfirmByLanguage($language);
                /* chuyển hướng sang trang nhận ảnh */
                if(!empty($slug)) return redirect()->route('main.confirm', ['slug' => $slug, 'code' => $code]);
            }
        }
        /* thanh toán không thành công */
        return redirect()->route('main.home');
    }
    
    private static function handleAfterPayment($orderInfo, $language, $updateStatus = true){
        if(!empty($orderInfo)){
            /* cập nhật trạng thái thành toán thành công */
            if($updateStatus == true) Order::updateItem($orderInfo->id, ['payment_status' => 1]);
            /* nếu là thanh toán giỏ hàng => clear giỏ hàng */
            if($orderInfo->payment_type=='payment_cart') Session::forget('cart');
            /* tạo job gửi email */
            if(!empty($orderInfo->customer->email)) Mail::to($orderInfo->customer->email)->queue(new SendProductMail($orderInfo, $language));
        }
    }

    private static function getSlugPageConfirmByLanguage($language){
        $slug       = '';
        if(!empty($language)){
            $tmp        = Page::select('*')
                            ->whereHas('seos.infoSeo', function($query){
                                $query->whereIn('slug', config('main_'.env('APP_NAME').'.url_confirm_page'));
                            })
                            ->first();
            
            foreach($tmp->seos as $seo){
                if($seo->infoSeo->language==$language) {
                    $slug = $seo->infoSeo->slug;
                    break;
                }
            }
        }
        return $slug;
    }
}
