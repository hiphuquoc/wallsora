<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Seo;
use App\Models\RelationSeoCategoryInfo;
use App\Models\RelationSeoTagInfo;
use App\Models\RelationSeoProductInfo;
use App\Models\RelationSeoPageInfo;
use App\Models\RelationSeoCategoryBlog;
use App\Models\RelationSeoBlogInfo;
use App\Models\RelationSeoFreeWallpaperInfo;
use App\Http\Controllers\Admin\HelperController;

class AutoTranslateAndCreatePage implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $arrayLanguageHas;
    private $arrayLanguageTranslate;
    private $infoPage;
    public  $tries = 5; // Số lần thử lại

    public function __construct($arrayLanguageHas, $arrayLanguageTranslate, $infoPage){
        $this->arrayLanguageHas         = $arrayLanguageHas;
        $this->arrayLanguageTranslate   = $arrayLanguageTranslate;
        $this->infoPage                 = $infoPage;
    }

    public function handle(){
        try {
            $type                   = $this->infoPage->seo->type;
            /* array chứa những ngôn ngữ để lấy content làm bản dịch , 'vi', 'fr', 'es', 'id', 'ja', 'ko', 'ru', 'id' */ 
            $arrayLanguageAccept    = ['en', 'vi', 'fr', 'es', 'id', 'ja', 'ko', 'ru', 'id']; /* dùng 5 ngôn ngữ này làm ngôn ngữ nguồn cho bản dịch -> tăng độ chính xác */
            /* tạo prompt */
            /*
                Tôi có nội dung của title, seo_title và seo_description của các phiên bản ngôn ngữ như sau, tôi gửi hết vào để bạn có thể tham khảo và cho ra bản dịch chuẩn nhất:
            */
            $promptText = "Tôi có nội dung của title, seo_title và seo_description của các phiên bản ngôn ngữ như sau, tôi gửi hết vào để bạn có thể tham khảo và cho ra bản dịch chuẩn nhất:\n";
            /*
                - Ngôn ngữ vi với title, seo_title, seo_description lần lượt là:
                Hài Hước
                ...
                ...
                - Ngôn ngữ en với title, seo_title, seo_description lần lượt là:
            */
            $partPromptOfSource     = "";
            foreach($this->arrayLanguageHas as $lhas){
                if(in_array($lhas['key'], $arrayLanguageAccept)){
                    $partPromptOfSource .= "Ngôn ngữ ".$lhas['key']." với title, seo_title, seo_description lần lượt là:\n";
                    $partPromptOfSource .= "title: ".$lhas['title']."\n";
                    $partPromptOfSource .= "seo_title: ".$lhas['seo_title']."\n";
                    $partPromptOfSource .= "seo_description: ".$lhas['seo_description']."\n";
                }
            }
            $promptText     .= $partPromptOfSource."\n";
            /* 
                Bên cạnh đó tôi cũng có thông tin từ wiki để bạn tham khảo và hiểu rõ chủ đề hơn: #wiki
            */
            $promptText     .= "Bên cạnh đó tôi cũng có thông tin từ wiki để bạn tham khảo và hiểu rõ chủ đề hơn: ".$this->infoPage->notes."\n";
            /*
                Tôi cần bạn hoàn thành các bản dịch, và điền vào array sau giúp tôi:
            */
            $promptText     .= "Tôi cần bạn hoàn thành các bản dịch, và điền vào array sau giúp tôi:\n";
            /*
                [
                    'ko'    => [
                        'title'             => 'bản dịch title của ngôn ngữ ko - Tiếng Hàn',
                        'seo_title'         => 'bản dịch seo_title của ngôn ngữ ko - Tiếng Hàn',
                        'seo_description'   => 'bản dịch seo_description của ngôn ngữ ko - Tiếng Hàn',
                    ],
                ]

            */
            $partPromptOfGuide  = "[";
            foreach($this->arrayLanguageTranslate as $ltranslate){
                $partPromptOfGuide .= "'".$ltranslate['key']."'    => [
                                        'title'             => 'bản dịch title của ngôn ngữ ".$ltranslate['name']." - ".$ltranslate['name_by_language']." - ".$ltranslate['key']."',
                                        'seo_title'         => 'bản dịch seo_title của ngôn ngữ ".$ltranslate['name']." - ".$ltranslate['name_by_language']." - ".$ltranslate['key']."',
                                        'seo_description'   => 'bản dịch seo_description của ngôn ngữ ".$ltranslate['name']." - ".$ltranslate['name_by_language']." - ".$ltranslate['key']."',
                                    ],\n";
            }
            $partPromptOfGuide  .= "]";
            $promptText         .= $partPromptOfGuide."\n";
            /* 
                Yêu cầu thêm của tôi:
                    - hãy tham khảo các bản dịch nguồn tôi đưa vào để dịch cho chuẩn, sát nghĩa và hay nhất
                    - hãy kiểm tra kỹ thông tin bạn phản hồi và chỉnh sửa sau đó trả cho tôi kết quả tốt nhất
                    - trả về array dạng json
                    - nếu trong nội dung có các ký tự đặc biệt hay ZWNJ hãy chuyển nó thành văn bản giúp tôi
                    - tôi đang dùng API nên chỉ cần câu trả lời và không cần giải thích thêm gì cả
            */
            $promptText .= "Yêu cầu thêm của tôi:\n";
            $promptText .= "- hãy tham khảo các bản dịch nguồn tôi đưa vào để dịch cho chuẩn, sát nghĩa và hay nhất\n";
            $promptText .= "- nếu là tên riêng và ngôn ngữ này không có bản dich chính xác, hay không có cách gọi thông dụng của ngôn ngữ địa phương cho title đó thì hãy tham khảo bản dịch tiếng anh và wiki để lấy tên riêng đúng.\n";
            $promptText .= "- hãy kiểm tra kỹ thông tin bạn phản hồi và chỉnh sửa sau đó trả cho tôi kết quả tốt nhất\n";
            $promptText .= "- lưu quan trọng nhất là hãy trả về array dạng json\n";
            $promptText .= "- tôi đang dùng API nên chỉ cần câu trả lời và không cần giải thích thêm gì cả";
            /* call API để lấy dữ liệu trả về */
            $infoPrompt = new \stdClass; /* giả lập infoPrompt để truyền vào function */
            $infoPrompt->version = config('main_'.env('APP_NAME').'.ai_version')[0];
            $response   = \App\Http\Controllers\Admin\ChatGptController::callApi($promptText, $infoPrompt);
            $pattern    = '/\{(?:[^{}]|(?R))*\}/';
            preg_match($pattern, $response['content'], $matches);
            $responseAPI    = $matches[0];
            $responseAPI    = json_decode($responseAPI, true);
            // $responseAPI            = [
            //     'gu'    => [
            //         'title'             => 'Nội dung title của gu',
            //         'seo_title'         => 'Nội dung seo_title của gu',
            //         'seo_description'   => 'Nội dung seo_description của gu',
            //     ],
            //     'bn'    => [
            //         'title'             => 'Nội dung title của bn',
            //         'seo_title'         => 'Nội dung seo_title của bn',
            //         'seo_description'   => 'Nội dung seo_description của bn',
            //     ],
            // ];
            /* tạo seo */
            foreach($responseAPI as $language => $value){
                if(!empty($language)){
                    $arrayInsert            = [];
                    /* kiểm tra tiếp parent trang cha của ngôn ngữ đó có dịch chưa (nếu là trang con) */
                    $idSeoParentVI          = $this->infoPage->seo->parent;
                    $flagNext               = false;
                    $idParentForLanguage    = 0;
                    if(!empty($idSeoParentVI)){ /* trường hợp có trang cha */
                        $infoParentForLanguage  = HelperController::getFullInfoPageByIdSeo($idSeoParentVI);
                        foreach($infoParentForLanguage->seos as $s){
                            if(!empty($s->infoSeo->language)&&$s->infoSeo->language==$language) {
                                $idParentForLanguage = $s->infoSeo->id;
                                break;
                            }
                        }
                        /* có trang cha của ngôn ngữ cần dịch mới thực hiện tiếp */
                        if(!empty($idParentForLanguage)) $flagNext = true;
                    }else { /* trường hợp không có trang cha -> mặc định thực hiện */
                        $flagNext = true;
                    }
                    /* hợp lệ mới tiến hành tiếp */
                    if($flagNext==true){
                        $title                                  = HelperController::removeUnicodeControlCharacters($value['title']);
                        $arrayInsert['title']                   = $title;
                        /* slug và slug_full */
                        $slug                                   = HelperController::buildSlugFromTitle($title, $type, $language, $idSeoParentVI);
                        $arrayInsert['slug']                    = $slug;
                        $slugFull                               = \App\Models\Seo::buildFullUrl($slug, $idParentForLanguage);
                        $arrayInsert['slug_full']               = $slugFull;
                        $arrayInsert['parent']                  = $idParentForLanguage;
                        /* xây dựng mảng các phần tử còn lại */
                        $arrayInsert['seo_title']               = HelperController::removeUnicodeControlCharacters($value['seo_title']);
                        $arrayInsert['seo_description']         = HelperController::removeUnicodeControlCharacters($value['seo_description']);
                        $arrayInsert['level']                   = $this->infoPage->seo->level;
                        $arrayInsert['type']                    = $type;
                        $arrayInsert['rating_author_name']      = $this->infoPage->seo->rating_author_name;
                        $arrayInsert['rating_author_star']      = $this->infoPage->seo->rating_author_star;
                        $arrayInsert['rating_aggregate_count']  = $this->infoPage->seo->rating_aggregate_count;
                        $arrayInsert['rating_aggregate_star']   = $this->infoPage->seo->rating_aggregate_star;
                        $arrayInsert['created_by']              = $this->infoPage->seo->created_by;
                        $arrayInsert['language']                = $language;
                        /* linkcanonical */
                        $idSeoPageSource            = $this->infoPage->seo->link_canonical ?? 0;
                        $idSeoPageSourceForLanguage = 0;
                        if(!empty($idSeoPageSource)){
                            $infoPageSource             = HelperController::getFullInfoPageByIdSeo($idSeoPageSource);
                            foreach($infoPageSource->seos as $seo){
                                if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language) {
                                    $idSeoPageSourceForLanguage = $seo->infoSeo->id;
                                    break;
                                }
                            }
                        }
                        $arrayInsert['link_canonical']  = $idSeoPageSourceForLanguage;
                        $idSeoInsert                    = Seo::insertItem($arrayInsert, $this->infoPage->seo->id);
                        if(!empty($idSeoInsert)){
                            /* tạo relation kết nối với loại của nó */
                            switch ($type) {
                                case 'tag_info':
                                    RelationSeoTagInfo::insertItem([
                                        'seo_id'        => $idSeoInsert,
                                        'tag_info_id'   => $this->infoPage->id,
                                    ]);
                                    break;
                                case 'product_info':
                                    RelationSeoProductInfo::insertItem([
                                        'seo_id'            => $idSeoInsert,
                                        'product_info_id'   => $this->infoPage->id,
                                    ]);
                                    break;
                                case 'page_info':
                                    RelationSeoPageInfo::insertItem([
                                        'seo_id'        => $idSeoInsert,
                                        'page_info_id'  => $this->infoPage->id,
                                    ]);
                                    break;
                                case 'category_blog':
                                    RelationSeoCategoryBlog::insertItem([
                                        'seo_id'            => $idSeoInsert,
                                        'category_blog_id'  => $this->infoPage->id,
                                    ]);
                                    break;
                                case 'blog_info':
                                    RelationSeoBlogInfo::insertItem([
                                        'seo_id'            => $idSeoInsert,
                                        'blog_info_id'  => $this->infoPage->id,
                                    ]);
                                    break;
                                case 'free_wallpaper_info':
                                    RelationSeoFreeWallpaperInfo::insertItem([
                                        'seo_id'            => $idSeoInsert,
                                        'category_info_id'  => $this->infoPage->id,
                                    ]);
                                    break;
                                default:
                                    /* vì có thể là category_info, style_info, event_info */
                                    RelationSeoCategoryInfo::insertItem([
                                        'seo_id'            => $idSeoInsert,
                                        'category_info_id'  => $this->infoPage->id,
                                    ]);
                                    break;
                            }
                        }
                    }                
                }
            }
        } catch (\Exception $e) {
            throw $e; // Đẩy lại lỗi để Laravel tự động thử lại
        }
    }

}
