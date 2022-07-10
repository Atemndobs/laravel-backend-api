<?php

namespace App\Console\Commands\Analysis;

use App\Services\ClassifyService;
use Illuminate\Console\Command;

class ReClassifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:reclassify {slug?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $slug = $this->argument('slug');
        $classifyService = new ClassifyService();
        return 0;
    }
}
