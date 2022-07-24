<?php

namespace App\Console\Commands\Scraper;

use App\Services\Scraper\SoundcloudService;
use Illuminate\Console\Command;

class SoundcloudDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:sc {link?} {--a|artist=null} {--p|playlist=null} {--t|title=null} {--m|mixtape=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download music from Soundcloud by Link, artist name tiles or playlist';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->argument('link') === 'null') {
            $this->info('Please provide a link');
            return 0;
        }
        $link = $this->argument('link');
        $artist = $this->option('artist');

       // $soundcloudService = new SoundcloudService();
       // $song = $soundcloudService->downloadSong($link);
// scdl -l  url --path storage/app/public/audio
        $shell = shell_exec("scdl  -l $link --path storage/app/public/audio");
        // $this->info($shell);
        try {
            $outputs = explode("\n", $shell);

            // search the word "found" from output
            $result = "";
            foreach ($outputs as $output) {
                if (strpos($output, 'Found') !== false) {
                    $result = $output;
                    $this->info($result);
                }
            }
            $this->info("Download Completed ... | $result");
        }catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return 0;
    }
}
