<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ArrayReportSheet implements FromArray, ShouldAutoSize, WithTitle
{
    /**
     * @param array<int, array<int, string|int|float>> $rows
     */
    public function __construct(
        private string $title,
        private array $rows
    ) {
    }

    public function array(): array
    {
        return Collection::make($this->rows)
            ->map(fn (array $row) => array_map(function ($value) {
                return is_float($value) ? round($value, 2) : $value;
            }, $row))
            ->all();
    }

    public function title(): string
    {
        return $this->title;
    }
}
