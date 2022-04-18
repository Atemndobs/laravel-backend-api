<?php

namespace App\Http\Controllers\Api;

use App\Models\Card;
use Orion\Http\Controllers\Controller;
use Orion\Concerns\DisableAuthorization;
use Illuminate\Http\Request;

class CardController extends Controller
{
    use DisableAuthorization;
    
    protected $model = Card::class;
}
