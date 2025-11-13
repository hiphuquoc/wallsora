@if(!empty($data)&&$data->isNotEmpty())
    @php
        $xhtml              = null;
        $i                  = 1;
        foreach($data as $d){
            foreach($d->seos as $s){
                if(!empty($s->infoSeo->language)&&$s->infoSeo->language==$language){
                    if($i!=1) $xhtml .= ', ';
                    $url    = env('APP_URL').'/'.$s->infoSeo->slug_full;
                    $xhtml  .= '{
                                "@type": "ListItem",
                                "position": '.$i.',
                                "url": "'.$url.'"
                            }';
                    ++$i;
                    break;
                }
            }
        }
    @endphp
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