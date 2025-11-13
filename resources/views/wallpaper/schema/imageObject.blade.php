@if(!empty($data)&&$data->isNotEmpty())
    @php
        $xhtml              = null;
        $i                  = 1;
        foreach($data as $d){
            if(!empty($d->seo)){
                if($d->seo->type=='product_info'){ /* xử lý cho phần tử con là product_info */
                    foreach($d->prices as $price){
                        foreach($price->wallpapers as $w){
                            if($i!=1) $xhtml .= ', ';
                            $urlImage   = \App\Helpers\Image::getUrlImageCloud($w->infoWallpaper->file_cloud_wallpaper);
                            $name           = null;
                            $description    = null;
                            foreach($d->seos as $s){
                                if(!empty($s->infoSeo->language)&&$s->infoSeo->language==$language) {
                                    $name           = $s->infoSeo->seo_title ?? $d->seo->seo_title;
                                    $description    = $s->infoSeo->seo_description ?? $s->seo->seo_description;
                                    break;
                                }
                            }
                            $xhtml      .= '{
                                                "@type": "ImageObject",
                                                "contentUrl": "'.$urlImage.'",
                                                "name": "'.$name.'",
                                                "description": "'.$description.'"
                                            }';
                            ++$i;
                        }
                    }
                }else if($d->seo->type=='free_wallpaper_info'){ /* xử lý cho phần tử con là free_wallpaper_info */
                    if($i!=1) $xhtml .= ', ';
                    $urlImage   = \App\Helpers\Image::getUrlImageCloud($d->file_cloud);
                    $name           = null;
                    $description    = null;
                    foreach($d->seos as $s){
                        if(!empty($s->infoSeo->language)&&$s->infoSeo->language==$language) {
                            $name           = $s->infoSeo->seo_title ?? $d->seo->seo_title;
                            $description    = $s->infoSeo->seo_description ?? $s->seo->seo_description;
                            break;
                        }
                    }
                    $xhtml      .= '{
                                        "@type": "ImageObject",
                                        "contentUrl": "'.$urlImage.'",
                                        "name": "'.$name.'",
                                        "description": "'.$description.'"
                                    }';
                    ++$i;
                }
            }
        }
    @endphp
    @if(!empty($xhtml))
        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "ItemList",
                "itemListElement": [
                    {!! $xhtml !!}
                ]
            }
        </script>
    @endif
@endif