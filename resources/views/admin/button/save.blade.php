<div class="pageAdminWithRightSidebar_main_rightSidebar_item buttonAction">
    @if(!empty($itemSeo->slug_full))
        <a href="/{{ $itemSeo->slug_full }}" target="_blank" style="font-size:1.4rem;"><i class="fa-regular fa-eye"></i></a>
    @endif
    <a href="{{ route($routeBack) }}" type="button" class="btn btn-secondary waves-effect waves-float waves-light">Quay lại</a>
    <button type="submit" class="btn btn-success waves-effect waves-float waves-light" aria-label="Lưu">Lưu</button>
    <div class="form-check form-check-success" style="margin-top:0.75rem;">
        <input type="checkbox" class="form-check-input" id="index_google" name="index_google" />
        <label class="form-check-label" for="index_google">Báo google index</label>
    </div>
</div>