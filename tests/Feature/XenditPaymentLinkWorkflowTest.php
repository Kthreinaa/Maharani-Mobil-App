<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class XenditPaymentLinkWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_is_redirected_to_xendit_payment_link_when_gateway_is_enabled(): void
    {
        config()->set('services.xendit.secret_key', 'xnd_development_test');
        config()->set('payments.booking_fee', 2500000);

        Http::fake([
            'https://api.xendit.co/v2/invoices' => Http::response([
                'id' => 'inv-test-001',
                'external_id' => 'mm-order-1-test',
                'status' => 'PENDING',
                'invoice_url' => 'https://checkout.xendit.test/inv-test-001',
            ], 200),
        ]);

        $customer = User::factory()->create(['role' => 'customer']);
        $marketing = User::factory()->create(['role' => 'marketing']);

        $car = Car::create([
            'kode_unit' => 'MM-XDT-001',
            'merk' => 'Toyota',
            'tipe' => 'Raize',
            'tahun' => 2023,
            'harga' => 275000000,
            'kilometer' => 5000,
            'status' => 'reserved',
            'photos' => [],
            'created_by' => $marketing->id,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'pending',
            'total' => 275000000,
            'payment_method' => null,
            'transaction_channel' => 'online',
            'sales_flow' => 'direct_purchase',
            'document_status' => Order::pendingDocumentStatuses(),
        ]);

        $this->actingAs($customer)
            ->post(route('customer.payments.store'), [
                'order_id' => $order->id,
                'method' => 'transfer',
            ])
            ->assertRedirect('https://checkout.xendit.test/inv-test-001');

        $payment = Payment::where('order_id', $order->id)->first();

        $this->assertNotNull($payment);
        $this->assertSame('xendit', $payment->gateway_provider);
        $this->assertSame('inv-test-001', $payment->gateway_reference);
        $this->assertSame('PENDING', $payment->gateway_status);
        $this->assertSame('https://checkout.xendit.test/inv-test-001', $payment->gateway_checkout_url);
        $this->assertSame('pending', $payment->status);

        $order->refresh();
        $this->assertSame('confirmed', $order->status);
        $this->assertSame('transfer', $order->payment_method);
    }

    public function test_customer_can_create_xendit_invoice_for_full_payment_when_selected(): void
    {
        config()->set('services.xendit.secret_key', 'xnd_development_test');
        config()->set('payments.booking_fee', 2500000);

        Http::fake([
            'https://api.xendit.co/v2/invoices' => Http::response([
                'id' => 'inv-test-full-001',
                'external_id' => 'mm-order-full-test',
                'status' => 'PENDING',
                'invoice_url' => 'https://checkout.xendit.test/inv-test-full-001',
            ], 200),
        ]);

        $customer = User::factory()->create(['role' => 'customer']);
        $marketing = User::factory()->create(['role' => 'marketing']);

        $car = Car::create([
            'kode_unit' => 'MM-XDT-003',
            'merk' => 'Honda',
            'tipe' => 'Brio',
            'tahun' => 2021,
            'harga' => 124000000,
            'kilometer' => 9000,
            'status' => 'reserved',
            'photos' => [],
            'created_by' => $marketing->id,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'pending',
            'total' => 124000000,
            'payment_method' => null,
            'transaction_channel' => 'online',
            'sales_flow' => 'direct_purchase',
            'document_status' => Order::pendingDocumentStatuses(),
        ]);

        $this->actingAs($customer)
            ->post(route('customer.payments.store'), [
                'order_id' => $order->id,
                'method' => 'transfer',
                'payment_plan' => 'full',
            ])
            ->assertRedirect('https://checkout.xendit.test/inv-test-full-001');

        $payment = Payment::where('order_id', $order->id)->first();

        $this->assertNotNull($payment);
        $this->assertSame(124000000.0, (float) $payment->amount);
        $this->assertSame('xendit', $payment->gateway_provider);
    }

    public function test_xendit_webhook_marks_payment_as_verified_automatically(): void
    {
        config()->set('services.xendit.webhook_token', 'webhook-secret');

        $customer = User::factory()->create(['role' => 'customer']);
        $marketing = User::factory()->create(['role' => 'marketing']);

        $car = Car::create([
            'kode_unit' => 'MM-XDT-002',
            'merk' => 'Honda',
            'tipe' => 'HRV',
            'tahun' => 2022,
            'harga' => 338000000,
            'kilometer' => 7000,
            'status' => 'available',
            'photos' => [],
            'created_by' => $marketing->id,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'confirmed',
            'total' => 338000000,
            'payment_method' => 'transfer',
            'transaction_channel' => 'online',
            'sales_flow' => 'direct_purchase',
            'document_status' => Order::pendingDocumentStatuses(),
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'method' => 'transfer',
            'gateway_provider' => 'xendit',
            'gateway_reference' => 'inv-test-002',
            'gateway_external_id' => 'mm-order-2-test',
            'gateway_checkout_url' => 'https://checkout.xendit.test/inv-test-002',
            'gateway_status' => 'PENDING',
            'amount' => 2500000,
            'status' => 'pending',
        ]);

        $this->postJson(
            route('webhooks.xendit.invoices'),
            [
                'id' => 'inv-test-002',
                'external_id' => 'mm-order-2-test',
                'status' => 'PAID',
                'paid_amount' => 2500000,
                'paid_at' => '2026-06-03T08:30:00Z',
                'payment_method' => 'BANK_TRANSFER',
                'payment_channel' => 'MANDIRI',
            ],
            ['x-callback-token' => 'webhook-secret']
        )->assertOk();

        $payment->refresh();
        $order->refresh();
        $car->refresh();

        $this->assertSame('verified', $payment->status);
        $this->assertSame('PAID', $payment->gateway_status);
        $this->assertSame('MANDIRI', $payment->gateway_channel);
        $this->assertSame('gateway', $payment->handled_role);
        $this->assertSame('confirmed', $order->status);
        $this->assertSame('reserved', $car->status);
    }
}
