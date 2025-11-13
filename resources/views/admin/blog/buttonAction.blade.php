<!-- nút save -->
@include('admin.button.save')

<!-- remove language -->
@include('admin.button.removeLanguage')

<div class="pageAdminWithRightSidebar_main_rightSidebar_item">
    <div class="actionBox">
        @if($language=='vi')

            <div class="actionBox_item maxLine_1" data-bs-toggle="modal" data-bs-target="#modalChooseProduct" onclick="loadProduct(), loadThemeProductChoosed()">
                <i class="fa-solid fa-robot"></i>Viết bài Gợi ý
            </div>

            <!-- tạo trang ngôn ngữ --> 
            @include('admin.button.translatePage')

            <!-- dịch content (những trang chưa có) -->
            @include('admin.button.translateContentWithOption')

        @else   

            <!-- dịch toàn trang (trực quan) -->
            @include('admin.button.translateAll')

            <!-- dịch content trang này (chạy ngầm) -->
            @include('admin.button.translateContentThisLanguage')

        @endif
        
        <!-- copy sang trang con -->
        @include('admin.button.copyAllToPageChild')

        <!-- copy từ trang cha (dành cho trang gốc) -->
        @include('admin.button.copyAllFromPageParent')

    </div>
</div>