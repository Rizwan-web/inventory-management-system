<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sales;
use App\Models\Setting;
use App\Models\InvoiceSequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SalesControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create([
            'level' => 1
        ]);

        // Create setting
        Setting::create([
            'name_perusahaan' => 'Test Store',
            'address' => 'Test Address',
            'telepon' => '123456789',
            'tipe_nota' => 1,
            'path_logo' => 'logo.png',
            'path_kartu_member' => 'card.png'
        ]);

        // Create invoice sequence
        InvoiceSequence::create([
            'year' => date('Y'),
            'last_number' => 0
        ]);
    }

    public function test_create_new_transaction()
    {
        $this->actingAs($this->user);

        $response = $this->get('/transaksi/baru');

        $response->assertRedirect('/transaksi');
        $this->assertDatabaseHas('sales', [
            'id_user' => $this->user->id,
            'total_item' => 0,
            'total_price' => 0
        ]);
    }

    public function test_store_transaction_with_dc_number()
    {
        $this->actingAs($this->user);

        // Create a sales record first
        $sales = Sales::create([
            'id_member' => null,
            'total_item' => 0,
            'total_price' => 0,
            'discount' => 0,
            'pay' => 0,
            'diterima' => 0,
            'id_user' => $this->user->id
        ]);

        $requestData = [
            'id_sales' => $sales->id_sales,
            'id_member' => null,
            'total_item' => 0,
            'total' => 0,
            'discount' => 0,
            'pay' => 0,
            'diterima' => 0,
            'dc_number' => 'DC-2025-001'
        ];

        $response = $this->post('/transaksi/simpan', $requestData);

        $response->assertRedirect('/transaksi/selesai');
        
        $this->assertDatabaseHas('sales', [
            'id_sales' => $sales->id_sales,
            'dc_number' => 'DC-2025-001'
        ]);
    }

    public function test_store_transaction_without_dc_number()
    {
        $this->actingAs($this->user);

        // Create a sales record first
        $sales = Sales::create([
            'id_member' => null,
            'total_item' => 0,
            'total_price' => 0,
            'discount' => 0,
            'pay' => 0,
            'diterima' => 0,
            'id_user' => $this->user->id
        ]);

        $requestData = [
            'id_sales' => $sales->id_sales,
            'id_member' => null,
            'total_item' => 0,
            'total' => 0,
            'discount' => 0,
            'pay' => 0,
            'diterima' => 0,
            'dc_number' => null
        ];

        $response = $this->post('/transaksi/simpan', $requestData);

        $response->assertRedirect('/transaksi/selesai');
        
        $this->assertDatabaseHas('sales', [
            'id_sales' => $sales->id_sales,
            'dc_number' => null
        ]);
    }

    public function test_generate_invoice_without_dc_numbers()
    {
        $this->actingAs($this->user);

        $requestData = [
            'dc_numbers' => []
        ];

        $response = $this->post('/transaksi/generate-invoice', $requestData);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Please select at least one DC number.');
    }

    public function test_generate_invoice_with_invalid_dc_numbers()
    {
        $this->actingAs($this->user);

        $requestData = [
            'dc_numbers' => ['INVALID-DC-001']
        ];

        $response = $this->post('/transaksi/generate-invoice', $requestData);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'No orders found with the specified DC numbers.');
    }

    public function test_cancel_transaction()
    {
        $this->actingAs($this->user);

        // Create a sales record
        $sales = Sales::create([
            'id_member' => null,
            'total_item' => 0,
            'total_price' => 0,
            'discount' => 0,
            'pay' => 0,
            'diterima' => 0,
            'id_user' => $this->user->id,
            'dc_number' => 'DC-2025-001'
        ]);

        // Set session
        session(['id_sales' => $sales->id_sales]);

        $response = $this->get('/transaksi/batal');

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseMissing('sales', ['id_sales' => $sales->id_sales]);
    }

    public function test_get_dc_numbers()
    {
        $this->actingAs($this->user);

        // Create a member first
        $member = \App\Models\Member::create([
            'member_code' => 'M001',
            'name' => 'Test Member',
            'telepon' => '123456789',
            'address' => 'Test Address'
        ]);

        // Create sales records with DC numbers
        $sales1 = Sales::create([
            'id_member' => $member->id_member,
            'total_item' => 1,
            'total_price' => 1000,
            'discount' => 0,
            'pay' => 1000,
            'diterima' => 1000,
            'id_user' => $this->user->id,
            'dc_number' => 'DC-2025-001'
        ]);

        // Debug: Check what's in the database
        $allSales = Sales::all();
        $this->assertCount(1, $allSales);
        
        $firstSale = $allSales->first();
        $this->assertEquals('DC-2025-001', $firstSale->dc_number);

        $response = $this->get('/transaksi/dc-numbers');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['DC-2025-001']);
    }

    public function test_generate_invoice_with_dc_numbers()
    {
        $this->actingAs($this->user);

        // Create a member first
        $member = \App\Models\Member::create([
            'member_code' => 'M001',
            'name' => 'Test Member',
            'telepon' => '123456789',
            'address' => 'Test Address'
        ]);

        // Create sales records with DC numbers and member
        $sales1 = Sales::create([
            'id_member' => $member->id_member,
            'total_item' => 1,
            'total_price' => 1000,
            'discount' => 0,
            'pay' => 1000,
            'diterima' => 1000,
            'id_user' => $this->user->id,
            'dc_number' => 'DC-2025-001'
        ]);

        $sales2 = Sales::create([
            'id_member' => $member->id_member,
            'total_item' => 1,
            'total_price' => 1000,
            'discount' => 0,
            'pay' => 1000,
            'diterima' => 1000,
            'id_user' => $this->user->id,
            'dc_number' => 'DC-2025-002'
        ]);

        // Create sales detail records
        \App\Models\SalesDetail::create([
            'id_sales' => $sales1->id_sales,
            'id_product' => 1,
            'selling_price' => 1000,
            'jumlah' => 1,
            'discount' => 0,
            'subtotal' => 1000,
            'invoice_number' => 0
        ]);

        \App\Models\SalesDetail::create([
            'id_sales' => $sales2->id_sales,
            'id_product' => 1,
            'selling_price' => 1000,
            'jumlah' => 1,
            'discount' => 0,
            'subtotal' => 1000,
            'invoice_number' => 0
        ]);

        $requestData = [
            'dc_numbers' => ['DC-2025-001', 'DC-2025-002']
        ];

        $response = $this->post('/transaksi/generate-invoice', $requestData);

        $response->assertRedirect();
        $this->assertDatabaseHas('sales_detail', [
            'id_sales' => $sales1->id_sales,
            'invoice_number' => 1
        ]);
        $this->assertDatabaseHas('sales_detail', [
            'id_sales' => $sales2->id_sales,
            'invoice_number' => 1
        ]);
    }

    public function test_generate_invoice_does_not_overwrite_existing_invoice_numbers()
    {
        $this->actingAs($this->user);

        // Create a member first
        $member = \App\Models\Member::create([
            'member_code' => 'M001',
            'name' => 'Test Member',
            'telepon' => '123456789',
            'address' => 'Test Address'
        ]);

        // Create sales record with existing invoice number
        $sales = Sales::create([
            'id_member' => $member->id_member,
            'total_item' => 1,
            'total_price' => 1000,
            'discount' => 0,
            'pay' => 1000,
            'diterima' => 1000,
            'id_user' => $this->user->id,
            'dc_number' => 'DC-2025-001'
        ]);

        // Create sales detail with existing invoice number
        \App\Models\SalesDetail::create([
            'id_sales' => $sales->id_sales,
            'id_product' => 1,
            'selling_price' => 1000,
            'jumlah' => 1,
            'discount' => 0,
            'subtotal' => 1000,
            'invoice_number' => 5 // Existing invoice number
        ]);

        $requestData = [
            'dc_numbers' => ['DC-2025-001']
        ];

        $response = $this->post('/transaksi/generate-invoice', $requestData);

        $response->assertRedirect();
        // The invoice should still be generated since we're not checking for existing invoice numbers anymore
        
        // Verify the existing invoice number is not changed
        $this->assertDatabaseHas('sales_detail', [
            'id_sales' => $sales->id_sales,
            'invoice_number' => 5
        ]);
    }
}
    