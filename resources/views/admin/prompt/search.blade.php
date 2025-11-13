<form id="formSearch" method="get" action="{{ route('admin.prompt.list') }}">
    <div class="searchBox">
        <div class="searchBox_item">
            <div class="input-group">
                <input type="text" class="form-control" name="search_name" placeholder="Tìm theo tên" value="{{ $params['search_name'] ?? null }}">
                <button class="btn btn-primary waves-effect" id="button-addon2" type="submit" aria-label="Tìm">Tìm</button>
            </div>
        </div>
        <div class="searchBox_item" style="margin-left:auto;text-align:right;">
            @php
                $xhtmlSettingView   = \App\Helpers\Setting::settingView('viewPromptInfo', config('setting.admin_array_number_view'), $viewPerPage, $list->total());
                echo $xhtmlSettingView;
            @endphp
        </div>
    </div>
</form>