{{-- @php
    /* trang ve-chung-toi */
    $pageAboutUs = \App\Models\Page::select('*')
                    ->whereHas('seo', function($query){
                        $query->where('slug', 've-chung-toi');
                    })
                    ->with('seo', 'seos')
                    ->first();
    /* chủ đề */
    $wallpaperMobile            = [];
    $tmp                        = \App\Models\Category::getTreeCategory();
    foreach($tmp as $categoryLv1){
        if($categoryLv1->seo->level==1){
            $wallpaperMobile    = $categoryLv1;
            break;
        }
    }
    /* trang chính sách */
    $policies                   = \App\Models\Page::select('page_info.*')
                                    ->join('seo', 'seo.id', '=', 'page_info.seo_id')
                                    ->whereHas('type', function($query){
                                        $query->where('code', 'policy');
                                    })
                                    ->orderBy('seo.ordering', 'DESC')
                                    ->orderBy('seo.id', 'ASC')
                                    ->with('seo', 'seos')
                                    ->get();
    /* chuyên mục blog */
    $categoriesBlog             = \App\Models\CategoryBlog::getTreeCategory();
    /* url hiện tại */
    $urlPath                    = urldecode(request()->path());
@endphp              --}}
<div class="logoInMenuMobile show-991">
    <div class="logoMain">
        <a href="/{{ $language }}" class="logoMain_show" aria-label="{{ config('data_language_1.'.$language.'.home') }} {{ env('DOMAIN_NAME') }}"></a>
    </div>
</div>
<!-- icon hiển thị chế độ xem -->
<div class="layoutHeaderSide_header_menuView" onclick="settingCollapsedMenu();">
    <svg><use xlink:href="#icon_setting_view"></use></svg>
</div>

<div class="headerSide customScrollBar-y">
    <ul>
        <!-- trang chủ -->
        @php
            $selected       = '';
        @endphp
        <li class="{{ $selected }}">
            <a href="/{{ $language }}" title="{{ config('data_language_1.'.$language.'.home').' '.config('main_'.env('APP_NAME').'.company_name') }}" aria-label="{{ config('data_language_1.'.$language.'.home') }} {{ env('DOMAIN_NAME') }}">
                <svg><use xlink:href="#icon_home"></use></svg>
                <div class="maxLine_1">{{ config('data_language_1.'.$language.'.home') }}</div>
            </a>
        </li>
        <!-- về chúng tôi -->
        @php
            $urlAbotUs      = '';
            $nameAboutUs    = '';
            $selected       = '';
            foreach($pageAboutUs->seos as $s){
                if(!empty($s->infoSeo->language)&&$s->infoSeo->language==$language){
                    $urlAbotUs      = $s->infoSeo->slug_full;
                    $nameAboutUs    = $s->infoSeo->title;
                    if($urlAbotUs==$urlPath) $selected = 'selected';
                }
            }
        @endphp
        <li class="{{ $selected }}">
            <a href="/{{ $urlAbotUs }}" title="{{ $nameAboutUs }}" aria-label="{{ $nameAboutUs }}">
                <svg><use xlink:href="#icon_about_me"></use></svg>
                <div class="maxLine_1">{{ $nameAboutUs }}</div>
            </a>
        </li>
        <!-- chủ đề -->
        @if(!empty($wallpaperMobile))
            @php
                $titlePhoneWallpaper = config('data_language_2.'.$language.'.wallpaper_theme');
                $url      = '';
                foreach($wallpaperMobile->seos as $s){
                    if(!empty($s->infoSeo->language)&&$s->infoSeo->language==$language){
                        $url    = $s->infoSeo->slug_full;
                        break;
                    }
                }
                $classTmp       = 'close';
                $classActive    = '';
                $classStatusMenu = '';
                $selected       = '';
                $flagOpen       = $url==$urlPath ? true : false;
                if($flagOpen==true){
                    $classTmp       = 'open';
                    $classActive    = 'active';
                    $classStatusMenu = 'isOpen';
                    $selected       = 'selected';
                }
            @endphp
            <li class="{{ $selected }}">
                <div class="{{ $classTmp }}" onclick="showHideListMenuMobile(this, '{{ $url }}')">
                    <svg><use xlink:href="#icon_category"></use></svg>
                    @if($flagOpen==true)
                        <div class="maxLine_1">{{ $titlePhoneWallpaper }}</div>
                    @else 
                        <a href="{{ env('APP_URL') }}/{{ $url }}" class="maxLine_1" arira-label="{{ $wallpaperMobile->name }}">{{ $titlePhoneWallpaper }}</a>
                    @endif
                    <div class="actionMenu {{ $classStatusMenu }}"></div>
                </div>
                <ul id="{{ $url }}" class="filterLinkSelected {{ $classActive }}">
                    @foreach($wallpaperMobile->childs as $event)
                        @if(!empty($event->seo->type)&&$event->seo->type=='category_info')
                            @foreach($event->seos as $seo)
                                @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                                    @php
                                        $title      = $seo->infoSeo->title ?? null;
                                        $urlFull    = env('APP_URL').'/'.$seo->infoSeo->slug_full;
                                    @endphp
                                    <li>
                                        <a href="{{ $urlFull }}" title="{{ $title }}" aria-label="{{ $title }}">
                                            <div class="maxLine_1">{{ $title }} {!! $event->products->count()>0 ? '(<span class="highLight">'.$event->products->count().'</span>)' : null !!}</div>
                                        </a>
                                    </li>
                                    @break
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </ul>
            </li>
        @endif
        <!-- phong cách -->
        <li>
            <div class="close" onclick="showHideListMenuMobile(this, 'phong-cach')">
                <svg style="transform: scale(1.325);"><use xlink:href="#icon_brush"></use></svg>
                <div class="maxLine_1" style="margin-left:-3px;">{{ config('data_language_2.'.$language.'.wallpaper_style') }}</div>
                <div class="actionMenu"></div>
            </div>
            <ul id="phong-cach" class="filterLinkSelected">
                @foreach($wallpaperMobile->childs as $event)
                    @if(!empty($event->seo->type)&&$event->seo->type=='style_info')
                        @foreach($event->seos as $seo)
                            @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                                @php
                                    $title      = $seo->infoSeo->title ?? null;
                                    $urlFull    = env('APP_URL').'/'.$seo->infoSeo->slug_full;
                                @endphp
                                <li>
                                    <a href="{{ $urlFull }}" title="{{ $title }}" aria-label="{{ $title }}">
                                        <div class="maxLine_1">{{ $title }} {!! $event->products->count()>0 ? '(<span class="highLight">'.$event->products->count().'</span>)' : null !!}</div>
                                    </a>
                                </li>
                                @break
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </ul>
        </li>
        <!-- sự kiện -->
        <li>
            @php
                $altPhoneWallpaperEvent = config('data_language_2.'.$language.'.phone_wallpaper');
            @endphp
            <div class="close" onclick="showHideListMenuMobile(this, 'su-kien')">
                {{-- {{ request()->cookie('view_mode') ?? config('main_'.env('APP_NAME').'.view_mode')[0]['key'] }} --}}
                <img src="https://cdn.wallsora.com/storage/images/icon-event-light.webp" alt="{!! $altPhoneWallpaperEvent !!}" title="{!! $altPhoneWallpaperEvent !!}" />
                <div class="maxLine_1">{{ config('data_language_1.'.$language.'.event') }}</div>
                <div class="actionMenu"></div>
            </div>
            <ul id="su-kien" class="filterLinkSelected">
                @foreach($wallpaperMobile->childs as $event)
                    @if(!empty($event->seo->type)&&$event->seo->type=='event_info')
                        @foreach($event->seos as $seo)
                            @if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language)
                                @php
                                    $title      = $seo->infoSeo->title ?? null;
                                    $urlFull    = env('APP_URL').'/'.$seo->infoSeo->slug_full;
                                @endphp
                                <li>
                                    <a href="{{ $urlFull }}" title="{{ $title }}" aria-label="{{ $title }}">
                                        <div class="maxLine_1">{{ $title }} {!! $event->products->count()>0 ? '(<span class="highLight">'.$event->products->count().'</span>)' : null !!}</div>
                                    </a>
                                </li>
                                @break
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </ul>
        </li>
        <!-- miễn phí -->
        <li>
            @php
                $wallpaperFreeText      = config('data_language_2.'.$language.'.free_wallpaper');
                $slugFullWallpaperFree  = '';
                foreach($wallpaperMobile->childs as $child){
                    if(in_array($child->seo->slug, config('main_'.env('APP_NAME').'.url_free_wallpaper_category'))){
                        foreach($child->seos as $seo){
                            if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                                $slugFullWallpaperFree = $seo->infoSeo->slug_full;
                                break;
                            }
                        }
                    }
                }
            @endphp
            <a href="{{ env('APP_URL') }}/{{ $slugFullWallpaperFree }}" title="{{ $wallpaperFreeText }}" aria-label="{{ $wallpaperFreeText }}">
                <svg><use xlink:href="#icon_share"></use></svg>
                <div class="maxLine_1">{{ $wallpaperFreeText }}</div>
            </a>
        </li>
        <!-- hỗ trợ -->
        <li>
            <div class="close" onclick="showHideListMenuMobile(this, 'ho-tro')">
                <svg><use xlink:href="#icon_support"></use></svg>
                <div class="maxLine_1">{{ config('data_language_1.'.$language.'.support') }}</div>
                <div class="actionMenu"></div>
            </div>
            <ul id="ho-tro" class="filterLinkSelected">
                @foreach($policies as $policy)
                    @php
                        $title      = '';
                        $slugPage   = '';
                        foreach($policy->seos as $seo){
                            if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                                $title      = $seo->infoSeo->title;
                                $slugPage   = $seo->infoSeo->slug_full;
                                break;
                            }
                        }
                    @endphp
                    <li>
                        <a href="{{ env('APP_URL').'/'.$slugPage }}" title="{{ $title }}" aria-label="{{ $title }}">
                            <div class="maxLine_1">{{ $title }}</div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
        <!-- chủ đề blog => duyệt để in tất cả cấp 1 -->
        @foreach($categoriesBlog as $categoryBlogLv1)
            @php
                $cateogyrBlogLv1ByLanguage = [];
                foreach($categoryBlogLv1->seos as $seo){
                    if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                        $cateogyrBlogLv1ByLanguage = $seo->infoSeo;
                        break;
                    }
                }
                $idGroupMenu    = $cateogyrBlogLv1ByLanguage->slug ?? '';
                $classTmp       = 'close';
                $classActive    = '';
                $classStatusMenu      = '';
                $selected       = '';
                $flagOpen       = $idGroupMenu==$urlPath ? true : false;
                if($flagOpen==true){
                    $classTmp       = 'open';
                    $classActive    = 'active';
                    $classStatusMenu      = 'isOpen';
                    $selected       = 'selected';
                }
            @endphp
            @if(!empty($cateogyrBlogLv1ByLanguage))
                <li class="{{ $selected }}">
                    <div class="{{ $classTmp }}" onclick="showHideListMenuMobile(this, '{{ $idGroupMenu }}')">
                        <svg style="transform: scale(0.9);"><use xlink:href="#icon_blog"></use></svg>
                        {{-- <div class="maxLine_1">{{ $cateogyrBlogLv1ByLanguage->title }}</div> --}}
                        <a href="{{ env('APP_URL') }}/{{ $cateogyrBlogLv1ByLanguage->slug_full }}" class="maxLine_1" arira-label="{{ $wallpaperMobile->name }}">{{ $cateogyrBlogLv1ByLanguage->title }}</a>
                        <div class="actionMenu {{ $classStatusMenu }}"></div>
                    </div>
                    <ul id="{{ $idGroupMenu }}" class="filterLinkSelected {{ $classActive }}">
                        @foreach($categoryBlogLv1->childs as $categoryBlog)
                            @php
                                $title      = '';
                                $slugPage   = '';
                                foreach($categoryBlog->seos as $seo){
                                    if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                                        $title      = $seo->infoSeo->title;
                                        $slugPage   = $seo->infoSeo->slug_full;
                                        break;
                                    }
                                }
                            @endphp
                            <li>
                                <a href="{{ env('APP_URL').'/'.$slugPage }}" title="{{ $title }}" aria-label="{{ $title }}">
                                    <div class="maxLine_1">{{ $title }}</div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endif
        @endforeach
    </ul>
</div>

<div class="socialBox">
    <div class="socialBox_social">
        <div class="socialBox_social_title">
            {{ config('data_language_1.'.$language.'.connect_with_us') }}
        </div>
        <div class="socialBox_social_box">
            <a href="https://www.facebook.com/wallpapers.name.com.vn" class="socialBox_social_box_item facebook" aria-label="facebook"></a>
            <a href="https://www.instagram.com/wallpapers_namecomvn" class="socialBox_social_box_item instagram" aria-label="instagram"></a>
            <a href="https://www.youtube.com/@wallpapers_namecomvn" class="socialBox_social_box_item youtube" aria-label="youtube"></a>
            <a href="https://www.tiktok.com/@wallpapers_namecomvn" class="socialBox_social_box_item tiktok" aria-label="tiktok"></a>
            {{-- <a href="https://twitter.com/wallpapers_name" class="socialBox_social_box_item twitter" aria-label="twitter"></a> --}}
        </div>
    </div>
    
    <!-- DMCA -->
    @include('wallpaper.template.dmca')
</div>

<div class="closeButtonMobileMenu show-991" onClick="toggleMenuMobile('js_toggleMenuMobile');">
    <svg><use xlink:href="#icon_close"></use></svg>
</div>