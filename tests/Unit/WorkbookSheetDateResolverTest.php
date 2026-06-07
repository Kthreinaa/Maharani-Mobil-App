<?php

namespace Tests\Unit;

use App\Support\WorkbookSheetDateResolver;
use Carbon\Carbon;
use Tests\TestCase;

class WorkbookSheetDateResolverTest extends TestCase
{
    public function test_aligns_mismatched_date_to_sheet_month(): void
    {
        $date = Carbon::create(2025, 1, 28, 14, 0, 0);

        $resolved = WorkbookSheetDateResolver::alignToSheetMonth($date, 'Desember');

        $this->assertNotNull($resolved);
        $this->assertSame('2025-12-28 14:00:00', $resolved->format('Y-m-d H:i:s'));
    }

    public function test_keeps_matching_sheet_month_unchanged(): void
    {
        $date = Carbon::create(2025, 12, 28, 10, 0, 0);

        $resolved = WorkbookSheetDateResolver::alignToSheetMonth($date, 'Desember');

        $this->assertNotNull($resolved);
        $this->assertSame('2025-12-28 10:00:00', $resolved->format('Y-m-d H:i:s'));
    }
}
