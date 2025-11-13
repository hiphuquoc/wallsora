<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use MeiliSearch\Client;

class ReindexSearchDataCategory extends Command {

    protected $signature    = 'search:reindex-category';
    protected $description  = 'Re-index all Categories into Meilisearch';

    public function handle() {
        $this->info('⚙️ Đang cấu hình Meilisearch index cho Category...');

        // Cấu hình Meilisearch index
        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $index = $client->index('category_info'); // <- phải khớp với tên ở Meilisearch Cloud

        $index->updateSearchableAttributes([
            'title',
            'seos',        // cho phép search trong seos.infoSeo.title
            'tags',
            'products',
            'freeWallpapers',
        ]);

        $this->info('✅ Đã cấu hình searchable attributes cho index "categories".');

        // Bắt đầu reindex dữ liệu
        $this->info('⏳ Bắt đầu re-index toàn bộ categories...');

        Category::with(['seo', 'seos.infoSeo', 'tags.infoTag', 'products.infoProduct', 'freeWallpapers.infoFreeWallpaper'])
            ->chunk(50, function ($categories) {
                $categories->each->searchable(); // Laravel Scout xử lý
                $this->info('✅ Đã index thêm ' . $categories->count() . ' categories');
            });

        $this->info('🎉 Hoàn tất re-index!');
    }

}
