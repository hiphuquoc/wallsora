<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Tag;
use App\Jobs\CheckTranslateOfPage;

class Tmp implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(){

    }

    public function handle(){
        try {
            $tags   = Tag::select('*')
                        ->get();
            $arrayNotCheck  = ['vi', 'en'];
            foreach($tags as $tag){
                foreach($tag->seos as $seo){
                    if(!empty($seo->infoSeo->language)&&!in_array($seo->infoSeo->language, $arrayNotCheck)){
                        CheckTranslateOfPage::dispatch($seo->infoSeo->id, $seo->infoSeo->language);
                    }
                }
            }

        } catch (\Exception $e) {
            throw $e; // Đẩy lại lỗi để Laravel tự động thử lại
        }
    }

}
