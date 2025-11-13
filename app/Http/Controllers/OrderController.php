<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Services\BuildInsertUpdateModel;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\RelationOrderInfoProductInfo;
use App\Models\RelationOrderInfoWallpaperInfo;

class OrderController extends Controller{

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public function create($params){
        try {
            DB::beginTransaction();
            /* kiểm tra customer tồn tại chưa => chưa thì tạo customer_info */
            $idCustomer             = 0;
            if(!empty($params['email'])){
                $infoCustomer       = Customer::select('*')
                                        ->where('email', $params['email'])
                                        ->first();
                if(!empty($infoCustomer)){
                    $idCustomer     = $infoCustomer->id;
                }else {
                    $idCustomer     = Customer::insertItem([
                        'name'  => $params['name'] ?? '',
                        'phone' => $params['phone'] ?? '',
                        'zalo'  => $params['zalo'] ?? '',
                        'email' => $params['email'],
                    ]);
                }
            }
            /* tạo order_info */
            $language       = $params['language_sub'] ?? session()->get('language');
            if($params['payment_type']=='payment_cart'){ /* thanh toán giỏ hàng */
                $idOrder = $this->createOrderByPaymentCart($idCustomer, $params, $language);
            }else { /* thanh toán ngay (1 sản phẩm) */
                $idOrder = $this->createOrderByPaymentNow($idCustomer, $params, $language);
            }
            DB::commit();
            return $idOrder;
        } catch (\Exception $exception){
            DB::rollBack();
        }
    }

    private function createOrderByPaymentCart($idCustomer, $params, $language){
        $productsCarts      = session()->get('cart');
        $productsCarts      = json_decode($productsCarts, true);
        $idPaymentMethod    = $params['payment_method_info_id'];
        $detailCart         = \App\Http\Controllers\CartController::calculatorDetailCart($productsCarts, $idPaymentMethod, $language);
        $insertOrder        = $this->BuildInsertUpdateModel->buildArrayTableOrderInfo($params, $idCustomer, $detailCart);
        $idOrder            = Order::insertItem($insertOrder);
        /* tạo relation_order_info_product_info */
        $this->insertAllRelationOfOrder($productsCarts, $idOrder);
        return $idOrder;
    }

    private function createOrderByPaymentNow($idCustomer, $params, $language){
        $arrayPrice         = explode('-', $params['product_price_id']);
        $idProduct          = $params['product_info_id'];
        $idPaymentMethod    = $params['payment_method_info_id'];
        /* giả lập cart */
        $type               = count($arrayPrice)==1 ? 'per' : 'all'; /* ở đây chỉ có 2 trường hợp xảy ra -> mua nguyên set thì $arrayPrice bằng số lượng product_price - trường hợp thứ 2 mua 1 ảnh thì $arrayPrice chỉ có 1 phần tử */
        $productsCarts      = [
            [
                'product_info_id'   => $idProduct,
                'product_price_id'  => $arrayPrice,
                'type'              => $type,
            ]
        ];
        $detailCart         = \App\Http\Controllers\CartController::calculatorDetailCart($productsCarts, $idPaymentMethod, $language);
        $insertOrder        = $this->BuildInsertUpdateModel->buildArrayTableOrderInfo($params, $idCustomer, $detailCart);
        $idOrder            = Order::insertItem($insertOrder);
        /* tạo relation_order_info_product_info */
        $this->insertAllRelationOfOrder($productsCarts, $idOrder);
        return $idOrder;
    }

    private function insertAllRelationOfOrder($productsCarts, $idOrder){
        foreach($productsCarts as $product){
            /* tính giá của từng sản phẩm trong cart */
            $idProduct      = $product['product_info_id'];
            if($product['type']=='all'){ /* option tất cả => giá của sản phẩm */
                $tmp        = Product::select('price')
                                ->where('id', $idProduct)
                                ->first();
                $price      = $tmp['price'];
            }else { /* mua sản phẩm lẻ => giả bằng tổng giá của từng product_price */
                $price      = 0;
                foreach($product['product_price_id'] as $idProductPrice){
                    $tmp    = ProductPrice::select('price')
                                ->where('id', $idProductPrice)
                                ->first();
                    if(!empty($tmp)) $price  += $tmp['price'];
                }
            }
            /* tiến hành insert relation_order_info_product_info */
            $insertData = [
                'order_info_id'     => $idOrder,
                'product_info_id'   => (int) $idProduct,
                'product_price_id'  => implode('-', $product['product_price_id']),
                'quantity'          => 1,
                'price'             => (int) $price,
            ];
            RelationOrderInfoProductInfo::insertItem($insertData);
            /* insert relation_order_info_wallpaper_info */
            foreach($product['product_price_id'] as $idProductPrice){
                $tmp            = ProductPrice::select('*')
                                    ->where('id', $idProductPrice)
                                    ->with('wallpapers')
                                    ->first();
                foreach($tmp->wallpapers as $wallpaper){
                    RelationOrderInfoWallpaperInfo::insertItem([
                        'order_info_id' => $idOrder,
                        'wallpaper_info_id'     => $wallpaper->infoWallpaper->id,
                    ]);
                }
            }
        }
    }
}
