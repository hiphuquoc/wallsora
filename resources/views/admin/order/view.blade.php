@extends('layouts.admin')
@section('content')

    {{-- <div class="titlePage">Danh sách yêu cầu</div>
    @include('admin.order.search')
    @include('admin.order.test', compact('list')) --}}

    @include('admin.order.contentView')
    
@endsection