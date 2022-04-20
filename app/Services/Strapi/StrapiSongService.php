<?php

namespace App\Services\Strapi;

use App\Models\Song;
use App\Services\UploadService;
use Dbfx\LaravelStrapi\LaravelStrapi;
use Http\Client\Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use function Psy\debug;
use const Widmogrod\Monad\Writer\log;

class StrapiSongService
{
    public string $baseUrl ;
    public LaravelStrapi $laravelStrapi;

    /**
     */
    public function __construct()
    {
        $this->baseUrl = config('strapi.url');
        $url = env('MEILISEARCH_HOST');

        $this->laravelStrapi = new LaravelStrapi();
    }


    /**
     * @return array
     */
    public function importStrapiUploads()
    {
        $prepareImports = [];

        try {
            $response = Http::get('http://localhost:1337/api/upload/files');
            $strapiUploads = $response->json();
            $prepareImports = $this->prepareImports($strapiUploads);
        }catch (\Exception $exception){
            info($exception->getMessage());
        }
        return $prepareImports;
    }

    /**
     * @param array $song
     * @return array
     */
    public function importStrapiSong(array $song)
    {
        return $this->importAndSave($song, [], new UploadService());
    }

    /**
     * @param string $title
     * @return mixed
     */
    public function existing(string $title)
    {
        return  Song::where('title', '=', $title)->first();
    }

    /**
     * @param $strapiUploads
     * @return array
     */
    public function prepareImports($strapiUploads): array
    {
        $response = [];
        $uploadService = new UploadService();
        foreach ($strapiUploads as $upload){
            $response = $this->importAndSave($upload, $response, $uploadService);
        }
        return $response;
    }

    /**
     * @param mixed $upload
     * @param array $response
     * @param UploadService $uploadService
     * @return array
     */
    protected function importAndSave(mixed $upload, array $response, UploadService $uploadService): array
    {
        $base = "http://mage.tech:1337";
        $song = new Song();

        if ($existing = $this->existing($upload['name'])) {
            $response[] = $existing;
        } else {
            $song->title = $upload['name'];
            $song->path = $base . $upload['url'];
            $song->link = $upload['hash'];
            $song->source = $upload['provider'];
            $song->status = 'queued';
            // http://mage.tech:8899/api/songs/match/ODIE%20-%20North%20Face.mp3
            $api_url = env('APP_URL') . '/api/songs/match/';
            $song->related_songs = $api_url . $song->title;

            $uploadService->fillSong(
                $song->source,
                $song,
                $upload['mime'],
                $upload['name'],
                $upload['ext']
            );
            $song->save();
            $response[] = $song;
        }
        return $response;
    }
}
