<?php

namespace App\Http\Controllers\Api;
use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Orion\Concerns\DisablePagination;
use Orion\Http\Controllers\Controller;
use Orion\Concerns\DisableAuthorization;

class FileController extends Controller
{
    use DisableAuthorization;
    use DisablePagination;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected $model = File::class;

}
