<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Blog\GetBlogPostsRequestValidator;
use App\Scenarios\Blog\BuildBlogIndexPageScenario;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(
        GetBlogPostsRequestValidator $request,
        BuildBlogIndexPageScenario $scenario
    ): View
    {
        $dto = $request->toDTO();

        $data = $scenario->execute($dto);

        return view('public.blog.index', $data);
    }
}
