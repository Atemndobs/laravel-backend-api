<?php

namespace App\Http\Controllers\Api;

use App\Events\AnalyzeSongEvent;
use App\Http\Controllers\Controller;
use App\Jobs\AnalyzeSongJob;
use App\Models\Song;
use App\Services\MoodAnalysisService;
use App\Services\Strapi\StrapiSongService;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\DataCollector\EventDataCollector;
use voku\helper\ASCII;
use function Psy\debug;
use function Widmogrod\Monad\Control\Doo\in;

class UploadController extends Controller{

    public Request $request;
    public UploadService $uploadService;
    public StrapiSongService $strapiSongService;

    /**
     * @param uploadService $uploadService
     * @param Song $song
     */
    public function __construct(
        UploadService $uploadService,
        Song $song,
        Request $request,
        StrapiSongService $strapiSongService
    )
    {
        $this->uploadService = $uploadService;
        $this->song = $song;
        $this->request = $request;
        $this->strapiSongService = $strapiSongService;
    }
    public function upload(Request $request)
    {
        $data = $request->path;
        if (is_array($data)){
           return $this->uploadService->batchUpload($data);
        }
        return $this->uploadService->uploadSong($data);
    }

    public function getStrapiUploads(): array
    {
        return $this->strapiSongService->importStrapiUploads();
    }

    public function strapiUploadsWebhook(Request $request) : void
    {
        $payload = $request->all();


        if ($payload['event'] === "media.create"){
            $song = $payload['media'];
            $res = $this->strapiSongService->importStrapiSong($song);

            $title = $res[0]->title;
            info("Imported : " . $title);
            AnalyzeSongJob::dispatch($title)->onConnection('database')->onQueue('analyze');
            // broadcast(new AnalyzeSongEvent($title))->toOthers();
        }

        if ($payload['event'] === "media.update"){
            $song = $payload['media'];
            $res = $this->strapiSongService->importStrapiSong($song);

            info("Imported : " . $res[0]->path);
        }

        if ($payload['event'] === "media.delete"){
            $track = $payload['media'];
            $song =   Song::where('title', '=', $track['name'])->first();
            $song->delete();

            info("Deleted : " . $song->title);
        }

    }
}
