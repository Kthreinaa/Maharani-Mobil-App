<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'transaction_channel')) {
                $table->string('transaction_channel', 20)->default('online')->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'sales_flow')) {
                $table->string('sales_flow', 40)->default('direct_purchase')->after('transaction_channel');
            }
            if (!Schema::hasColumn('orders', 'cancel_reason')) {
                $table->string('cancel_reason')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('orders', 'follow_up_status')) {
                $table->string('follow_up_status', 40)->default('new_lead')->after('cancel_reason');
            }
            if (!Schema::hasColumn('orders', 'next_follow_up_at')) {
                $table->timestamp('next_follow_up_at')->nullable()->after('follow_up_status');
            }
            if (!Schema::hasColumn('orders', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('handled_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('orders', 'document_status')) {
                $table->json('document_status')->nullable()->after('approved_at');
            }
        });

        Schema::table('offers', function (Blueprint $table) {
            if (!Schema::hasColumn('offers', 'customer_channel')) {
                $table->string('customer_channel', 20)->default('online')->after('status');
            }
            if (!Schema::hasColumn('offers', 'follow_up_status')) {
                $table->string('follow_up_status', 40)->default('new_lead')->after('notes');
            }
            if (!Schema::hasColumn('offers', 'lost_reason')) {
                $table->string('lost_reason')->nullable()->after('follow_up_status');
            }
            if (!Schema::hasColumn('offers', 'next_follow_up_at')) {
                $table->timestamp('next_follow_up_at')->nullable()->after('lost_reason');
            }
        });

        Schema::table('test_drives', function (Blueprint $table) {
            if (!Schema::hasColumn('test_drives', 'customer_channel')) {
                $table->string('customer_channel', 20)->default('online')->after('status');
            }
            if (!Schema::hasColumn('test_drives', 'follow_up_status')) {
                $table->string('follow_up_status', 40)->default('appointment')->after('notes');
            }
            if (!Schema::hasColumn('test_drives', 'lost_reason')) {
                $table->string('lost_reason')->nullable()->after('follow_up_status');
            }
            if (!Schema::hasColumn('test_drives', 'next_follow_up_at')) {
                $table->timestamp('next_follow_up_at')->nullable()->after('lost_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('test_drives', function (Blueprint $table) {
            foreach (['next_follow_up_at', 'lost_reason', 'follow_up_status', 'customer_channel'] as $column) {
                if (Schema::hasColumn('test_drives', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('offers', function (Blueprint $table) {
            foreach (['next_follow_up_at', 'lost_reason', 'follow_up_status', 'customer_channel'] as $column) {
                if (Schema::hasColumn('offers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }

            foreach (['document_status', 'approved_at', 'next_follow_up_at', 'follow_up_status', 'cancel_reason', 'sales_flow', 'transaction_channel'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
