<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Tag;
use App\Models\Category;
use App\Models\SeoContent;

class CopyBoxContentToAllTagAndCategory implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ordering;
    private $language;
    private $content;
    public  $tries = 5; // Số lần thử lại

    public function __construct($ordering, $language, $content){
        $this->ordering     = $ordering;
        $this->language     = $language;
        $this->content      = $content;
    }

    public function handle(){
        try {
            /* tag */
            $tags           = Tag::select('*')
                                ->with('seo', 'seos')
                                ->get();
            foreach($tags as $tag){
                foreach($tag->seos as $seo){
                    if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$this->language){
                        $idSeo = $seo->infoSeo->id;
                        /* xóa box cũ */
                        SeoContent::select('*')
                            ->where('seo_id', $idSeo)
                            ->where('ordering', $this->ordering)
                            ->delete();
                        /* tạo lại box mới bằng content truyền vào */
                        SeoContent::insertItem([
                            'seo_id'    => $idSeo,
                            'ordering'  => $this->ordering,
                            'content'   => $this->content,
                        ]);
                        break;
                    }
                }
            }
            /* category */
            $categories     = Category::select('*')
                                ->whereHas('seos.infoSeo', function($query){
                                    $query->where('level', 2);
                                })
                                ->with('seo', 'seos')
                                ->get();
            foreach($categories as $category){
                foreach($category->seos as $seo){
                    if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$this->language){
                        $idSeo = $seo->infoSeo->id;
                        /* xóa box cũ */
                        SeoContent::select('*')
                            ->where('seo_id', $idSeo)
                            ->where('ordering', $this->ordering)
                            ->delete();
                        /* tạo lại box mới bằng content truyền vào */
                        SeoContent::insertItem([
                            'seo_id'    => $idSeo,
                            'ordering'  => $this->ordering,
                            'content'   => $this->content,
                        ]);
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            throw $e; // Đẩy lại lỗi để Laravel tự động thử lại
        }
    }

}
