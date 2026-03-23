<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Traits;

use Illuminate\Support\Facades\Date;

trait HasDatePresenter
{
    public function dateLocalized(string $column = 'date', string $format = 'LL'): string
    {
        return $this->{$column}->isoFormat($format);
    }

    public function dateTimeLocalized(string $column = 'datetime', string $format = 'LLL'): string
    {
        return $this->{$column}->isoFormat($format);
    }

    public function datetimeOrNow(string $column = 'date'): string
    {
        $date = $this->{$column} ?: Date::now();

        return $date->format('Y-m-d\TH:i');
    }

    public function dateOrNow(string $column = 'date'): string
    {
        $date = $this->{$column} ?: Date::now();

        return $date->format('Y-m-d');
    }

    public function timeOrNow(string $column = 'date'): string
    {
        $date = $this->{$column} ?: Date::now();

        return $date->format('H:i');
    }
}
