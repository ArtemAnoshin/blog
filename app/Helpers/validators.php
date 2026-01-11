<?php

if (!function_exists('is_valid_date')) {
    function is_valid_date($date, $format = 'Y-m-d'): bool
    {
        $dt = \DateTime::createFromFormat($format, $date);

        return $dt && $dt->format($format) === $date;
    }
}
