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
use App\Http\Controllers\Admin\GoogleTranslateController;
use App\Http\Controllers\Admin\HelperController;

class CheckTranslateOfPage implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $language;
    private $idSeo;
    public  $tries = 5; // Số lần thử lại

    public function __construct($idSeo, $language){
        $this->idSeo        = $idSeo;
        $this->language     = $language;
    }

    public function handle(){
        try {
            /* array not check */
            $arrayNotCheck          = ['vi', 'en'];
            if(!in_array($this->language, $arrayNotCheck)){
                /* thông tin trang */
                $infoPage       = HelperController::getFullInfoPageByIdSeo($this->idSeo);
                /* lấy thông tin trang tiếng việt & tiếng anh để ghép vào prompt */
                $sourceText     = '';
                foreach($infoPage->seos as $seo){
                    if(!empty($seo->infoSeo->language)&&($seo->infoSeo->language=='vi'||$seo->infoSeo->language=='en')){
                        $sourceText .= '"'.$seo->infoSeo->language.'" => [
                                            "title" => "'.$seo->infoSeo->title.'",
                                            "seo_title" => "'.$seo->infoSeo->seo_title.'",
                                            "seo_description" => "'.$seo->infoSeo->seo_description.'",
                                        ],';
                    }
                }
                /* tiến hành */
                foreach($infoPage->seos as $seo){
                    if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$this->language){

                        /* biến */
                        $language       = $seo->infoSeo->language;
                        $nameLanguage   = config('language.'.$language.'.name');
                        $promptText     = 'tôi có một danh mục hình nền điện thoại, gồm title, seo_title và seo_description. Thông tin bản gốc tiếng việt và tiếng anh như bên dưới:
                                                [
                                                    '.$sourceText.'
                                                ]

                                            bên dưới đây là định nghĩa từ wiki về chủ đề '.$seo->infoSeo->title.' và hình nền '.$seo->infoSeo->title.' cho bạn hiểu về chủ đề:
                                            '.$infoPage->notes.'

                                            yêu cầu của tôi, hãy dựa vào bản tiếng Việt, bản Tiếng Anh và thông tin từ wiki tôi gửi để kiểm tra bản dịch tiếng '.$nameLanguage.' ('.$language.') trong các giá trị bên trong array bên dưới:
                                            [
                                                "'.$language.'" => [
                                                    "title"             => "'.$seo->infoSeo->title.'",
                                                    "seo_title"         => "'.$seo->infoSeo->seo_title.'",
                                                    "seo_description"   => "'.$seo->infoSeo->seo_description.'",
                                                ]
                                            ]
                                            
                                            '.env('NOTES_PROMPT').'
                                            
                                            kết quả trả về, nếu trường hợp các bản dịch tiếng '.$nameLanguage.' ('.$language.') không sai thì trả về array rỗng (không cần giải thích gì thêm), nếu giá trị title chưa đúng cần sửa lại thì hãy trả về array theo mẫu bên dưới giúp tôi (tôi chỉ cần array kết quả, không cần giải thích gì thêm):
                                            [
                                                "title"                 => "'.$seo->infoSeo->title.'",
                                                "seo_title"             => "'.$seo->infoSeo->seo_title.'",
                                                "seo_description"       => "'.$seo->infoSeo->seo_description.'",
                                                "new_title"             => "bản dịch title đúng",
                                                "new_seo_title"         => "bản dịch seo_title đúng",
                                                "new_seo_description"   => "bản dịch seo_description đúng",
                                            ]';
                        /* gọi API AI */
                        $infoPrompt     = ['version' => 'qwen-max'];
                        $response       = ChatGptController::callApi($promptText, $infoPrompt);
                        /* lọc array */
                        $arrayData      = ChatGptController::getArrayInResponse($response['content']);
                        if(!empty($arrayData)){
                            /* thêm các trường dữ liệu thông tin */
                            $arrayData['seo_id']                = $seo->infoSeo->id;
                            $arrayData['language']              = $language;
                            $arrayData['type']                  = $infoPage->seo->type;
                            /* bản gốc tiếng việt */
                            $arrayData['title_vi']              = $infoPage->seo->title;
                            $arrayData['seo_title_vi']          = $infoPage->seo->seo_title;
                            $arrayData['seo_description_vi']    = $infoPage->seo->seo_description;
                            /* bản gốc tiếng anh */
                            foreach($infoPage->seos as $s){
                                if(!empty($s->infoSeo->language)&&$s->infoSeo->language=='en'){
                                    $arrayData['title_en']              = $s->infoSeo->title;
                                    $arrayData['seo_title_en']          = $s->infoSeo->seo_title;
                                    $arrayData['seo_description_en']    = $s->infoSeo->seo_description;
                                    break;
                                }
                            }
                            /* dịch google translate các cột để dễ so sánh */
                            $arrayData['title_google_translate_vi']     = GoogleTranslateController::translate($arrayData['title'], 'vi');
                            $arrayData['title_google_translate_en']     = GoogleTranslateController::translate($arrayData['title'], 'en');
                            $arrayData['new_title_google_translate_vi'] = GoogleTranslateController::translate($arrayData['new_title'], 'vi');
                            $arrayData['new_title_google_translate_en'] = GoogleTranslateController::translate($arrayData['new_title'], 'en');
                            /* tiến hành insert */
                            \App\Models\CheckTranslate::select('*')
                                ->where('seo_id', $seo->infoSeo->id)
                                ->where('language', $language)
                                ->where('type', $infoPage->seo->type)
                                ->delete();
                            \App\Models\CheckTranslate::insertItem($arrayData);
                        }
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            throw $e; // Đẩy lại lỗi để Laravel tự động thử lại
        }
    }



}
