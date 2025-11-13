<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\RelationCategoryProduct;
use App\Models\RelationSeoProductInfo;
use App\Models\RelationTagInfoOrther;
use App\Models\Seo;
use App\Models\SeoContent;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class CopyMultiProductJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $infoProductSource;
    protected $arrayInfoProduct;

    /**
     * Create a new job instance.
     *
     * @param $infoProductSource
     * @param $arrayInfoProduct
     */
    public function __construct($infoProductSource, $arrayInfoProduct) {
        $this->infoProductSource = $infoProductSource;
        $this->arrayInfoProduct = $arrayInfoProduct;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $response = [];

        try {
            DB::beginTransaction();

            foreach ($this->arrayInfoProduct as $t) {
                // Xóa relation seos -> infoSeo -> contents (nếu có)
                foreach ($t->seos as $seo) {
                    foreach ($seo->infoSeo->contents as $content) {
                        SeoContent::where('id', $content->id)->delete();
                    }
                    RelationSeoProductInfo::where('seo_id', $seo->seo_id)->delete();
                    Seo::where('id', $seo->seo_id)->delete();
                }

                // Tạo dữ liệu mới
                foreach ($this->infoProductSource->seos as $seoS) {
                    $tmp2 = $seoS->infoSeo->toArray();
                    $insert = [];

                    foreach ($tmp2 as $key => $value) {
                        if ($key != 'contents' && $key != 'id') $insert[$key] = $value;
                    }

                    $insert['link_canonical'] = $tmp2['id'];
                    $insert['slug'] = $tmp2['slug'] . '-' . $t->id;
                    $insert['slug_full'] = $tmp2['slug_full'] . '-' . $t->id;

                    $idSeo = Seo::insertItem($insert);

                    if ($insert['language'] == 'vi') {
                        Product::updateItem($t->id, [
                            'seo_id' => $idSeo,
                        ]);
                    }

                    $response[] = $idSeo;

                    RelationSeoProductInfo::insertItem([
                        'seo_id' => $idSeo,
                        'product_info_id' => $t->id,
                    ]);

                    foreach ($seoS->infoSeo->contents as $content) {
                        $contentInsert = str_replace($seoS->infoSeo->slug_full, $insert['slug_full'], $content->content);

                        SeoContent::insertItem([
                            'seo_id' => $idSeo,
                            'content' => $contentInsert,
                            'ordering' => $content->ordering,
                        ]);
                    }
                }

                // Copy relation product và category
                RelationCategoryProduct::where('product_info_id', $t->id)->delete();
                foreach ($this->infoProductSource->categories as $category) {
                    RelationCategoryProduct::insertItem([
                        'category_info_id' => $category->category_info_id,
                        'product_info_id' => $t->id
                    ]);
                }

                // Copy relation product và tag
                RelationTagInfoOrther::where('reference_type', 'product_info')
                    ->where('reference_id', $t->id)
                    ->delete();

                foreach ($this->infoProductSource->tags as $tag) {
                    RelationTagInfoOrther::insertItem([
                        'tag_info_id' => $tag->tag_info_id,
                        'reference_type' => 'product_info',
                        'reference_id' => $t->id
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $response;
    }
}
