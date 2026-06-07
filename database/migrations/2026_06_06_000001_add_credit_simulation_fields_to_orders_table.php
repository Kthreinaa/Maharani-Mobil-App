<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'leasing_partner')) {
                $table->string('leasing_partner')->nullable()->after('payment_method');
            }

            if (!Schema::hasColumn('orders', 'credit_dp_percentage')) {
                $table->decimal('credit_dp_percentage', 5, 2)->nullable()->after('leasing_partner');
            }

            if (!Schema::hasColumn('orders', 'credit_dp_amount')) {
                $table->decimal('credit_dp_amount', 15, 2)->nullable()->after('credit_dp_percentage');
            }

            if (!Schema::hasColumn('orders', 'credit_tenor_months')) {
                $table->unsignedSmallInteger('credit_tenor_months')->nullable()->after('credit_dp_amount');
            }

            if (!Schema::hasColumn('orders', 'credit_monthly_installment')) {
                $table->decimal('credit_monthly_installment', 15, 2)->nullable()->after('credit_tenor_months');
            }

            if (!Schema::hasColumn('orders', 'credit_interest_rate')) {
                $table->decimal('credit_interest_rate', 6, 2)->nullable()->after('credit_monthly_installment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'leasing_partner',
                'credit_dp_percentage',
                'credit_dp_amount',
                'credit_tenor_months',
                'credit_monthly_installment',
                'credit_interest_rate',
            ];

            $existing = array_values(array_filter($columns, fn (string $column) => Schema::hasColumn('orders', $column)));

            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
