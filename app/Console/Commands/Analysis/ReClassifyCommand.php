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
    protected $signature = 'song:reclassify {slug?} {--all}';

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
        $all = $this->option('all');
        $classifyService = new ClassifyService();
        // if slug is empty, write console info : no slug given, all songs will be reclassified

        // start progress bar

        if (empty($slug)) {
            $this->info('No slug given, all songs will be reclassified');
            if ($all) {
                $this->info('All songs will be reclassified');

                return $this->classify($classifyService, $slug);
            }
            $answer = $this->ask('Are you sure you want to reclassify all songs? (y/n)');
            if ($answer != 'y') {
                $this->error('Aborting');

                return 1;
            }
        } else {
            $this->info('Reclassifying song with slug: '.$slug);
        }

        return $this->classify($classifyService, $slug);
    }

    /**
     * @param  ClassifyService  $classifyService
     * @param  array|string|null  $slug
     * @return int
     */
    public function classify(ClassifyService $classifyService, array|string|null $slug): int
    {
        $bar = $this->output->createProgressBar(100);
        $bar->start();
        $this->newLine();

        try {
            $classifiedSongs = $classifyService->reClassify($slug);
            $this->table(['slug', 'classification_properties', 'values'], $classifiedSongs);
            $this->newLine();
            $bar->finish();

            return 0;
        } catch (NotAnalyzedException $e) {
            $this->error($e->getMessage());
            $this->table(['slug', 'message'], [
                [$slug, $e->getMessage()],
            ]);

            return 1;
        }
    }
}
