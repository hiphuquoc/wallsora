<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Order;
use App\Models\ProductPrice;
use App\Models\RelationOrderInfoWallpaperInfo;
// use App\Models\OrderProduct;
use App\Services\BuildInsertUpdateModel;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\App;

class CheckoutController extends Controller{

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    // public static function index(Request $request){
    //     $payments       = PaymentMethod::all();
    //     // $provinces      = Province::all();
    //     $products       = \App\Http\Controllers\CartController::getCollectionProducts();
    //     $productsCart   = json_decode(Cookie::get('cart'), true);
    //     return view('main.checkout.index', compact('payments', 'provinces', 'products', 'productsCart'));
    // }

    public function paymentCart(Request $request){
        /* tạo đơn hàng */
        $params             = $request->all();
        $params['payment_type'] = 'payment_cart';
        $orderController    = App::make(OrderController::class);
        $idOrder            = $orderController->create($params);
        /* lấy ngược lại thông tin order để xử lý cho chính xác */
        $orderInfo          = Order::select('*')
                                ->where('id', $idOrder)
                                ->with('products.infoProduct', 'products.infoPrice', 'paymentMethod')
                                ->first();
        $urlRedirect        = self::getUrlRedirect($orderInfo);
        /* trả về đường dẫn để chuyển hướng */
        return redirect($urlRedirect);
    }

    public function paymentNow(Request $request){
        /* tạo đơn hàng */
        $params                 = $request->all();
        $params['payment_type'] = 'payment_now';
        $orderController        = App::make(OrderController::class);
        $idOrder                = $orderController->create($params);
        /* lấy ngược lại thông tin order để xử lý cho chính xác */
        $orderInfo              = Order::select('*')
                                    ->where('id', $idOrder)
                                    ->with('products.infoProduct', 'products.infoPrice', 'paymentMethod')
                                    ->first();
        $urlRedirect            = self::getUrlRedirect($orderInfo);
        /* trả về đường dẫn để chuyển hướng */
        $reponse['url']         = $urlRedirect;
        return json_encode($reponse);
    }

    private static function getUrlRedirect($orderInfo){
        $urlRedirect   = '/';
        if(!empty($orderInfo->paymentMethod->code)){
            /* tạo yêu cầu thanh toán => nếu zalo pay */
            if($orderInfo->paymentMethod->code=='zalopay') $urlRedirect = \App\Http\Controllers\ZalopayController::create($orderInfo);
            /* tạo yêu cầu thanh toán => nếu momo */
            if($orderInfo->paymentMethod->code=='momo') $urlRedirect = \App\Http\Controllers\MomoController::create($orderInfo);
            /* tạo yêu cầu thanh toán => nếu paypal */
            if($orderInfo->paymentMethod->code=='paypal') $urlRedirect = \App\Http\Controllers\PaypalController::create($orderInfo);
            /* tạo yêu cầu thanh toán => nếu paypal */
            if($orderInfo->paymentMethod->code=='vnpay') $urlRedirect = \App\Http\Controllers\VNPayController::create($orderInfo);
            /* tạo yêu cầu thanh toán => nếu paypal */
            if($orderInfo->paymentMethod->code=='2checkout') $urlRedirect = \App\Http\Controllers\TwoCheckoutController::create($orderInfo);
        }
        return $urlRedirect;
    }

}
