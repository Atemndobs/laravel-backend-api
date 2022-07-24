<?php

namespace App\Console\Commands;

use App\Services\UploadService;
use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;
use Spatie\Watcher\Exceptions\CouldNotStartWatcher;
use Spatie\Watcher\Watch;
use function Widmogrod\Monad\Control\Doo\in;

class AudioFileWatcherCommand extends Command
{
    use WithoutOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'watch:audio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Watch Audio Directory for New Files ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Watching Audio Directory for New Files...');

        $dir = storage_path('app/public/audio');
        try {
            Watch::path($dir)
                ->onFileCreated(function (string $path) {
                  //  $this->info("New File Created: $path");
                    if (strpos($path, '.mp3') !== false) {
                      //  $this->info("New File Created: $path");
                        $uploadService = new UploadService();
                        $path = str_replace('/var/www/html/storage/app/public/audio', '', $path);
                        $uploadService->importSongs([$path]);
                        $this->line("<fg=magenta>Song Imported | $path</>");
                    }
                })
                ->onFileUpdated(function (string $path) {
                    if (strpos($path, '.mp3') !== false) {
                      //  $this->line("<fg=blue>Song Path Has been Updated | $path</>");
                    }
                })
                ->start();
        }catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return 0;
    }
}
