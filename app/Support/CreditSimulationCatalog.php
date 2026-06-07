<?php

namespace App\Support;

class CreditSimulationCatalog
{
    public static function partners(): array
    {
        return [
            ['code' => 'clipan-finance', 'name' => 'Clipan Finance', 'annual_rate' => 8.10],
            ['code' => 'cesul-finance', 'name' => 'Cesul Finance', 'annual_rate' => 8.35],
            ['code' => 'bca-finance', 'name' => 'BCA Finance', 'annual_rate' => 7.85],
            ['code' => 'cimb-niaga-finance', 'name' => 'CIMB Niaga Finance', 'annual_rate' => 8.25],
            ['code' => 'bri-finance', 'name' => 'BRI Finance', 'annual_rate' => 8.40],
            ['code' => 'mandiri-utama-finance', 'name' => 'Mandiri Utama Finance', 'annual_rate' => 8.05],
            ['code' => 'mizuo-finance', 'name' => 'Mizuo Finance', 'annual_rate' => 8.30],
            ['code' => 'adira-finance', 'name' => 'Adira Finance', 'annual_rate' => 8.60],
            ['code' => 'oto-mukti-arta-finance', 'name' => 'Oto Mukti Arta Finance', 'annual_rate' => 8.45],
            ['code' => 'bfi-syariah', 'name' => 'BFI Syariah', 'annual_rate' => 8.55],
        ];
    }

    public static function tenors(): array
    {
        return [36, 48, 60];
    }

    public static function minimumDpPercentage(): float
    {
        return 20.0;
    }

    public static function defaultPartner(): array
    {
        return self::partners()[0];
    }

    public static function find(string $code): ?array
    {
        foreach (self::partners() as $partner) {
            if ($partner['code'] === $code) {
                return $partner;
            }
        }

        return null;
    }

    public static function simulate(float $price, float $dpAmount, int $tenor, string $partnerCode): array
    {
        $price = max($price, 0);
        $partner = self::find($partnerCode) ?? self::defaultPartner();
        $minimumDpAmount = round($price * (self::minimumDpPercentage() / 100), 2);
        $dpAmount = min(max($dpAmount, $minimumDpAmount), max($price - 1, $minimumDpAmount));
        $financedAmount = max($price - $dpAmount, 0);
        $annualRate = (float) $partner['annual_rate'];
        $interestAmount = $financedAmount * ($annualRate / 100) * ($tenor / 12);
        $monthlyInstallment = $tenor > 0
            ? round(($financedAmount + $interestAmount) / $tenor, 2)
            : 0.0;

        return [
            'partner' => $partner,
            'price' => round($price, 2),
            'tenor_months' => $tenor,
            'minimum_dp_amount' => $minimumDpAmount,
            'dp_amount' => round($dpAmount, 2),
            'dp_percentage' => $price > 0 ? round(($dpAmount / $price) * 100, 2) : self::minimumDpPercentage(),
            'financed_amount' => round($financedAmount, 2),
            'annual_rate' => $annualRate,
            'interest_amount' => round($interestAmount, 2),
            'monthly_installment' => $monthlyInstallment,
        ];
    }
}
