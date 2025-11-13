<form id="formSearch" method="get" action="{{ route('admin.translate.list') }}">
    <div class="searchBox">
        {{-- <div class="searchBox_item">
            <div class="input-group">
                <input type="text" class="form-control" name="search_name" placeholder="Tìm theo tên" value="{{ $params['search_name'] ?? null }}">
                <button class="btn btn-primary waves-effect" id="button-addon2" type="submit" aria-label="Tìm">Tìm</button>
            </div>
        </div> --}}
        <div class="searchBox_item">
            <div class="position-relative">
                @php
                    $arrayDataSelect = [
                        0 => 'Chưa hoàn thành',
                        1 => 'Hoàn thành',
                    ];
                    $selectedStatus = request()->get('search_status');
                @endphp
                <select class="form-select select2 select2-hidden-accessible" name="search_status" onchange="submitForm('formSearch');" aria-hidden="true">
                    @foreach($arrayDataSelect as $key => $value)
                        <option value="{{ $key }}" {{ $selectedStatus == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="searchBox_item">
            <div class="position-relative">
                @php
                    $tmp = config('language');
                    $arrayDataSelect = [];
                    foreach($tmp as $key => $t){
                        $arrayDataSelect[$key]['key'] = $key;
                        $arrayDataSelect[$key]['name_by_language'] = $t['name_by_language'];
                    }
                    $selectedStatus = request()->get('search_language');
                @endphp
                <select class="form-select select2 select2-hidden-accessible" name="search_language" onchange="submitForm('formSearch');" aria-hidden="true">
                    <option value="0">- Tìm theo Ngôn ngữ -</option>
                    @foreach($arrayDataSelect as $value)
                        <option value="{{ $value['key'] }}" {{ $selectedStatus == $value['key'] ? 'selected' : '' }}>{{ $value['key'] }} - {{ $value['name_by_language'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="searchBox_item">
            <div class="position-relative button button-primary" onclick="autoTranslateMissing();">
                Dịch box còn thiếu
            </div>
        </div>
        <div class="searchBox_item" style="margin-left:auto;text-align:right;">
            @php
                $xhtmlSettingView   = \App\Helpers\Setting::settingView('viewTranslateReport', config('setting.admin_array_number_view'), $viewPerPage, $list->total());
                echo $xhtmlSettingView;
            @endphp
        </div>
    </div>
</form>

@push('scriptCustom')
    <script type="text/javascript">
        function autoTranslateMissing(){
            if(confirm('{{ config("admin.alert.confirmRemove") }}')) {

                // Gửi dữ liệu qua AJAX
                openCloseFullLoading();
                
                $.ajax({
                    url         : '{{ route("admin.translate.autoTranslateMissing") }}',
                    type        : 'post',
                    dataType    : 'json',
                    data        : {
                        "_token": "{{ csrf_token() }}",
                    }
                })
                .done(function(response) {
                    // Hiển thị Toast từ response
                    createToast(response.toast_type, response.toast_title, response.toast_message);
                    $('#lock').css('display', 'block');
                })
                .fail(function() {
                    // Hiển thị thông báo lỗi mặc định
                    createToast('error', 'Thất bại', '❌ Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.');
                })
                .always(function() {
                    setTimeout(() => openCloseFullLoading(), 300);
                });

            }
        }
    </script>
@endpush