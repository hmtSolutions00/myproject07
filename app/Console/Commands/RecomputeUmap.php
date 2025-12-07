<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UmapService;

class RecomputeUmap extends Command
{
    protected $signature = 'umap:recompute {batchTag?}';
    protected $description = 'Recompute UMAP embedding for all approved menteris';

    public function handle(UmapService $service)
    {
        $tag = $this->argument('batchTag');
        $this->info("Recomputing UMAP...");

        $service->recomputeAll($tag);

        $this->info("Done.");
        return 0;
    }
}
