<?php

namespace App\Console\Commands\FileWatcher;

use App\Services\UploadService;
use Spatie\Watcher\Watch;

trait Tools
{
    /**
     * @param string $dir
     * @param string $destination
     * @return void
     */
    public function watchFolder(string $dir, string $destination): void
    {
        try {
            Watch::path($dir)
                ->onFileCreated(function (string $path) use ($destination) {
                    sleep(2);
                    if (str_contains($path, '.mp3') || str_contains($path, '.vav')) {
                        $fileName = basename($path);
                        $audioPath = storage_path("app/public/$destination/".$fileName);
                        rename($path, $audioPath);
                        sleep(2);
                        $this->call('song:import');
                        $this->line("<fg=magenta>Song Imported | $path</>");
                    }
                })
                ->onFileUpdated(function (string $path) {
                    if (str_contains($path, '.mp3')) {
                        $this->line("<fg=blue>Song Path Has been Updated | $path</>");
                    }
                })
                ->start();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
