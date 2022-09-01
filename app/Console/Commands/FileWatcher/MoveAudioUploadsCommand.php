<?php

namespace App\Console\Commands\FileWatcher;

use Illuminate\Console\Command;

class MoveAudioUploadsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move:audio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move uploaded audio to audio folder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $files = glob(storage_path('app/public/uploads/audio/*'));
        foreach ($files as $file) {
            $fileName = basename($file);
            $destination = storage_path('app/public/music/audio/' . $fileName);
            if (!file_exists($destination)) {
                rename($file, $destination);
            }
        }
    }
}
