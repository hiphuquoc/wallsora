<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blog;
use MeiliSearch\Client;

class ReindexSearchDataBlog extends Command {

    protected $signature    = 'search:reindex-blog';
    protected $description  = 'Re-index all Blogs into Meilisearch';

    public function handle() {
        $this->info('⚙️ Đang cấu hình Meilisearch index cho Blog...');

        // Cấu hình Meilisearch index
        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $index = $client->index('blog_info'); // <- phải khớp với tên ở Meilisearch Cloud

        $index->updateSearchableAttributes([
            'title',
            'seos',        // cho phép search trong seos.infoSeo.title
            'categories',
        ]);

        $this->info('✅ Đã cấu hình searchable attributes cho index "Blogs".');

        // Bắt đầu reindex dữ liệu
        $this->info('⏳ Bắt đầu re-index toàn bộ blogs...');

        Blog::with(['seo', 'seos.infoSeo', 'categories.infoCategory'])
            ->chunk(50, function ($blogs) {
                $blogs->each->searchable(); // Laravel Scout xử lý
                $this->info('✅ Đã index thêm ' . $blogs->count() . ' blogs');
            });

        $this->info('🎉 Hoàn tất re-index!');
    }

}
