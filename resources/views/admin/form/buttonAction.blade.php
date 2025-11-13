<!-- nút save -->
@include('admin.button.save')

<!-- remove language -->
@include('admin.button.removeLanguage')

<div class="pageAdminWithRightSidebar_main_rightSidebar_item">
    @include('admin.category.action', compact('item', 'itemSeo', 'prompts', 'language'))
</div>