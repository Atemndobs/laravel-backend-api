<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;
use App\Http\Controllers\Api\SongApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('feeder')->group(function () {
    Route::get('/{id?}', [\App\Http\Controllers\Feeds\FeedsController::class, 'index']);
    Route::get('/cloud', [\App\Http\Controllers\Feeds\FeedsController::class, 'cloud']);

});

Route::get('/classify', [\App\Http\Controllers\Api\ClassificationController::class, 'classify']);
Route::get('/analyze/{track}', [\App\Http\Controllers\Api\ClassificationController::class, 'analyze']);

Route::get('/card', [\App\Http\Controllers\CardController::class, 'show']);
Route::get('/new', [\App\Http\Controllers\Feeds\FeedsController::class, 'new']);
Route::get('/sort', [\App\Http\Controllers\Feeds\FeedsController::class, 'sort']);

Orion::resource('songs', \App\Http\Controllers\Api\SongController::class);
Orion::resource('cards', \App\Http\Controllers\Api\CardController::class);
Orion::resource('extracts', \App\Http\Controllers\Api\ExtractController::class);
Orion::resource('feeds', \App\Http\Controllers\Api\FeedController::class);

Route::get('classify/{title}', [\App\Http\Controllers\Api\ClassificationController::class, 'findByTitle']);
Route::post('upload', [\App\Http\Controllers\Api\UploadController::class, 'upload']);
Route::get('upload/strapi', [\App\Http\Controllers\Api\UploadController::class, 'getStrapiUploads']);
Route::post('upload/webhook', [\App\Http\Controllers\Api\UploadController::class, 'strapiUploadsWebhook']);

Route::get('songs/match/{title}', [\App\Http\Controllers\Api\MatchSongController::class, 'getSongMatch']);
Route::get('songs/match/{title}/{$attribute}', [\App\Http\Controllers\Api\MatchSongController::class, 'matchByAttribute']);
Route::get('songs/search/{term}', [\App\Http\Controllers\Api\SongSearchController::class, 'searchSong']);
Route::get('songs/genre/{artist}', [\App\Http\Controllers\Api\SpotifyController::class, 'getArtistGenre']);
