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

        if(count($categories))
            return $this->apiResponse(200,ReturnMessages::Ok->value,$categories);

        return $this->apiResponse(404,ReturnMessages::NotFound->value);
    }
}
