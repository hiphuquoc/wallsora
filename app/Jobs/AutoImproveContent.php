<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Seo;
use App\Models\Prompt;
use App\Models\SeoContent;
use App\Http\Controllers\Admin\ChatGptController;
use App\Http\Controllers\Admin\ImproveController;
use App\Http\Controllers\Admin\HelperController;

class AutoImproveContent implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ordering;
    private $idSeo;
    public  $tries = 5; // Số lần thử lại

    public function __construct($ordering, $idSeo){
        $this->ordering     = $ordering;
        $this->idSeo        = $idSeo;
    }

    public function handle(){
        try {
            $content        = ImproveController::handleImproveContent($this->ordering, $this->idSeo);
            if(!empty(trim($content))){
                // Xóa content cũ
                SeoContent::where('seo_id', $this->idSeo)
                    ->where('ordering', $this->ordering)
                    ->delete();

                // Lưu content mới
                SeoContent::insertItem([
                    'seo_id'    => $this->idSeo,
                    'content'   => $content,
                    'ordering'  => $this->ordering,
                ]);
            }

        } catch (\Exception $e) {
            throw $e; // Đẩy lại lỗi để Laravel tự động thử lại
        }
    }

}
