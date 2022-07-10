<?php

namespace App\Http\Controllers\Api;

use App\Models\Song;
use Orion\Concerns\DisableAuthorization;
use Orion\Concerns\DisablePagination;
use Orion\Http\Controllers\Controller;

class SongController extends Controller
{
    use DisableAuthorization;
    use DisablePagination;

    /**
     * @var string
     */
    protected $model = Song::class;
}
