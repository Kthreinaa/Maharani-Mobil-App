<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Order;
use App\Models\Payment;
use App\Models\TestDrive;
use App\Models\User;
use App\Support\TestDriveOrderLinker;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class PaymentValidationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_checkout_stays_as_draft_until_payment_succeeds(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $otherCustomer = User::factory()->create(['role' => 'customer']);
        $marketing = User::factory()->create(['role' => 'marketing']);

        $car = Car::create([
            'kode_unit' => 'STOCK-001',
            'merk' => 'Toyota',
            'tipe' => 'Calya Facelift G 1.2',
            'tahun' => 2019,
            'harga' => 136000000,
            'kilometer' => 72168,
            'status' => 'available',
            'photos' => [],
            'created_by' => $marketing->id,
        ]);

        $this->actingAs($customer)
            ->post(route('customer.orders.store'), [
                'car_id' => $car->id,
                'payment_method' => 'transfer',
                'payment_plan' => 'booking',
                'booking_fee_agreement' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame('available', $car->fresh()->status);

        $draftToken = session('active_checkout_draft');

        $this->assertNotEmpty($draftToken);

        $this->actingAs($customer)
            ->post(route('customer.payments.simulate-success'), [
                'draft_token' => $draftToken,
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $firstOrder = Order::query()->first();

        $this->assertNotNull($firstOrder);
        $this->assertSame('reserved', $car->fresh()->status);
        $this->assertDatabaseCount('orders', 1);

        $this->actingAs($otherCustomer)
            ->post(route('customer.orders.store'), [
                'car_id' => $car->id,
                'payment_method' => 'transfer',
                'payment_plan' => 'booking',
                'booking_fee_agreement' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('car_id');

        $this->assertSame('reserved', $car->fresh()->status);
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_customer_selects_payment_method_and_internal_team_validates_it(): void
    {
        $this->assertSame('available', Order::pendingDocumentStatuses()['stnk'] ?? null);
        $this->assertSame('available', Order::pendingDocumentStatuses()['bpkb'] ?? null);

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $marketing = User::factory()->create([
            'role' => 'marketing',
        ]);

        $supervisor = User::factory()->create([
            'role' => 'supervisor',
        ]);

        $car = Car::create([
            'kode_unit' => 'PAY-001',
            'merk' => 'Toyota',
            'tipe' => 'Yaris',
            'tahun' => 2022,
            'harga' => 245000000,
            'kilometer' => 1200,
            'status' => 'reserved',
            'photos' => [],
            'created_by' => $marketing->id,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'pending',
            'total' => 245000000,
            'payment_method' => null,
        ]);

        $this->actingAs($customer)
            ->post(route('customer.payments.store'), [
                'order_id' => $order->id,
                'method' => 'transfer',
            ])
            ->assertRedirect(route('order.tracking', ['order' => $order->id]));

        $payment = Payment::where('order_id', $order->id)->first();

        $this->assertNotNull($payment);
        $this->assertSame('transfer', $payment->method);
        $this->assertNull($payment->proof_file);
        $this->assertSame('pending', $payment->status);

        $order->refresh();
        $this->assertSame('confirmed', $order->status);

        $this->actingAs($customer)
            ->get(route('payment.upload.page', ['order' => $order->id]))
            ->assertRedirect(route('order.tracking', ['order' => $order->id]));

        $this->actingAs($marketing)
            ->get(route('marketing.transactions.index'))
            ->assertOk()
            ->assertSee('Dicatat internal')
            ->assertSee('ROLE');

        $this->actingAs($supervisor)
            ->patch(route('supervisor.payments.verify', $payment))
            ->assertRedirect();

        $payment->refresh();
        $order->refresh();

        $this->assertSame('verified', $payment->status);
        $this->assertSame('confirmed', $order->status);
        $this->assertSame('pending', $order->document_status['invoice'] ?? null);
        $this->assertSame('pending', $order->document_status['receipt'] ?? null);
        $this->assertSame('pending', $order->document_status['handover_note'] ?? null);

        $this->actingAs($supervisor)
            ->get(route('supervisor.payments.show', $payment))
            ->assertOk()
            ->assertSee('divalidasi langsung oleh supervisor')
            ->assertSee('baru tampil setelah pembayaran lunas');

        $this->actingAs($supervisor)
            ->patch(route('supervisor.orders.syncPayment', $order), [
                'purchase_method' => 'cash',
                'payment_method' => 'transfer',
                'amount' => $order->total,
            ])
            ->assertRedirect();

        $payment->refresh();
        $order->refresh();

        $this->assertSame('verified', $payment->status);
        $this->assertSame('completed', $order->status);
        $this->assertSame('ready', $order->document_status['invoice'] ?? null);
        $this->assertSame('ready', $order->document_status['receipt'] ?? null);
        $this->assertSame('ready', $order->document_status['handover_note'] ?? null);
    }

    public function test_customer_can_simulate_local_payment_success_to_preview_digital_documents(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $marketing = User::factory()->create([
            'role' => 'marketing',
        ]);

        $car = Car::create([
            'kode_unit' => 'PAY-LOCAL-001',
            'merk' => 'Honda',
            'tipe' => 'HRV',
            'tahun' => 2024,
            'harga' => 336000000,
            'kilometer' => 1000,
            'status' => 'reserved',
            'photos' => [],
            'created_by' => $marketing->id,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'confirmed',
            'total' => 336000000,
            'payment_method' => 'transfer',
            'transaction_channel' => 'online',
            'sales_flow' => 'direct_purchase',
            'document_status' => Order::pendingDocumentStatuses(),
        ]);

        $this->actingAs($customer)
            ->post(route('customer.payments.simulate-success'), [
                'order_id' => $order->id,
            ])
            ->assertRedirect(route('order.tracking', ['order' => $order->id]));

        $payment = Payment::where('order_id', $order->id)->first();

        $this->assertNotNull($payment);
        $this->assertSame('local_demo', $payment->gateway_provider);
        $this->assertSame('PAID', $payment->gateway_status);
        $this->assertSame('verified', $payment->status);
        $this->assertSame((float) $order->total, (float) $payment->amount);

        $order->refresh();
        $car->refresh();

        $this->assertSame('completed', $order->status);
        $this->assertSame('ready', $order->document_status['invoice'] ?? null);
        $this->assertSame('ready', $order->document_status['receipt'] ?? null);
        $this->assertSame('ready', $order->document_status['handover_note'] ?? null);
        $this->assertSame('sold', $car->status);
    }

    public function test_customer_can_choose_full_payment_for_online_cash_order(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $marketing = User::factory()->create([
            'role' => 'marketing',
        ]);

        $car = Car::create([
            'kode_unit' => 'PAY-FULL-001',
            'merk' => 'Honda',
            'tipe' => 'Brio',
            'tahun' => 2020,
            'harga' => 124000000,
            'kilometer' => 8000,
            'status' => 'reserved',
            'photos' => [],
            'created_by' => $marketing->id,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'pending',
            'total' => 124000000,
            'payment_method' => 'transfer',
            'transaction_channel' => 'online',
            'sales_flow' => 'direct_purchase',
            'document_status' => Order::pendingDocumentStatuses(),
        ]);

        $this->actingAs($customer)
            ->post(route('customer.payments.store'), [
                'order_id' => $order->id,
                'method' => 'transfer',
                'payment_plan' => 'full',
                'bank_account' => 'mandiri',
            ])
            ->assertRedirect(route('order.tracking', ['order' => $order->id]));

        $payment = Payment::where('order_id', $order->id)->first();

        $this->assertNotNull($payment);
        $this->assertSame('transfer', $payment->method);
        $this->assertSame(124000000.0, (float) $payment->amount);
        $this->assertSame('pending', $payment->status);
        $this->assertStringContainsString('Pilihan pembayaran customer: Bayar Lunas', (string) $order->fresh()->notes);
    }

    public function test_credit_checkout_route_redirects_customer_to_cash_checkout(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Rina Kredit',
        ]);

        $supervisor = User::factory()->create([
            'role' => 'supervisor',
        ]);

        $car = Car::create([
            'kode_unit' => 'KRD-001',
            'merk' => 'Daihatsu',
            'tipe' => 'Xenia R',
            'tahun' => 2013,
            'harga' => 134000000,
            'kilometer' => 12000,
            'status' => 'available',
            'photos' => [],
            'created_by' => $supervisor->id,
        ]);

        $this->actingAs($customer)
            ->get(route('checkout', ['car_id' => $car->id]))
            ->assertRedirect(route('checkout.cash', ['car_id' => $car->id]));

        $this->actingAs($customer)
            ->get(route('checkout.credit', ['car_id' => $car->id]))
            ->assertRedirect(route('checkout.cash', ['car_id' => $car->id]));
    }

    public function test_order_code_uses_unit_date_channel_and_global_sequence_format(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $supervisor = User::factory()->create([
            'role' => 'supervisor',
        ]);

        $car = Car::create([
            'kode_unit' => 'INV-TEST-001',
            'merk' => 'Toyota',
            'tipe' => 'Innova Reborn',
            'tahun' => 2021,
            'harga' => 350000000,
            'status' => 'reserved',
            'photos' => [],
            'created_by' => $supervisor->id,
        ]);

        $firstOrder = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'pending',
            'total' => 350000000,
            'transaction_channel' => 'offline',
            'sales_flow' => 'offline_showroom',
            'document_status' => Order::pendingDocumentStatuses(),
        ]);

        $firstOrder->forceFill([
            'created_at' => Carbon::create(2021, 1, 2, 9, 0, 0),
            'updated_at' => Carbon::create(2021, 1, 2, 9, 0, 0),
        ])->saveQuietly();
        $firstOrder->assignOrderCodeIfMissing(true);

        $secondOrder = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'pending',
            'total' => 350000000,
            'transaction_channel' => 'offline',
            'sales_flow' => 'offline_showroom',
            'document_status' => Order::pendingDocumentStatuses(),
        ]);

        $secondOrder->forceFill([
            'created_at' => Carbon::create(2021, 1, 2, 11, 15, 0),
            'updated_at' => Carbon::create(2021, 1, 2, 11, 15, 0),
        ])->saveQuietly();
        $secondOrder->assignOrderCodeIfMissing(true);

        $importedOrder = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'completed',
            'total' => 350000000,
            'transaction_channel' => 'online',
            'sales_flow' => 'direct_purchase',
            'import_source' => 'arsip-2025.xlsx',
            'document_status' => Order::pendingDocumentStatuses(),
        ]);

        $importedOrder->forceFill([
            'created_at' => Carbon::create(2025, 1, 2, 8, 30, 0),
            'updated_at' => Carbon::create(2025, 1, 2, 8, 30, 0),
        ])->saveQuietly();
        $importedOrder->assignOrderCodeIfMissing(true);

        $this->assertSame('INV020121OFN01', $firstOrder->fresh()->order_code);
        $this->assertSame('INV020121OFN02', $secondOrder->fresh()->order_code);
        $this->assertSame('INV020125OFN03', $importedOrder->fresh()->order_code);
    }

    public function test_only_after_test_drive_orders_link_test_drive_records_and_direct_purchase_cleans_orphan_test_drive(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $supervisor = User::factory()->create([
            'role' => 'supervisor',
        ]);

        $car = Car::create([
            'kode_unit' => 'HRV-LINK-001',
            'merk' => 'Honda',
            'tipe' => 'HRV E 1.5 CVT',
            'tahun' => 2024,
            'harga' => 336000000,
            'status' => 'reserved',
            'photos' => [],
            'created_by' => $supervisor->id,
        ]);

        $linkedTestDrive = TestDrive::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'booking_date' => '2026-05-24',
            'booking_time' => '09:00',
            'status' => 'approved',
            'customer_channel' => 'online',
            'follow_up_status' => 'appointment',
        ]);

        $afterTestDriveOrder = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'pending',
            'total' => 336000000,
            'payment_method' => 'transfer',
            'transaction_channel' => 'online',
            'sales_flow' => 'after_test_drive',
            'document_status' => Order::pendingDocumentStatuses(),
        ]);

        $afterTestDriveOrder->forceFill([
            'created_at' => Carbon::create(2026, 5, 24, 12, 0, 0),
            'updated_at' => Carbon::create(2026, 5, 24, 12, 0, 0),
        ])->saveQuietly();
        TestDriveOrderLinker::attach($afterTestDriveOrder);

        $this->assertSame($afterTestDriveOrder->id, $linkedTestDrive->fresh()->order_id);

        $separateTestDrive = TestDrive::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'booking_date' => '2026-06-01',
            'booking_time' => '10:00',
            'status' => 'approved',
            'customer_channel' => 'online',
            'follow_up_status' => 'appointment',
        ]);

        $directOrder = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'pending',
            'total' => 336000000,
            'payment_method' => 'transfer',
            'transaction_channel' => 'online',
            'sales_flow' => 'direct_purchase',
            'document_status' => Order::pendingDocumentStatuses(),
        ]);

        $directOrder->forceFill([
            'created_at' => Carbon::create(2026, 6, 2, 14, 0, 0),
            'updated_at' => Carbon::create(2026, 6, 2, 14, 0, 0),
        ])->saveQuietly();
        TestDriveOrderLinker::attach($directOrder);

        $this->assertDatabaseMissing('test_drives', [
            'id' => $separateTestDrive->id,
        ]);
    }

    public function test_supervisor_can_input_manual_test_drive_for_whatsapp_customer(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
        ]);

        $car = Car::create([
            'kode_unit' => 'TD-MANUAL-001',
            'merk' => 'Honda',
            'tipe' => 'HRV E 1.5 CVT',
            'tahun' => 2024,
            'harga' => 336000000,
            'status' => 'available',
            'photos' => [],
            'created_by' => $supervisor->id,
        ]);

        $this->actingAs($supervisor)
            ->post(route('supervisor.testdrives.store'), [
                'customer_name' => 'Customer WhatsApp',
                'customer_phone' => '081234567890',
                'customer_city' => 'Dumai',
                'car_id' => $car->id,
                'booking_date' => '2026-06-10',
                'booking_time' => '10:00',
                'customer_channel' => 'online',
                'lead_source' => 'WhatsApp',
                'location' => 'Rumah customer',
                'status' => 'approved',
                'notes' => 'Customer minta unit diantar untuk test drive.',
            ])
            ->assertRedirect(route('supervisor.testdrives.index'));

        $this->assertDatabaseHas('test_drives', [
            'car_id' => $car->id,
            'customer_channel' => 'online',
            'status' => 'approved',
            'handled_by' => $supervisor->id,
            'handled_role' => 'supervisor',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Customer WhatsApp',
            'phone' => '081234567890',
            'role' => 'customer',
        ]);
    }

    public function test_transaction_documents_render_professional_identity_and_owner_authorization_blocks(): void
    {
        config()->set('documents.showroom.name', 'Maharani Mobil');
        config()->set('documents.showroom.phone', '0811-7584-617');
        config()->set('documents.showroom.whatsapp', '628117584617');
        config()->set('documents.showroom.address', 'Jl. Arifin Ahmad No.113, Sidomulyo Timur, Marpoyan Damai, Kota Pekanbaru, Riau');
        config()->set('documents.owner.name', 'Rarendra');
        config()->set('documents.owner.title', 'Pemilik Maharani Mobil');

        $customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Rina Al-Qomar Puji Siswanti',
            'phone' => '081277778888',
        ]);

        $supervisor = User::factory()->create([
            'role' => 'supervisor',
        ]);

        $car = Car::create([
            'kode_unit' => 'MM-HRV-024-524',
            'merk' => 'Honda',
            'tipe' => 'HRV E 1.5 CVT',
            'tahun' => 2024,
            'harga' => 336000000,
            'kilometer' => 14550,
            'status' => 'reserved',
            'transmisi' => 'CVT',
            'warna' => 'Hitam',
            'photos' => [],
            'created_by' => $supervisor->id,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'status' => 'paid',
            'total' => 336000000,
            'payment_method' => 'transfer',
            'transaction_channel' => 'online',
            'sales_flow' => 'direct_purchase',
            'handled_by' => $supervisor->id,
            'handled_role' => 'supervisor',
            'approved_by' => $supervisor->id,
            'approved_at' => now(),
            'document_status' => [
                'invoice' => 'ready',
                'receipt' => 'ready',
                'handover_note' => 'ready',
                'stnk' => 'available',
                'bpkb' => 'available',
            ],
        ]);

        Payment::create([
            'order_id' => $order->id,
            'method' => 'transfer',
            'amount' => 336000000,
            'status' => 'verified',
            'gateway_provider' => 'local_demo',
            'gateway_reference' => 'LOCAL-REF-336',
            'gateway_status' => 'PAID',
            'verified_by' => $supervisor->id,
            'verified_at' => now(),
            'handled_by' => $supervisor->id,
            'handled_role' => 'supervisor',
            'handled_at' => now(),
        ]);

        $order->load(['user', 'car', 'payment.verifier', 'handledBy', 'approvedBy']);
        $profile = \App\Support\TransactionDocumentProfile::build($order);

        $invoiceHtml = View::make('documents.invoice', [
            'order' => $order,
            'type' => 'invoice',
            'documentTitle' => 'Faktur Pembelian Unit',
            'documentProfile' => $profile,
        ])->render();

        $receiptHtml = View::make('documents.receipt', [
            'order' => $order,
            'type' => 'receipt',
            'documentTitle' => 'Kwitansi Digital Pembayaran',
            'documentProfile' => $profile,
        ])->render();

        $handoverHtml = View::make('documents.handover-note', [
            'order' => $order,
            'type' => 'handover_note',
            'documentTitle' => 'Berita Acara Serah Terima Kendaraan',
            'documentProfile' => $profile,
        ])->render();

        $this->assertStringContainsString('Telp Showroom: 0811-7584-617', $invoiceHtml);
        $this->assertStringContainsString('Rarendra', $invoiceHtml);
        $this->assertStringContainsString('081277778888', $invoiceHtml);
        $this->assertStringContainsString('Faktur ini diterbitkan oleh Maharani Mobil sebagai bukti administratif atas transaksi pembelian kendaraan yang telah diproses.', $invoiceHtml);
        $this->assertStringContainsString('Ini merupakan faktur asli yang diterbitkan oleh pihak Maharani Mobil.', $invoiceHtml);

        $this->assertStringContainsString('Kwitansi Jual Beli Mobil', $receiptHtml);
        $this->assertStringContainsString('Pemilik Maharani Mobil', $receiptHtml);
        $this->assertStringContainsString('081277778888', $receiptHtml);
        $this->assertStringContainsString('Ini merupakan kwitansi asli yang diterbitkan oleh pihak Maharani Mobil.', $receiptHtml);

        $this->assertStringContainsString('Berita Acara Serah Terima Kendaraan', $handoverHtml);
        $this->assertStringContainsString('Status ketersediaan STNK', $handoverHtml);
        $this->assertStringContainsString('Tersedia', $handoverHtml);
        $this->assertStringContainsString('Ini merupakan surat Berita Acara Serah Terima asli yang diterbitkan oleh pihak Maharani Mobil.', $handoverHtml);
    }
}
