<?php

namespace Tests\Unit;

use App\Support\UnitCodeFormatter;
use Tests\TestCase;

class UnitCodeFormatterTest extends TestCase
{
    public function test_it_builds_expected_type_codes_for_common_models(): void
    {
        $this->assertSame('AVZ', UnitCodeFormatter::typeCode('Avanza 1.3 G'));
        $this->assertSame('INV', UnitCodeFormatter::typeCode('Innova 2.0 G'));
        $this->assertSame('CRV', UnitCodeFormatter::typeCode('CR-V 2.4 i-VTEC'));
        $this->assertSame('JZZ', UnitCodeFormatter::typeCode('Jazz RS (GE8 Facelift)'));
    }

    public function test_it_builds_full_unit_code_in_requested_format(): void
    {
        $this->assertSame('MM-AVZ-017-01', UnitCodeFormatter::compose('Avanza 1.3 G', 2017, 1));
        $this->assertSame('MM-CRV-007-12', UnitCodeFormatter::compose('CR-V 2.4 i-VTEC', 2007, 12));
    }
}
