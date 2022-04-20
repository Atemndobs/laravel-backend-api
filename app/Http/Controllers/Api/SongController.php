<?php

namespace App\Http\Controllers\Api;
use App\Models\Song;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Orion\Concerns\DisablePagination;
use Orion\Http\Controllers\Controller;
use Orion\Concerns\DisableAuthorization;

class SongController extends Controller
{
    use DisableAuthorization;
    use DisablePagination;

    protected $model = Song::class;

}
