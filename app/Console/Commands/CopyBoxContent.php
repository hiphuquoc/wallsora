<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Jobs\CopyBoxContentToAllTagAndCategory;
use Illuminate\Console\Command;

class CopyBoxContent extends Command {

    // protected $signature = 'run:copyBoxContent {ordering : The ordering value to process}';
    protected $signature = 'run:copyBoxContent';
    protected $description = 'Copy box content based on ordering to all tags and categories';

    public function handle() {
        // Lấy giá trị ordering từ tham số
        $orderingCopy = 7;

        // Truy vấn dữ liệu
        $infoPageParent = Category::select('*')
            ->whereHas('seos.infoSeo', function($query){
                $query->where('slug', 'hinh-nen-dien-thoai'); /* mặc định lấy thông tin của trang hinh-nen-dien-thoai */
            })
            ->with('seo', 'seos')
            ->first();

        if (!$infoPageParent) {
            $this->error('No category found with the specified slug.');
            return 1; // Trả về mã lỗi
        }

        $contentCopy = '';
        foreach ($infoPageParent->seos as $seo) {
            if (!empty($seo->infoSeo->language)) {
                $language = $seo->infoSeo->language;
                foreach ($seo->infoSeo->contents as $content) {
                    if ($content->ordering == $orderingCopy) {
                        $contentCopy = $content->content;
                        CopyBoxContentToAllTagAndCategory::dispatch($orderingCopy, $language, $contentCopy);
                        $this->info("Content copied for ordering {$orderingCopy} and language {$language}.");
                        break;
                    }
                }
            }
        }

        if (empty($contentCopy)) {
            $this->warn("No content found for ordering {$orderingCopy}.");
        }

        return 0; // Trả về mã thành công
    }
}