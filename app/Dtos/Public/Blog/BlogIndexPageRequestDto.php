<?php

namespace App\Dtos\Public\Blog;

use Illuminate\Http\Request;

/**
 * DTO для страницы блога.
 * Всегда находится в валидном состоянии.
 * Не выбрасывает исключения при инициализации.
 */
readonly class BlogIndexPageRequestDto
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
        // Если указаны обе даты, гарантируем dateFrom <= dateTo
        if ($this->dateFrom && $this->dateTo && $this->dateFrom > $this->dateTo) {
            // Меняем местами
            $temp = $this->dateFrom;
            $this->dateFrom = $this->dateTo;
            $this->dateTo = $temp;
        }
    }

    /**
     * Создание DTO из HTTP-запроса
     */
    public static function createFromRequest(Request $request): self
    {
        return new self(
            title: self::extractTitle($request),
            dateFrom: self::extractDateFrom($request),
            dateTo: self::extractDateTo($request),
            page: self::extractPage($request),
            limit: self::extractLimit($request),
            sortBy: self::extractSortBy($request),
            sortDirection: self::extractSortDirection($request)
        );
    }

    /**
     * Извлечение и нормализация заголовка
     */
    private static function extractTitle(Request $request): ?string
    {
        $title = $request->input('title');

        // Если значение не передано или null
        if ($title === null) {
            return null;
        }

        // Приводим к строке (на случай, если пришло число или другой тип)
        $title = (string) $title;
        $title = trim($title);

        // Если после трима строка пустая - возвращаем null
        if ($title === '') {
            return null;
        }

        // Ограничение длины
        return substr($title, 0, 125);
    }

    /**
     * Извлечение и валидация даты "от"
     */
    private static function extractDateFrom(Request $request): ?string
    {
        $date = $request->input('date_from');

        if (empty($date)) {
            return null;
        }

        return is_valid_date($date) ? $date : null;
    }

    /**
     * Извлечение и валидация даты "до"
     */
    private static function extractDateTo(Request $request): ?string
    {
        $date = $request->input('date_to');

        if (empty($date)) {
            return null;
        }

        return is_valid_date($date) ? $date : null;
    }

    /**
     * Извлечение номера страницы
     */
    private static function extractPage(Request $request): int
    {
        $page = $request->input('page', 1);

        if (!is_numeric($page)) {
            return 1;
        }

        $page = (int) $page;

        return $page > 0 ? $page : 1;
    }

    /**
     * Извлечение лимита записей
     */
    private static function extractLimit(Request $request): int
    {
        $limit = $request->input('limit', 10);

        if (!is_numeric($limit)) {
            return 10;
        }

        $limit = (int) $limit;

        // Мягкие ограничения
        if ($limit < 1) {
            return 10;
        }

        if ($limit > 100) {
            return 50; // Автоматическое ограничение без ошибки
        }

        return $limit;
    }

    /**
     * Извлечение поля для сортировки
     */
    private static function extractSortBy(Request $request): string
    {
        $sortBy = $request->input('sort_by', 'date');

        if (!is_string($sortBy)) {
            return 'date';
        }

        $sortBy = strtolower(trim($sortBy));
        $allowed = ['date', 'title'];

        return in_array($sortBy, $allowed, true) ? $sortBy : 'date';
    }

    /**
     * Извлечение направления сортировки
     */
    private static function extractSortDirection(Request $request): string
    {
        $direction = $request->input('sort_direction', 'desc');

        if (!is_string($direction)) {
            return 'desc';
        }

        $direction = strtolower(trim($direction));

        return in_array($direction, ['asc', 'desc'], true) ? $direction : 'desc';
    }
}
