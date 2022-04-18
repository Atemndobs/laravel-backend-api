<?php

namespace App\Http\Controllers\Api;

use App\Models\Feed;
use Orion\Http\Controllers\Controller;
use Orion\Concerns\DisableAuthorization;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    use DisableAuthorization;

    protected $model  = Feed::class;
}
