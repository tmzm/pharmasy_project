<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Models\Category;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     */
    public function index()
    {
        $categories = Category::get();

        count($categories) ? self::ok($categories) : self::notFound();
    }
}
