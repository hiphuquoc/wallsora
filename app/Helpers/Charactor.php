<?php

namespace App\Helpers;

use GoogleTranslate;

class Charactor {

    public static function randomString($length = 10){
        $arr    = array_merge(range(0,9),range('A','Z'));
        $str    = implode('', $arr);
        $str    = str_shuffle($str);
        $result = mb_substr($str, 0, $length);
        return $result;
    }

    public static function convertStrToUrl($str = null, $word = '-') {
        if ($str == null || $str == '') {
            return '';
        }

        // Chuẩn hóa chuỗi về dạng NFC (nếu extension intl được cài đặt)
        if (function_exists('normalizer_normalize')) {
            $str = normalizer_normalize($str, \Normalizer::FORM_C);
        }

        // Bảng ánh xạ ký tự tiếng Việt có dấu sang không dấu
        $vietnameseChars = [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd',
            'À' => 'A', 'Á' => 'A', 'Ạ' => 'A', 'Ả' => 'A', 'Ã' => 'A',
            'Â' => 'A', 'Ầ' => 'A', 'Ấ' => 'A', 'Ậ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A',
            'Ă' => 'A', 'Ằ' => 'A', 'Ắ' => 'A', 'Ặ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A',
            'È' => 'E', 'É' => 'E', 'Ẹ' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E',
            'Ê' => 'E', 'Ề' => 'E', 'Ế' => 'E', 'Ệ' => 'E', 'Ể' => 'E', 'Ễ' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Ị' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I',
            'Ò' => 'O', 'Ó' => 'O', 'Ọ' => 'O', 'Ỏ' => 'O', 'Õ' => 'O',
            'Ô' => 'O', 'Ồ' => 'O', 'Ố' => 'O', 'Ộ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O',
            'Ơ' => 'O', 'Ờ' => 'O', 'Ớ' => 'O', 'Ợ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Ụ' => 'U', 'Ủ' => 'U', 'Ũ' => 'U',
            'Ư' => 'U', 'Ừ' => 'U', 'Ứ' => 'U', 'Ự' => 'U', 'Ử' => 'U', 'Ữ' => 'U',
            'Ỳ' => 'Y', 'Ý' => 'Y', 'Ỵ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y',
            'Đ' => 'D'
        ];

        // Thay thế ký tự tiếng Việt có dấu
        $str = strtr($str, $vietnameseChars);

        // Chuyển về chữ thường
        $str = strtolower($str);

        // Loại bỏ ký tự đặc biệt không mong muốn, chỉ giữ chữ cái, số, và dấu gạch ngang
        $str = preg_replace('/[^a-z0-9\s-]/', '', $str);

        // Thay thế nhiều dấu cách hoặc gạch ngang liên tiếp bằng một dấu gạch ngang
        $str = preg_replace('/[\s-]+/', $word, $str);

        // Loại bỏ dấu gạch ngang ở đầu và cuối chuỗi
        $str = trim($str, $word);

        return $str;
    }

    public static function convertStringSearch($string){
        $output     = null;
        if(!empty($string)){
            $output = explode(' ', $string);
            $output = implode('%', $output);
        }
        return $output;
    }

    public static function translateViToEn($strVi){
        $response       = null;
        if(!empty($strVi)){
            $ggTrans    = GoogleTranslate::translate($strVi);
            $response   = $ggTrans['translated_text'] ?? null;
        }
        return $response;
    }

    public static function splitString($html, $limitWord = 2000) {
        // Tạo mảng để lưu trữ các phần của chuỗi
        $parts = [];
    
        // Tạo một biến tạm để lưu trữ một phần của chuỗi
        $currentPart = '';
    
        // Phân tách chuỗi thành các đoạn dựa trên các thẻ đóng HTML và dấu xuống dòng
        $sentences = preg_split('/(\n|<\/p>|<\/h[2-6]>|<\/ul>|<\/ol>)/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    
        $newSentences = [];
        for($i=0;$i<count($sentences);++$i){
            if(!empty(trim($sentences[$i]))){
                $newSentences[] = trim($sentences[$i]);
            }
        }
    
        // Lặp qua từng đoạn
        foreach ($newSentences as $sentence) {
            // Nếu độ dài của phần hiện tại cộng với độ dài của câu vượt quá 2000 ký tự
            if (strlen($currentPart) + strlen($sentence) > $limitWord && strlen($sentence) > 10) { /* lớn hơn 10 để tránh tách những thẻ HTML ngắn */
                // Thêm phần hiện tại vào mảng
                $parts[] = $currentPart;
                // Reset phần hiện tại để bắt đầu một phần mới
                $currentPart = '';
            }
            // Thêm đoạn vào phần hiện tại
            $currentPart .= $sentence;
    
            // // Nếu đoạn không phải là đoạn cuối cùng, thêm xuống dòng vào phần hiện tại
            // if ($sentence !== end($newSentences)) {
            //     $currentPart .= "\n";
            // }
        }
    
        // Thêm phần hiện tại cuối cùng vào mảng
        $parts[] = $currentPart;
    
        return $parts;
    }    

    public static function generateChatgptDataAndEvent($itemPage, $prompt, $language, $key, $idContent = 0) {
        $dataChatgpt = null;
        $eventChatgpt = null;
        $dataChatgpt = 'data-id=' . $itemPage->id . ' data-language=' . $language . ' data-id_prompt=' . $prompt->id.' data-type='.$prompt->type.' data-id_content='.$idContent;
        $eventChatgpt = "chatGpt($('#".$key."'), " . $itemPage->id . ", '" . $language . "', " . $prompt->id . ", ".$idContent.")";
        return compact('dataChatgpt', 'eventChatgpt');
    }

    public static function concatenateWords(array $words, $language){
        // Lấy giá trị config flag từ ngôn ngữ
        $flagJoinLeftToRight    = config('language.'.$language.'.flag_join_left_to_right');
        $spaceBetween           = config('language.'.$language.'.flag_has_space_in_content')==true ? ' ' : '';
        // Nếu flag là true, ghép từ trái sang phải, nếu false, ghép từ phải sang trái
        if ($flagJoinLeftToRight) {
            return implode($spaceBetween, $words); // Ghép từ trái sang phải
        } else {
            return implode($spaceBetween, array_reverse($words)); // Ghép từ phải sang trái
        }
    }

}