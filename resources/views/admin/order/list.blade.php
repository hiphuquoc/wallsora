@extends('layouts.admin')
@section('content')

<div class="titlePage">Danh Sách Đơn Hàng</div>

@include('admin.order.search', compact('list'))

<div class="card">
    <!-- ===== Table ===== -->
    <div class="table-responsive">
        <table class="table table-bordered" style="min-width:900px;">
            <thead>
                <tr>
                    <th style="width:60px;"></th>
                    <th class="text-center" style="width:180px;">Trạng thái</th>
                    <th class="text-center">Khách hàng</th>
                    <th class="text-center" style="width:500px;">Chi tiết</th>
                    <th class="text-center" width="200px">-</th>
                 </tr>
            </thead>
            <tbody>
                @if(!empty($list)&&$list->isNotEmpty())
                    @foreach($list as $item)
                    <tr id="booking_425">
                        <td class="text-center">{{ $loop->index + 1 }}</td>
                        <td style="text-align:center;">
                            <div style="margin-bottom:0.25rem;">{{ date('H:i d/m/Y') }}</div>
                            <div class="badge" style="font-size:0.95rem;background:{{ $item->status->color ?? '#283747' }}">{{ $item->status->name ?? 'Chờ xác nhận' }}</div>
                        </td>
                        <td>
                            {{-- <div class="oneLine">
                                {{ $item->customer->name ?? null }} - {{ $item->customer->phone ?? null }}
                            </div> --}}
                            @php
                                $fullAddress = [];
                                if(!empty($item->address)) $fullAddress[] = $item->address;
                                if(!empty($item->district->district_name)) $fullAddress[] = $item->district->district_name;
                                if(!empty($item->province->province_name)) $fullAddress[] = $item->province->province_name;
                                $fullAddress = implode(', ', $fullAddress);
                            @endphp
                            @if(!empty($fullAddress))
                                <div class="oneLine">
                                    {{ $fullAddress }}
                                </div>
                            @endif
                            <div class="oneLine">
                                Tổng tiền: <span class="highLight">{{ number_format($item->total) }}$</span> - {{ $item->paymentMethod->name ?? null }}
                            </div>
                        </td>
                        <td>
                            <div class="productListBox">
                                @foreach($item->products as $product)
                                    <div class="productListBox_item">
                                        <div class="productListBox_item_image">
                                            @php
                                                $image = !empty($product->infoPrice->files[0]->file_path) ? Storage::url($product->infoPrice->files[0]->file_path) : config('image.default');
                                                $title = $product->infoProduct->name ?? $product->infoProduct->seo->title ?? null;
                                            @endphp
                                            <img src="{{ $image }}" />
                                            <div class="productListBox_item_image_quantity">{{ $product->quantity }}</div>
                                        </div>
                                        <div class="productListBox_item_content">
                                            <div class="productListBox_item_content_title maxLine_2">
                                                {{ $title }}
                                            </div>
                                            <div class="productListBox_item_content_option">
                                                {{ $product->infoPrice->name ?? 'Không xác định' }} / {!! number_format($product->price).'USD' !!}
                                            </div>
                                        </div>
                                        <div class="productListBox_item_price">
                                            {!! number_format($product->price*$product->quantity).'USD' !!}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td style="vertical-align:top;display:flex;">
                            <div class="icon-wrapper iconAction">
                                <a href="{{ route('admin.order.viewExport', ['id' => $item->id]) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <div>Xem</div>
                                </a>
                            </div>
                            <div class="icon-wrapper iconAction">
                                <a href="https://hitour.vn/admin/booking/425/view">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    <div>Sửa</div>
                                </a>
                            </div>
                            <div class="icon-wrapper iconAction">
                                <div class="actionDelete" onclick="deleteItem('425');">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-square">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                    </svg>
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
<a href="{{ route('admin.order.view') }}" class="addItemBox">
    <i class="fa-regular fa-plus"></i>
    <span>Thêm</span>
</a>
    
@endsection
@push('scripts-custom')
    <script type="text/javascript">
    
    </script>
@endpush