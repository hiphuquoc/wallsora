<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\SitemapController;

class GenerateChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;
    public $backoff = 10;

    protected $language;
    protected $type;

    public function __construct($language, $type)
    {
        $this->language = $language;
        $this->type = $type;
    }

    public function handle()
    {
        $config = config("tablemysql.{$this->type}");
        if (!$config || empty($config['sitemap'])) return;

        $seoRelation = $config['seo_relation'];
        $relationTable = $seoRelation;
        $idCol = "{$this->type}_id";
        $batchSize = SitemapController::MAX_ITEMS; // số url / sitemap file

        $cursor = DB::table($relationTable . ' as r')
            ->join('seo as s', 's.id', '=', 'r.seo_id')
            ->where('s.language', $this->language)
            ->orderBy('r.' . $idCol)
            ->select('r.' . $idCol)
            ->distinct()
            ->cursor();

        $bufferMin = null;
        $bufferMax = null;
        $countInBuffer = 0;
        $page = 0;

        foreach ($cursor as $row) {
            $id = (int) $row->{$idCol};
            if ($bufferMin === null) $bufferMin = $id;
            $bufferMax = $id;
            $countInBuffer++;

            if ($countInBuffer >= $batchSize) {
                $page++;
                GenerateIdRangeJob::dispatch($this->language, $this->type, $bufferMin, $bufferMax, $page);
                $this->info("Dispatched batch page {$page} for {$this->language}/{$this->type} (ids {$bufferMin}-{$bufferMax})");
                $bufferMin = $bufferMax = null;
                $countInBuffer = 0;
            }
        }

        // Gửi phần còn lại nếu có
        if ($countInBuffer > 0) {
            $page++;
            GenerateIdRangeJob::dispatch($this->language, $this->type, $bufferMin, $bufferMax, $page);
            $this->info("Dispatched final batch page {$page} for {$this->language}/{$this->type} (ids {$bufferMin}-{$bufferMax})");
        }

        // 👉 Sau khi chia xong, tạo file index sitemap cấp ngôn ngữ (ví dụ /vi/company_info.xml)
        if ($page > 0) {
            GenerateLanguageIndexJob::dispatch($this->language, $this->type, $page);
            $this->info("Generated language index for {$this->language}/{$this->type} ({$page} pages)");
        } else {
            $this->info("No sitemap pages generated for {$this->language}/{$this->type}");
        }
    }

    protected function info($message)
    {
        if (app()->runningInConsole()) {
            echo "[Chunk] {$message}\n";
        }
        Log::info("[Chunk] " . $message);
    }
}
