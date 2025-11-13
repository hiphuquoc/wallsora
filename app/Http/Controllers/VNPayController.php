<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Order;

class VNPayController extends Controller{

    public static function create($infoOrder){
        $urlRedirect    = null;
        if(!empty($infoOrder)){
            $vnp_Url        = config('payment.vnpay.endpoint');
            $vnp_TmnCode    = config('payment.vnpay.access_key'); //Mã website tại VNPAY 
            $vnp_HashSecret = config('payment.vnpay.secret_key'); //Chuỗi bí mật
            $vnp_Returnurl  = route('main.handlePaymentVNPay', ['code' => $infoOrder->code]);
            $language       = 'vi';
            $currencyCode   = 'VND';
            /* tổng tiền (không có phí thanh toán) */
            $tmp        = \App\Helpers\Number::getPriceByLanguage($infoOrder->total, $language);
            $total      = $tmp['number'] ?? 0;

            if(!empty($total)&&$total>0){
                $vnp_TxnRef     = $infoOrder->code;
                $vnp_OrderInfo  = ucfirst(env('APP_NAME')).' - Thanh toán đơn hàng '.$infoOrder->code;
                $vnp_OrderType  = 'billpayment';
                $vnp_Amount     = $total * 100; /* bắt buộc phải * 100 để ra số tiền đúng */
                $vnp_Locale     = $language;
                $vnp_IpAddr     = $_SERVER['REMOTE_ADDR'];
                $inputData = array(
                    "vnp_Version"       => "2.1.0",
                    "vnp_TmnCode"       => $vnp_TmnCode,
                    "vnp_Amount"        => $vnp_Amount,
                    "vnp_Command"       => "pay",
                    "vnp_CreateDate"    => date('YmdHis'),
                    "vnp_CurrCode"      => $currencyCode,
                    "vnp_IpAddr"        => $vnp_IpAddr,
                    "vnp_Locale"        => $vnp_Locale,
                    "vnp_OrderInfo"     => $vnp_OrderInfo,
                    "vnp_OrderType"     => $vnp_OrderType,
                    "vnp_ReturnUrl"     => $vnp_Returnurl,
                    "vnp_TxnRef"        => $vnp_TxnRef,
                );
                
                if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                    $inputData['vnp_BankCode'] = $vnp_BankCode;
                }
                if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
                    $inputData['vnp_Bill_State'] = $vnp_Bill_State;
                }
                
                ksort($inputData);
                $query = "";
                $i = 0;
                $hashdata = "";
                foreach ($inputData as $key => $value) {
                    if ($i == 1) {
                        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                    } else {
                        $hashdata .= urlencode($key) . "=" . urlencode($value);
                        $i = 1;
                    }
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }
                
                $vnp_Url = $vnp_Url . "?" . $query;
                if (isset($vnp_HashSecret)) {
                    $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
                }
                /* trả đường dẫn redirect */
                $urlRedirect = $vnp_Url;
            }
        }
        return $urlRedirect;
    }

    public function handleIPN(Request $request){ /* url_IPN để VNPay gọi qua check xem đơn hàng xác nhận chưa => trong trường hợp mạng khách hàng có vấn đề */
        // Lấy tất cả dữ liệu từ VNPAY gửi về
        $inputData      = $request->all();
        $returnData     = [];

        // Lấy vnp_SecureHash và loại bỏ khỏi mảng inputData
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);

        // Sắp xếp dữ liệu theo thứ tự bảng chữ cái
        ksort($inputData);
        $hashData = urldecode(http_build_query($inputData));

        // Hash lại dữ liệu với vnp_HashSecret
        $vnp_HashSecret = config('payment.vnpay.secret_key');
        $secureHash     = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        try {
            if ($secureHash == $vnp_SecureHash) {
                // Lấy thông tin giao dịch
                $orderId = $inputData['vnp_TxnRef'] ?? null;
                $vnp_Amount = $inputData['vnp_Amount'] / 100; // Chuyển đổi đơn vị
                $responseCode = $inputData['vnp_ResponseCode'] ?? null;
                $transactionStatus = $inputData['vnp_TransactionStatus'] ?? null;

                // Tìm đơn hàng trong cơ sở dữ liệu
                $order = Order::where('code', $orderId)->first();

                if (!empty($order)) {
                    // Kiểm tra số tiền có khớp không
                    if ((float)$order->total == (float)$vnp_Amount) {
                        // Kiểm tra trạng thái giao dịch
                        if ($order->payment_status == 0) {
                            if ($responseCode == '00' && $transactionStatus == '00') {
                                // Cập nhật trạng thái thanh toán thành công
                                $order->update([
                                    'payment_status'    => 1, // 1: Giao dịch thành công
                                    'trans_id'          => $inputData['vnp_TransactionNo'] ?? null,
                                ]);

                                $returnData['RspCode']  = '00';
                                $returnData['Message']  = 'Confirm Success';
                            } else {
                                // Xử lý giao dịch thất bại
                                $order->update(['payment_status' => 0]); // 0: Giao dịch thất bại
                                $returnData['RspCode'] = '00';
                                $returnData['Message'] = 'Payment Failed';
                            }
                        } else {
                            $returnData['RspCode'] = '02';
                            $returnData['Message'] = 'Order already confirmed';
                        }
                    } else {
                        $returnData['RspCode'] = '04';
                        $returnData['Message'] = 'Invalid amount';
                    }
                } else {
                    $returnData['RspCode'] = '01';
                    $returnData['Message'] = 'Order not found';
                }
            } else {
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Invalid signature';
            }
        } catch (\Exception $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknown error';
        }

        // Trả về kết quả JSON cho VNPAY
        return response()->json($returnData);
    }
}
