<?php

namespace App\Http\Controllers;

use App\Http\Helpers\LogicHelper;
use App\Http\Helpers\TableHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponse, LogicHelper, TableHelper, ApiResponse;
}
