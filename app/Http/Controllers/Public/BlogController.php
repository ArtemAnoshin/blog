<?php

namespace App\Http\Controllers\Public;

use App\Dtos\Public\Blog\BlogIndexPageRequestDto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Scenarios\Blog\BuildBlogIndexPageScenario;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(
        Request $request,
        BuildBlogIndexPageScenario $scenario
    ): View
    {
        $dto = BlogIndexPageRequestDto::createFromRequest($request);

        $data = $scenario->execute($dto);

        return view('public.blog.index', $data);
    }
}
