@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh Sách Prompt</div>

@include('admin.prompt.search', compact('list'))

<div class="card">
    <!-- ===== Table ===== -->
    <div class="table-responsive">
        <table class="table table-bordered" style="min-width:900px;">
            <thead>
                <tr>
                    <th></th>
                    <th>Loại</th>
                    <th>Bảng</th>
                    <th>Cột</th>
                    <th width="120px">Công cụ</th>
                    <th width="120px">Phiên bản</th>
                    <th>Prompt</th>
                    <th class="text-center" width="200px">-</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($list)&&$list->isNotEmpty())
                    @foreach($list as $item)
                        <tr id="oneItem-{{ $item->id }}">
                            <td class="text-center">{{ ($loop->index + 1) }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->reference_table }}</td>
                            <td>{{ $item->reference_name }}</td>
                            <td>{{ $item->tool }}</td>
                            <td>{{ $item->version }}</td>
                            <td><div style="white-space: pre-line;">{{ $item->reference_prompt }}</div></td>
                            <td>
                                <div class="actionBoxOfList">
                                    <a href="{{ route('admin.prompt.view', ['language' => 'vi', 'id' => $item->id]) }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <div>Sửa</div>
                                    </a>
                                    <a href="{{ route('admin.prompt.view', ['id' => $item->id, 'language' => 'vi', 'type' => 'copy']) }}">
                                        <i class="fa-solid fa-copy"></i>
                                        <div>Chép</div>
                                    </a>
                                    <div class="actionDelete" onclick="deleteItem({{ $item->id }});">
                                        <i class="fa-solid fa-trash"></i>
                                        <div>Xóa</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="5">Không có dữ liệu phù hợp!</td></tr>
                @endif
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    {{ !empty($list&&$list->isNotEmpty()) ? $list->appends(request()->query())->links('admin.template.paginate') : '' }}
</div>

<!-- Nút thêm -->
<a href="{{ route('admin.prompt.view') }}" class="addItemBox">
    <i class="fa-regular fa-plus"></i>
    <span>Thêm</span>
</a>
    
@endsection
@push('scriptCustom')
    <script type="text/javascript">
        function deleteItem(id){
            if(confirm('{{ config("admin.alert.confirmRemove") }}')) {
                $.ajax({
                    url         : "{{ route('admin.prompt.delete') }}",
                    type        : "get",
                    dataType    : "html",
                    data        : { id }
                }).done(function(data){
                    if(data==true) $('#oneItem-'+id).remove();
                });
            }
        }
    </script>
@endpush