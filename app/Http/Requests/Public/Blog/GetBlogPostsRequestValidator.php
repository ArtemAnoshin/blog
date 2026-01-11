<?php

namespace App\Http\Requests\Public\Blog;

use App\Dtos\Public\Blog\BlogIndexPageDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetBlogPostsRequestValidator extends FormRequest
{
    /**
     * Правила валидации с nullable и без strict-валидации
     * Мы не запрещаем запрос при невалидных данных, а просто их игнорируем
     */
    public function rules(): array
    {
        return [
            'title' => [
                'nullable',
                'string',
                'max:125',
            ],

            'date_from' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'date_to' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'page' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],

            'sort_by' => [
                'nullable',
                'string',
            ],

            'sort_direction' => [
                'nullable',
                'string',
            ],
        ];
    }

    /**
     * Нормализация данных перед валидацией
     * Здесь мы "чиним" некоторые очевидные проблемы
     */
    protected function prepareForValidation(): void
    {
        // Приведение к нижнему регистру
        if ($this->has('sort_by')) {
            $this->merge(['sort_by' => strtolower(trim($this->input('sort_by')))]);
        }

        if ($this->has('sort_direction')) {
            $this->merge(['sort_direction' => strtolower(trim($this->input('sort_direction')))]);
        }

        // Очистка title
        if ($this->has('title')) {
            $this->merge(['title' => trim($this->input('title'))]);
        }

        // Нормализация page и limit
        if ($this->has('page') && !is_numeric($this->input('page'))) {
            $this->merge(['page' => 1]);
        }

        if ($this->has('limit') && !is_numeric($this->input('limit'))) {
            $this->merge(['limit' => 10]);
        }
    }

    /**
     * Дополнительная логика валидации без фатальных ошибок
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Если обе даты указаны, но date_from > date_to — просто меняем их местами
            if ($this->date_from && $this->date_to && $this->date_from > $this->date_to) {
                // Не добавляем ошибку, а корректируем данные
                $temp = $this->date_from;
                $this->merge(['date_from' => $this->date_to]);
                $this->merge(['date_to' => $temp]);
            }
        });
    }

    /**
     * Преобразование в DTO с автоматической корректировкой невалидных значений
     */
    public function toDTO(): BlogIndexPageDto
    {
        return new BlogIndexPageDto(
            title: $this->getValidatedOrNull('title'),
            dateFrom: $this->getValidatedDate('date_from'),
            dateTo: $this->getValidatedDate('date_to'),
            page: $this->getValidatedPage(),
            limit: $this->getValidatedLimit(),
            sortBy: $this->getValidatedSortBy(),
            sortDirection: $this->getValidatedSortDirection()
        );
    }

    /**
     * ============================================
     * Вспомогательные методы для безопасного извлечения
     * ============================================
     */
    private function getValidatedOrNull(string $key): ?string
    {
        $value = $this->validated($key);
        return empty($value) ? null : (string) $value;
    }

    private function getValidatedDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            $dateObj = \DateTime::createFromFormat('Y-m-d', $date);
            return $dateObj && $dateObj->format('Y-m-d') === $date ? $date : null;
        } catch (\Exception) {
            return null;
        }
    }

    private function getValidatedPage(): int
    {
        $page = $this->validated('page', 1);

        if (!is_numeric($page) || $page < 1) {
            return 1;
        }

        return (int) $page;
    }

    private function getValidatedLimit(): int
    {
        $limit = $this->validated('limit', 10);

        if (!is_numeric($limit)) {
            return 10;
        }

        $limit = (int) $limit;

        // Мягкие границы
        if ($limit < 1) {
            return 10;
        }

        if ($limit > 100) {
            return 50; // Не ошибка, а ограничение для защиты
        }

        return $limit;
    }

    private function getValidatedSortBy(): string
    {
        $sortBy = $this->validated('sort_by', 'date');
        $allowed = ['date', 'title'];

        return in_array($sortBy, $allowed, true) ? $sortBy : 'date';
    }

    private function getValidatedSortDirection(): string
    {
        $direction = $this->validated('sort_direction', 'desc');

        return in_array($direction, ['asc', 'desc'], true) ? $direction : 'desc';
    }
}
