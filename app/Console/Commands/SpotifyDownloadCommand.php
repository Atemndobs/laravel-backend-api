<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SpotifyDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download Playlists from Spotify and maybe Youtube too';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Downloading Playlists...');
        $url = $this->argument('url');

        $shell = shell_exec("spotdl  $url --output storage/app/public/audio/");
       // $this->info($shell);
        try {
            $outputs = explode("\n", $shell);

            // search the word "found" from output
            $result = "";
            foreach ($outputs as $output) {
                if (strpos($output, 'Found') !== false) {
                    // remove everthing after ":" from output
                    $result = strpos($output, ':');
                    $this->info($result);

                    $finalOut = explode(" ", $result);
                    $this->info($finalOut[0]);
                    $this->info($finalOut[1]);
                    $this->info($finalOut[2]);

                    // remove "Found YouTube URL for" from output
                    $result = str_replace("Found YouTube URL for", "", $result);
                    $this->info($result);

                }
            }

            $cheDownload = shell_exec("ls -la storage/app/public/audio | grep $result");
            $this->info($cheDownload);
        }catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        return 0;
    }
}
