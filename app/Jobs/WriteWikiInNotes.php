<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Http\Controllers\Admin\ChatGptController;
use App\Http\Controllers\Admin\HelperController;
use App\Models\JobAutoTranslateLinks;

class WriteWikiInNotes implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $infoPage;
    public  $tries = 5; // Số lần thử lại

    public function __construct($infoPage){
        $this->infoPage     = $infoPage;
    }

    public function handle(){
        try {
            /* lấy title, seo_title, seo_description của vi và en */
            $sourceText     = '';
            foreach($this->infoPage->seos as $seo){
                if(!empty($seo->infoSeo->language)&&($seo->infoSeo->language=='vi'||$seo->infoSeo->language=='en')){
                    $sourceText .= '"'.$seo->infoSeo->language.'" => [
                                        "title" => "'.$seo->infoSeo->title.'",
                                        "seo_title" => "'.$seo->infoSeo->seo_title.'",
                                        "seo_description" => "'.$seo->infoSeo->seo_description.'",
                                    ],';
                }
            }
            /* hoàn chỉnh prompt */
            $promptText     = 'tôi có một danh mục hình nền điện thoại, gồm title, seo_title và seo_description. Thông tin bản gốc tiếng việt và tiếng anh như bên dưới:
                                [
                                    '.$sourceText.'
                                ]

                                tôi cần một đoạn mô tả ngắn gọn, rõ ràng và chi tiết khoảng 3-5 dòng định nghĩa về chủ đề '.$this->infoPage->seo->title.' (như wiki giúp người dùng hiểu được bao quát chủ đề) và một đoạn giới thiệu ngắn khoảng 5-10 dòng cho người dùng biết danh mục hình nền '.$this->infoPage->seo->title.' của tôi gồm những hình ảnh gì, vẻ đẹp thu hút, sự hấp dẫn và lợi ích tinh thần của chủ đề hình nền này.
                                
                                tôi chỉ cần kết quả, đừng giải thích hay ghi chú gì thêm, trình bày như hướng dẫn bên dưới:
                                Định nghĩa về #title: [nội dung].
                                Hiểu về hình nền #title: [nội dung].';
            /* gọi API */
            $infoPrompt = ['version'   => 'qwen-max'];
            $response   = ChatGptController::callApi($promptText, $infoPrompt);
            /* lưu cơ sở dữ liệu */
            $content    = $response['content'] ?? '';
            if(!empty($content)){
                $this->infoPage->update(['notes' => $content]);
            }
        } catch (\Exception $e) {
            throw $e; // Đẩy lại lỗi để Laravel tự động thử lại
        }
    }

}
