{{-- <div class="cartSectionBox">
    @if(!empty($orders)&&$orders->isNotEmpty())
    <div class="cartSectionBox_body">
        <div class="cartProductBox_head">
            <div>Đơn hàng</div>
            <div>Đã thanh toán</div>
        </div>
        <div class="cartProductBox_body">
            
            <div class="cartProductBox_body_item">
                
                <div class="downloadBox">
                    @foreach($orders as $order)
                    <div class="downloadBox_item">
                        <div class="downloadBox_item_order">
                            <div class="downloadBox_item_order_title">Đơn hàng thành công #{{ $order->code }}</div>
                            <div class="downloadBox_item_order_price">{{ number_format($order->total) }}{!! config('language.'.$language.'.currency') !!}</div>
                        </div>
                        <div class="downloadBox_item_download">
                            @foreach($order->products as $product)
                                @php
                                    $zipPath = $product->infoPrice->folder_drive.'-'.$product->infoProduct->seo->slug.'/'.$product->infoPrice->folder_drive.'-'.$product->infoProduct->seo->slug.'.zip';
                                @endphp
                                <a class="downloadBox_item_download_item" href="{{ Storage::disk('google')->url($zipPath) }}" target="_blank">
                                    <img src="">
                                    <div>{{ $product->infoProduct->name }} (link google drive .ZIP)</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>

            
        </div>
    </div>
    @else
    <div class="cartSectionBox_body">
        <div class="emptyCartBox">
            <img src="" alt="danh sách sản phẩm trong giỏ hàng" title="danh sách sản phẩm trong giỏ hàng">
            <div class="emptyCart_text">Tải xuống của bạn trống!</div> 
            <a href="/hinh-nen-dien-thoai" class="emptyCartBox_button button" aria-label="Bắt đầu mua sắm">Bắt đầu mua hàng</a>
        </div>
    </div>
    @endif
</div> --}}