<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\GenerateMainSitemapJob;
use App\Jobs\GenerateChunkJob;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Dispatch sitemap generation jobs to queue';

    public function handle()
    {
        $this->info('Dispatching sitemap jobs...');

        GenerateMainSitemapJob::dispatch();

        foreach (config('language') as $lang) {
            if (empty($lang['key'])) continue;
            $language = $lang['key'];

            foreach (config('tablemysql') as $type => $config) {
                if (empty($config['sitemap'])) continue;
                GenerateChunkJob::dispatch($language, $type);
            }
        }

        $this->info('All jobs dispatched! Run: php artisan queue:work');
    }
}