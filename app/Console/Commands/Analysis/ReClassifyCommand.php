<?php

namespace App\Console\Commands\Analysis;

use App\Exceptions\SongException\NotAnalyzedException;
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
        try {
            $classifiedSongs = $classifyService->reClassify($slug);
        } catch (NotAnalyzedException $e) {
            $this->error($e->getMessage());
            $this->table(['slug', 'message'], [
                [$slug, $e->getMessage()],
            ]);
            return 1;
        }

        // table of classified songs
        $this->table(['slug', 'classification_properties',], $classifiedSongs);
        return 0;
    }
}
