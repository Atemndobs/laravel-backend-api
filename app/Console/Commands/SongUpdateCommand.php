<?php

namespace App\Console\Commands;

use App\Models\Song;
use Doctrine\DBAL\Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SongUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batch Update Song Details From AnalyseAPI';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // http://localhost:3000/song
        $data = [];
        $updated =[];
        $songs = Song::all();
        $base_url = "http://localhost:3000/music/update?id=";
        /** @var Song $song */
        foreach ($songs as  $song){
            if ($song->image === null){
                Http::get($base_url . $song->id);

                $total = count($songs);
                $rest = $total - count($updated);
                $this->output->info("Updated  $song->title   | $rest songs left");

                sleep(20);
            }else{
                $updated[] = $song->title;
            }

            $data[] = [
                'id' =>  $song->id,
                'title' =>  $song->title,
                'status' =>'updated',
            ];
        }
        $headers = [
            'id',
            'title',
            'status'
        ];

        $this->output->table($headers, $data);

        $total = count($data);
        $this->output->info("Updated  $total songs");
        info("=========================================UPDATE SONGS==========================================");
        return 0;
    }
}
