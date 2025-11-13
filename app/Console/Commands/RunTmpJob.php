<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\Tmp;

class RunTmpJob extends Command {
    
    protected $signature = 'run:tmp-job {num1 : Số thứ nhất} {num2 : Số thứ hai}';

    protected $description = 'Chạy job Tmp thông qua command với hai số truyền vào';

    public function handle() {
        // Lấy giá trị từ tham số
        $num1 = $this->argument('num1');
        $num2 = $this->argument('num2');

        // Kiểm tra xem cả hai giá trị có phải số hợp lệ không
        if (!is_numeric($num1) || !is_numeric($num2)) {
            $this->error('Cả hai tham số phải là số.');
            return 1;
        }

        $tags   = \App\Models\Tag::select('*')
                        ->where('id', '>=', $num1)
                        ->where('id', '<=', $num2)
                        ->get();
        $arrayNotTranslate  = ['vi', 'en'];
        $arrayOrdering      = [1, 2, 3, 4, 5, 6, 8];
        $count  = 0;
        foreach($tags as $tag){
            $type = $tag->seo->type;
            $infoPrompt = \App\Models\Prompt::select('*')
                            ->where('reference_table', $type)
                            ->where('reference_name', 'content')
                            ->where('type', 'translate_content')
                            ->first();
            foreach($tag->seos as $seo){
                if(!empty($seo->infoSeo->language)&&!in_array($seo->infoSeo->language, $arrayNotTranslate)){
                    \App\Http\Controllers\Admin\TranslateController::createJobTranslateContent($tag->seo->id, $seo->infoSeo->language, $arrayOrdering);
                    ++$count;
                }
            } 
        }

        $this->info('Đã tạo '.$count.' công việc!');
        return 0;
    }
}
