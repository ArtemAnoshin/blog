<?php

namespace App\Scenarios\Blog;

class BuildBlogIndexPageScenario
{
    public function execute($dto)
    {
        return config('fake.blog-index');
    }
}
