@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh Sách Sản phẩm còn thiếu ngôn ngữ</div>

@include('admin.product.searchLanguageNotExists', compact('list'))

<div class="card">
    <!-- ===== Table ===== -->
    <div class="table-responsive">
        <table class="table table-bordered" style="min-width:900px;">
            <thead>
                <tr>
                    <th style="width:60px;"></th>
                    <th style="width:320px;">Ảnh</th>
                    <th class="text-center">Thông tin</th>
                    <th class="text-center" style="width:225px;">Khác</th>
                    <th class="text-center" width="200px">-</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($list)&&$list->isNotEmpty())
                    @foreach($list as $item)
                        @include('admin.product.rowLanguageNotExists', [
                            'item'  => $item,
                            'no'    => $loop->index+1,
                            'typeRoute' => 'product',
                        ])
                    @endforeach
                @else
                    <tr><td colspan="5">Tuyệt vời, tất cả Sản phẩm đã đủ ngôn ngữ!</td></tr>
                @endif
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    {{ !empty($list&&$list->isNotEmpty()) ? $list->appends(request()->query())->links('admin.template.paginate') : '' }}
</div>
    
@endsection