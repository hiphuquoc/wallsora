<div class="formBox">
    <div class="formBox_full">
        <!-- One Row -->
        <div class="formBox_full_item">
            <label class="form-label inputRequired" for="type">Loại hành động</label>
            <select class="select2 form-select select2-hidden-accessible" id="type" name="type" data-minimum-results-for-search="-1">
                @foreach(config('ai.type_action') as $typeAction)
                    @php
                        $selected   = null;
                        if(!empty($item->type)&&$item->type==$typeAction['key']) $selected = ' selected';
                    @endphp
                    <option value="{{ $typeAction['key'] }}"{{ $selected }}>{{ $typeAction['name'] }}</option>
                @endforeach
            </select>
        </div>
        <!-- One Row -->
        <div class="formBox_full_item">
            <label class="form-label inputRequired" for="reference_table">Thuộc bảng</label>
            <select class="select2 form-select select2-hidden-accessible" id="reference_table" name="reference_table" data-minimum-results-for-search="-1" onchange="loadColumnTable(this, 'reference_name');">
                @foreach($tables as $table)
                    @php
                        $selected   = null;
                        if(!empty($item->reference_table)&&$item->reference_table==$table) $selected = ' selected';
                    @endphp
                    <option value="{{ $table }}"{{ $selected }}>{{ $table }}</option>
                @endforeach
            </select>
        </div>
        <!-- One Row -->
        <div class="formBox_full_item">
            <label class="form-label inputRequired" for="reference_name">Tên cột</label>
            <select class="select2 form-select select2-hidden-accessible" id="reference_name" name="reference_name" data-minimum-results-for-search="-1">
                {{-- @foreach($tables as $table)
                    @php
                        $selected   = null;
                        if(!empty($item->reference_name)&&$item->reference_name==$table) $selected = ' selected';
                    @endphp
                    <option value="{{ $table }}"{{ $selected }}>{{ $table }}</option>
                @endforeach --}}
            </select>
        </div>
        <!-- One Row -->
        <div class="formBox_full_item">
            <label class="form-label inputRequired" for="tool">Công cụ</label>
            <select class="select2 form-select select2-hidden-accessible" id="tool" name="tool">
                @foreach(config('main_'.env('APP_NAME').'.tool_translate') as $t)
                    @php
                        $selected   = null;
                        if(!empty($item->tool)&&$item->tool==$t) $selected = ' selected';
                    @endphp
                    <option value="{{ $t }}"{{ $selected }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <!-- One Row -->
        <div class="formBox_full_item">
            <label class="form-label inputRequired" for="version">Phiên bản</label>
            <select class="select2 form-select select2-hidden-accessible" id="version" name="version">
                @foreach(config('main_'.env('APP_NAME').'.ai_version') as $v)
                    @php
                        $selected   = null;
                        if(!empty($item->version)&&$item->version==$v) $selected = ' selected';
                    @endphp
                    <option value="{{ $v }}"{{ $selected }}>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <!-- One Row -->
        <div class="formBox_column2_item_row">
            <div class="inputWithNumberChacractor">
                <span data-toggle="tooltip" data-placement="top" title="
                    Cấu trúc #title để lấy tên của bảng thay vào câu prompt trong lúc chương trình thực thi
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label inputRequired" for="reference_prompt">Prompt</label>
                </span>
            </div>
            <textarea class="form-control" id="reference_prompt"  name="reference_prompt" rows="20" required>{{ old('reference_prompt') ?? $item->reference_prompt ?? null }}</textarea>
            <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
        </div>
    </div>
</div>

@push('scriptCustom')
    <script type="text/javascript">

        document.addEventListener('DOMContentLoaded', function() {
            loadColumnTable($('#reference_table') , 'reference_name');
        });

        function loadColumnTable(input, idWrite){
            const table_name        = $(input).val();
            const prompt_info_id    = $('#prompt_info_id').val();
            $.ajax({
                url         : '{{ route("admin.prompt.loadColumnTable") }}',
                type        : 'get',
                dataType    : 'html',
                data        : {
                    prompt_info_id, table_name
                }
            }).done(function(data) {
                // Xóa các option cũ trong select box
                $('#'+idWrite).html(data)
            }).fail(function(xhr, status, error) {
                // Xử lý lỗi nếu có
                console.error(error);
            });
        }
    </script>
@endpush