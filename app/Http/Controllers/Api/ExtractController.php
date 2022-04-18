<?php

namespace App\Http\Controllers\Api;

use App\Models\Extract;
use Orion\Http\Controllers\Controller;
use Orion\Concerns\DisableAuthorization;
use Illuminate\Http\Request;

class ExtractController extends Controller
{
    use DisableAuthorization;
    
    protected $model = Extract::class;
}
