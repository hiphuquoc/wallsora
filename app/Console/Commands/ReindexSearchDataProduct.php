<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use MeiliSearch\Client;

class ReindexSearchDataProduct extends Command {

    protected $signature    = 'search:reindex-product';
    protected $description  = 'Cấu hình & Re-index toàn bộ sản phẩm vào Meilisearch';

    public function handle() {
        $this->info('⚙️ Đang cấu hình Meilisearch index cho Product...');

        // Cấu hình Meilisearch index
        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $index = $client->index('product_info'); // <- phải khớp với tên ở Meilisearch Cloud

        $index->updateSearchableAttributes([
            'title',
            'seos',        // cho phép search trong seos.infoSeo.title
            'tags',
            'categories',
        ]);

        $this->info('✅ Đã cấu hình searchable attributes cho index "products".');

        // Bắt đầu reindex dữ liệu
        $this->info('⏳ Bắt đầu re-index toàn bộ sản phẩm...');

        Product::with(['seo', 'seos.infoSeo', 'tags.infoTag', 'categories.infoCategory'])
            ->chunk(50, function ($products) {
                $products->each->searchable(); // Laravel Scout xử lý
                $this->info('✅ Đã index thêm ' . $products->count() . ' sản phẩm');
            });

        $this->info('🎉 Hoàn tất re-index!');
    }

}
