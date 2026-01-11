<?php

namespace App\Dtos\Public\Blog;

/**
 * DTO для страницы блога.
 * Всегда находится в валидном состоянии.
 * Не выбрасывает исключения при инициализации.
 */
readonly class BlogIndexPageDto
{
    public function __construct(
        public ?string $title = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public int $page = 1,
        public int $limit = 10,
        public string $sortBy = 'date',
        public string $sortDirection = 'desc'
    ) {
        // Внутренняя нормализация вместо валидации
        $this->normalize();
    }

    /**
     * Приведение к корректным значениям
     */
    private function normalize(): void
    {
        // Гарантируем, что limit в разумных пределах
        if ($this->limit < 1) {
            $this->limit = 10;
        } elseif ($this->limit > 100) {
            $this->limit = 50; // Мягкое ограничение
        }

        // Гарантируем корректные значения сортировки
        $allowedSortFields = ['date', 'title'];
        if (!in_array($this->sortBy, $allowedSortFields, true)) {
            $this->sortBy = 'date';
        }

        if (!in_array($this->sortDirection, ['asc', 'desc'], true)) {
            $this->sortDirection = 'desc';
        }

        // Если указаны обе даты, гарантируем dateFrom <= dateTo
        if ($this->dateFrom && $this->dateTo && $this->dateFrom > $this->dateTo) {
            // Меняем местами
            $temp = $this->dateFrom;
            $this->dateFrom = $this->dateTo;
            $this->dateTo = $temp;
        }
    }
}
