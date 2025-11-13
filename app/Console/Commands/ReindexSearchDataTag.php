<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tag;
use MeiliSearch\Client;

class ReindexSearchDataTag extends Command {

    protected $signature    = 'search:reindex-tag';
    protected $description  = 'Re-index all Tags into Meilisearch';

    public function handle() {
        $this->info('⚙️ Đang cấu hình Meilisearch index cho Tag...');

        // Cấu hình Meilisearch index
        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $index = $client->index('tag_info'); // <- phải khớp với tên ở Meilisearch Cloud

        $index->updateSearchableAttributes([
            'title',
            'seos',        // cho phép search trong seos.infoSeo.title
            'categories',
            'products',
            'freeWallpapers',
        ]);

        $this->info('✅ Đã cấu hình searchable attributes cho index "tags".');

        // Bắt đầu reindex dữ liệu
        $this->info('⏳ Bắt đầu re-index toàn bộ tags...');

        Tag::with(['seo', 'seos.infoSeo', 'categories.infoCategory', 'products.infoProduct', 'freeWallpapers.infoFreeWallpaper'])
            ->chunk(50, function ($tags) {
                $tags->each->searchable(); // Laravel Scout xử lý
                $this->info('✅ Đã index thêm ' . $tags->count() . ' tags');
            });

        $this->info('🎉 Hoàn tất re-index!');
    }

}
