<?php

namespace App\Console\Commands;

use App\Models\Sales;
use Illuminate\Console\Command;

class CleanupAbandonedSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:cleanup-abandoned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up abandoned sales records that have no items and are older than 1 hour';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $abandonedSales = Sales::where('created_at', '<', now()->subHour())
            ->whereDoesntHave('details')
            ->get();

        $count = $abandonedSales->count();

        foreach ($abandonedSales as $sales) {
            $sales->delete();
        }

        $this->info("Cleaned up {$count} abandoned sales records.");

        return 0;
    }
}
