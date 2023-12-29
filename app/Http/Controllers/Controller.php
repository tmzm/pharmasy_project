<?php

namespace App\Http\Controllers;

use App\Http\Helpers\AuthHelper;
use App\Http\Helpers\CreateUpdateHelper;
use App\Http\Helpers\LogicHelper;
use App\Http\Helpers\MessageHelper;
use App\Http\Helpers\TableGetterHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponse, LogicHelper, TableGetterHelper, CreateUpdateHelper, AuthHelper, MessageHelper;
}
