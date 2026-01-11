<?php

namespace Tests\Unit\Public\Blog;

use App\Dtos\Public\Blog\BlogIndexPageDto;
use App\Http\Controllers\Public\BlogController;
use App\Scenarios\Blog\BuildBlogIndexPageScenario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class BuildBlogIndexPageScenarioTest extends TestCase
{
    public function test_scenario_returns_correct_structure(): void
    {
        // Arrange
        $dto = new BlogIndexPageDto();
        $scenario = new BuildBlogIndexPageScenario();

        // Act
        $result = $scenario->execute($dto);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertArrayHasKey('filters', $result);

        // Проверяем структуру постов
        $this->assertIsArray($result['data']);
        $this->assertGreaterThan(0, count($result['data']));

        // Проверяем первый пост
        $firstPost = $result['data'][0];
        $this->assertArrayHasKey('id', $firstPost);
        $this->assertArrayHasKey('title', $firstPost);
        $this->assertArrayHasKey('slug', $firstPost);
        $this->assertArrayHasKey('excerpt', $firstPost);
        $this->assertArrayHasKey('date', $firstPost);
        $this->assertArrayHasKey('image_url', $firstPost);

        // Проверяем пагинацию
        $pagination = $result['pagination'];
        $this->assertArrayHasKey('current_page', $pagination);
        $this->assertArrayHasKey('per_page', $pagination);
        $this->assertArrayHasKey('total', $pagination);
        $this->assertArrayHasKey('last_page', $pagination);

        // Проверяем фильтры
        $filters = $result['filters'];
        $this->assertArrayHasKey('title', $filters);
        $this->assertArrayHasKey('date_from', $filters);
        $this->assertArrayHasKey('date_to', $filters);
        $this->assertArrayHasKey('sort_by', $filters);
        $this->assertArrayHasKey('sort_direction', $filters);
    }
}
