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
use App\Models\JobAutoTranslate;
use App\Http\Controllers\Admin\ChatGptController;
use App\Http\Controllers\Admin\HelperController;
use App\Models\JobAutoTranslateLinks;

class AutoWriteContent implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ordering;
    private $idSeo;
    private $infoPrompt;
    public  $tries = 5; // Số lần thử lại

    public function __construct($ordering, $idSeo, $idPrompt){
        $this->ordering     = $ordering;
        $this->idSeo        = $idSeo;
        $this->infoPrompt   = Prompt::find($idPrompt);
    }

    public function handle(){
        try {
            $infoPage   = HelperController::getFullInfoPageByIdSeo($this->idSeo);
            $promptText = ChatGptController::convertPrompt($infoPage, $this->infoPrompt, 'vi');

            if($this->ordering==6||$this->ordering==7){ /* chỉ cần replace */
                $content    = $promptText;
            }else {
                $response   = ChatGptController::callApi($promptText, $this->infoPrompt);
                // Kiểm tra nếu có lỗi từ API thì đẩy lại Job
                if (!empty($response['error'])) {
                    throw new \Exception($response['error']); // Tạo Exception mới
                }

                $content = $response['content'] ?? '';
            }

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
        } catch (\Exception $e) {
            throw $e; // Đẩy lại lỗi để Laravel tự động thử lại
        }
    }

}
