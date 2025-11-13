<div class="actionBox">
    @if($language=='vi')

        {{-- <!-- nut viết toàn trang (trực quan) -->
        @include('admin.button.writeAll') --}}

        <!-- tạo trang ngôn ngữ --> 
        @include('admin.button.translatePage')

        <!-- nut viết content (tchạy ngầm) -->
        @include('admin.button.writeContent')

        <!-- dịch content (theo tùy chọn) -->
        @include('admin.button.translateContentWithOption')

    @else   

        {{-- <!-- dịch toàn trang (trực quan) -->
        @include('admin.button.translateAll') --}}

        <!-- dịch content trang này (chạy ngầm) -->
        @include('admin.button.translateContentThisLanguage')

    @endif

    <!-- copy sang trang con -->
    @include('admin.button.copyAllToPageChild')

    <!-- copy từ trang cha (dành cho trang gốc) -->
    @include('admin.button.copyAllFromPageParent')

</div>