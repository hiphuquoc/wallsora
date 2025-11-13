{{-- <div class="productDetailBox_detail_rating">
    <div class="ratingBox">
        @if(!empty($item->sold))
            <div class="ratingBox_numberSell">
                Đã bán <span>{{ $item->sold }}</span>
            </div>
        @endif
        @if(!empty($item->seo->rating_aggregate_star)&&!empty($item->seo->rating_aggregate_count))
            <div class="ratingBox_star">
                <div class="ratingBox_star_box">
                    <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                    <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                    <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                    <span class="ratingBox_star_box_on"><i class="fas fa-star"></i></span>
                    <span class="ratingBox_star_box_on"><i class="fas fa-star-half-alt"></i></span>
                </div>
                <div class="ratingBox_star_total">
                    @if(empty($language)||$language=='vi')
                        <span>{{ $item->seo->rating_aggregate_star }}</span> sao/<span>{{ $item->seo->rating_aggregate_count }}</span> đánh giá
                    @else 
                        <span>{{ $item->seo->rating_aggregate_star }}</span> star/<span>{{ $item->seo->rating_aggregate_count }}</span> votes
                    @endif
                </div>
            </div>
        @endif
    </div>
</div> --}}