<?php

namespace App\Services\Scraper;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BandcampService
{
    use Tools;
    public string $baseUrl = "https://bandcamp.com";

    public function getSongLinksByArtisName(string $artist)
    {
        $url = "$this->baseUrl/search?q=$artist&item_type";
        $songLinks = $this->getSongLinks($url);

        $searchQuery = Str::slug($artist);
        $artistSearchQuery = str_replace('-', '', $searchQuery);
        $artistSubDomain = $artistSearchQuery . '.bandcamp.com';
        dump($artistSubDomain);

        dump(['searchQuery' => $searchQuery]);
        $links = [];
        foreach ($songLinks as $songLink) {
            if (str_contains($songLink, $artistSubDomain)) {
                $links[] = $songLink;
            }
        }

        if (count($links) > 0) {
            foreach ($links as $link) {
                $collectedSongLinks = $this->getSongLinks($link);
            }
        }

        $trackLinks = [];
        foreach ($collectedSongLinks as $foundLink) {
            if(str_contains($foundLink, '/track')) {
                $trackLinks[] = "https://" . $artistSubDomain . $foundLink;
            }
        }
        return $trackLinks;
    }

    public function downloadSong(string $songLink)
    {
        $fileName = explode('/', $songLink);
        $fileName = Arr::last($fileName);
        $basDir = Storage::path('public/uploads/audio');

        $dnload = shell_exec("bandcamp-dl $songLink --base-dir=storage/app/public/uploads/audio --template=%{artist}-%{title}");

        $allFiles = Storage::allFiles('public/uploads/audio');
        foreach ($allFiles as $file) {
            $imageNewName = null;
            $fileName = basename($file);
            if (str_contains($fileName, '.mp3')) {
                $audioName = $fileName;
                $audioPath = $file;
                $imageNewName = str_replace('.mp3', '.jpg', $audioName);
            }
            if (str_contains($fileName, '.jpg')) {
                $imageName = $fileName;
                $imagePath = $file;
            }
        }

        try {
            // remove .jpg from image name
            $imageNewName = str_replace('.jpg', '', $imageNewName);
            $imageNewName = Str::slug($imageNewName, '_');
            $imageNewName = $imageNewName . '.jpg';

            $audioName = str_replace('.mp3', '', $audioName);
            $audioName = Str::slug($audioName, '_');
            $audioName = $audioName . '.mp3';

            dump([
                'audioName' => $audioName,
                'imageName' => $imageName,
                'imageNewName' => $imageNewName,
            ]);
            rename(Storage::path($imagePath), $basDir . '/' .$imageNewName );
            rename(Storage::path($audioPath), $basDir . '/' .$audioName );
            // move image to public/images
            Storage::move('public/uploads/audio/' . $imageNewName, 'public/images/' . $imageNewName);
            Storage::move('public/uploads/audio/' . $audioName, 'public/audio/' . $audioName);
        }catch (\Exception $exception){
            dump([
                'exception' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]);
        }
        return $fileName;
    }
}
