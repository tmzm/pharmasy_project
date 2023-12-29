<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Models\Category;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * @return Response
     */
    public function index(): Response
    {
        $categories = Category::get();

        return count($categories) ? self::apiResponse(200, ReturnMessages::Ok->value, $categories) : self::apiResponse(404, ReturnMessages::NotFound->value);
    }
}
