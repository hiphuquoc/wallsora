@extends('layouts.admin')
@section('content')

<div class="card">
    <!-- ===== Table ===== -->
    <div class="table-responsive">
        @include('main.order.confirm', compact('order'))
    </div>
</div>
    
@endsection