<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application as Application2;
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

        if($categories)
            return $this->apiResponse(200,'ok',$categories);

        return $this->apiResponse(404,'Not found');
    }
}
