@extends('layouts.admin')
@section('content')

    {{-- <div class="titlePage">Danh sách yêu cầu</div>
    @include('admin.prompt.search')
    @include('admin.prompt.test', compact('list')) --}}

    @include('admin.prompt.contentView')
    
@endsection
@push('scripts-custom')
    <script type="text/javascript">
    
    </script>
@endpush