@php
    $paymentMethods     = \App\Models\PaymentMethod::select('*')
                            ->where('active', 1)
                            ->get();
@endphp
@foreach($paymentMethods as $method)
    @php
        // Kiểm tra điều kiện lọc: nếu ngôn ngữ không phải 'vi', chỉ hiển thị phương thức có code là 'paypal'
        if ($language != 'vi' && $method->code != 'paypal') {
            continue; // Bỏ qua các phương thức không thỏa mãn điều kiện
        }

        // Xác định tên và mô tả dựa trên ngôn ngữ
        $nameMethodPayment          = empty($language) || $language == 'vi' ? $method->name : $method->en_name;
        $descriptionMethodPayment   = empty($language) || $language == 'vi' ? $method->description : $method->en_description;
    @endphp
    <div class="paymentMethodBox_item" onclick="{{ $action }}(this, {{ $method->id }});">
        <div class="paymentMethodBox_item_logo">
            <img src="{{ Storage::url($method->icon) }}" alt="{{ $nameMethodPayment ?? null }}" title="{{ $nameMethodPayment ?? null }}" />
        </div>
        <div class="paymentMethodBox_item_content">
            <div class="paymentMethodBox_item_content_title">{{ $nameMethodPayment ?? null }}</div>
            <div class="paymentMethodBox_item_content_desc maxLine_1">{{ $descriptionMethodPayment ?? null }}</div>
            @if(!empty($method->fee))
                <div class="paymentMethodBox_item_content_fee">
                    {{ config('data_language_1.'.$language.'.fee') }} +
                    {!! \App\Helpers\Number::getFormatPriceByLanguage($method->fee, $language) !!}
                </div>
            @endif
        </div>
    </div>
@endforeach

@pushonce('scriptCustom')
    <script type="text/javascript">
        function chooseOptionPayment(element, idMethod){
            const parentElement = $(element).parent();
            /* xóa selected tất cả phần tử */
            parentElement.children().each(function(){
                if($(this).hasClass('selected')) $(this).removeClass('selected');
            })
            /* bật lại cho phần tử được click */
            $(element).addClass('selected');
            $('#payment_method_info_id').val(idMethod);
            /* tải lại tổng tiền */
            loadTotalCart(idMethod);
        }

        function loadTotalCart(idMethod){
            const language = $('#language').val();
            $.ajax({
                url         : '{{ route("main.loadTotalCart") }}',
                type        : 'get',
                dataType    : 'html',
                data        : {
                    payment_method_info_id : idMethod,
                    language,
                },
                success     : function(response){
                    $('#js_loadTotalCart').html(response);
                }
            });
        }
    </script>
@endpushonce