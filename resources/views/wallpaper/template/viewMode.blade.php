<div class="viewMode">
    <div class="viewMode_show" onclick="closeLanguageBoxList('ja_closeViewBoxList');">
        <div class="viewMode_show_boxHeight">
            @php
                /* mặc định lấy icon đầu tiên */
                $dataViewMode   = config('main_'.env('APP_NAME').'.view_mode');
                if(!empty(request()->cookie('view_mode'))){
                    foreach($dataViewMode as $viewMode){
                        if(request()->cookie('view_mode')==$viewMode['key']){
                            $icon   = '<svg><use xlink:href="#'.$viewMode['icon'].'"></use></svg>';
                            break;
                        }
                    }
                }else {
                    $icon           = '<svg><use xlink:href="#'.$dataViewMode[0]['icon'].'"></use></svg>';
                }
            @endphp
            {!! $icon !!}
        </div>
    </div>
    <div id="ja_closeViewBoxList" class="viewMode_list">
        <div class="viewMode_list_title">{{ config('data_language_3.'.$language.'.view_mode_notes') }}</div>
        <div class="viewMode_list_close" onclick="closeLanguageBoxList('ja_closeViewBoxList');">
            <svg><use xlink:href="#icon_close"></use></svg>
        </div>
        <div class="viewMode_list_box">
            @foreach(config('main_'.env('APP_NAME').'.view_mode') as $viewMode)
                @php
                    $selected   = '';
                    $event      = 'onclick=setViewMode(\''.$viewMode['key'].'\')';
                    if(!empty(request()->cookie('view_mode'))){
                        if(request()->cookie('view_mode')==$viewMode['key']) {
                            $selected   = 'selected';
                        }
                    }else {
                        if($loop->index==0) {
                            $selected = 'selected';
                        }
                    }
                    
                @endphp
                <div class="viewMode_list_box_item {{ $viewMode['key'] }}Mode {{ $selected }}" {{ $event }}>
                    <svg><use xlink:href="#{{ $viewMode['icon'] }}"></use></svg>
                    <div>{{ config('data_language_3.'.$language.'.'.$viewMode['key'].'_mode') }}</div>
                </div>
            @endforeach
        </div>
    </div>
    <div id="ja_closeViewBoxList_background" class="viewMode_background"></div>
</div>

@push('scriptCustom')
    <script type="text/javascript">
        /* Chạy khi trang tải xong */
        document.addEventListener('DOMContentLoaded', function () {
            // Lấy chế độ xem từ localStorage (nếu có)
            const savedViewMode = localStorage.getItem('viewMode');

            // Nếu có chế độ xem đã lưu, áp dụng nó
            if (savedViewMode) {
                setViewMode(savedViewMode);
            }
        });
        /* Thiết lập chế độ xem */
        function setViewMode(viewMode) {
            // Lưu chế độ xem vào localStorage
            localStorage.setItem('viewMode', viewMode);

            // Gửi yêu cầu AJAX để cập nhật session PHP
            $.ajax({
                url: '{{ route("main.setViewMode") }}',
                type: 'get',
                dataType: 'json',
                data: {
                    view_mode: viewMode
                },
                success: function(response) {
                    // Cập nhật class của thẻ <html>
                    document.documentElement.className = viewMode;

                    // Cập nhật trạng thái "selected" cho các chế độ xem
                    const viewModeItems = document.querySelectorAll('.viewMode_list_box_item');
                    viewModeItems.forEach(item => {
                        item.classList.remove('selected');
                        if (item.classList.contains(viewMode + 'Mode')) {
                            item.classList.add('selected');
                        }
                    });

                    // Cập nhật icon chế độ xem hiện tại
                    updateViewModeIcon(viewMode);

                    // đóng modal 
                    closeLanguageBoxList('ja_closeViewBoxList', 'close');
                }
            });
        }

        /* Cập nhật icon chế độ xem */
        function updateViewModeIcon(viewMode) {
            const iconContainer = document.querySelector('.viewMode_show_boxHeight svg use');
            if (iconContainer) {
                const iconKey = getViewModeIconKey(viewMode);
                iconContainer.setAttribute('xlink:href', `#${iconKey}`);
            }
        }

        /* Lấy key của icon dựa trên chế độ xem */
        function getViewModeIconKey(viewMode) {
            const viewModes = @json(config('main_'.env('APP_NAME').'.view_mode'));
            const selectedMode = viewModes.find(mode => mode.key == viewMode);
            return selectedMode ? selectedMode.icon : viewModes[0].icon;
        }
    </script>
@endpush