<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetMutation;
use App\Models\AssetSpecField;
use App\Models\AssetType;
use App\Models\Site;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Prompt3FeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;
    private Tenant $tenantA;
    private Tenant $tenantB;
    private User $adminA;
    private User $userA;
    private User $adminB;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Super Admin
        $this->superadmin = User::create([
            'name'      => 'Global Super Admin',
            'email'     => 'superadmin@invenio.app',
            'password'  => Hash::make('password'),
            'role'      => 'superadmin',
            'tenant_id' => null,
            'is_active' => true,
        ]);

        // 2. Tenant A
        $this->tenantA = Tenant::create([
            'name'      => 'PT Alfa Digital',
            'slug'      => 'alfa-digital',
            'email'     => 'info@alfadigital.id',
            'is_active' => true,
        ]);

        $this->adminA = User::create([
            'name'        => 'Admin Alfa',
            'email'       => 'admin@alfa.id',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'tenant_id'   => $this->tenantA->id,
            'employee_id' => 'ALFA-ADM-01',
            'is_active'   => true,
        ]);

        $this->userA = User::create([
            'name'        => 'Staff Alfa',
            'email'       => 'staff@alfa.id',
            'password'    => Hash::make('password'),
            'role'        => 'user',
            'tenant_id'   => $this->tenantA->id,
            'employee_id' => 'ALFA-EMP-01',
            'position'    => 'Programmer',
            'is_active'   => true,
        ]);

        // 3. Tenant B (Untuk uji isolasi)
        $this->tenantB = Tenant::create([
            'name'      => 'CV Beta Sejahtera',
            'slug'      => 'beta-sejahtera',
            'email'     => 'info@betasejahtera.id',
            'is_active' => true,
        ]);

        $this->adminB = User::create([
            'name'        => 'Admin Beta',
            'email'       => 'admin@beta.id',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'tenant_id'   => $this->tenantB->id,
            'employee_id' => 'BETA-ADM-01',
            'is_active'   => true,
        ]);
    }

    /**
     * a1 & a2: Superadmin dapat melihat overview dan mengelola tenant + admin akun awal
     */
    public function test_superadmin_can_manage_tenants_and_view_overview(): void
    {
        // Overview (a2)
        $response = $this->actingAs($this->superadmin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('PT Alfa Digital');
        $response->assertSee('CV Beta Sejahtera');

        // Create Tenant + Admin (a1)
        $createResponse = $this->actingAs($this->superadmin)->post('/superadmin/tenants', [
            'tenant_name'    => 'PT Gamma Persada',
            'tenant_slug'    => 'gamma-persada',
            'tenant_email'   => 'contact@gammapersada.id',
            'tenant_phone'   => '021-99887766',
            'tenant_address' => 'Jl. Gatot Subroto No. 12, Jakarta',
            'admin_name'     => 'Admin Gamma',
            'admin_email'    => 'admin@gammapersada.id',
            'admin_password' => 'password123',
        ]);

        $createResponse->assertRedirect('/superadmin/tenants');
        $this->assertDatabaseHas('tenants', ['slug' => 'gamma-persada']);
        $this->assertDatabaseHas('users', ['email' => 'admin@gammapersada.id', 'role' => 'admin']);
    }

    /**
     * a1: Menghapus tenant sekaligus seluruh data user, sites, dan aset yang terkait (cascading)
     */
    public function test_superadmin_can_delete_tenant_with_cascading_data(): void
    {
        // Setup data di tenantA
        $site = Site::create([
            'tenant_id' => $this->tenantA->id,
            'code'      => 'SITE-DEL',
            'name'      => 'Site To Delete',
        ]);

        $cat = AssetCategory::create([
            'tenant_id' => $this->tenantA->id,
            'code'      => 'CAT-DEL',
            'name'      => 'Category To Delete',
        ]);

        $type = AssetType::create([
            'tenant_id'         => $this->tenantA->id,
            'asset_category_id' => $cat->id,
            'code'              => 'TYP-DEL',
            'name'              => 'Type To Delete',
        ]);

        $asset = Asset::create([
            'tenant_id'     => $this->tenantA->id,
            'asset_type_id' => $type->id,
            'asset_code'    => 'AST-DEL-01',
            'name'          => 'Asset To Delete',
            'status'        => 'in_stock',
        ]);

        // Hapus Tenant A oleh Super Admin
        $response = $this->actingAs($this->superadmin)->delete("/superadmin/tenants/{$this->tenantA->id}");
        $response->assertRedirect('/superadmin/tenants');

        $this->assertSoftDeleted('tenants', ['id' => $this->tenantA->id]);
        $this->assertDatabaseMissing('users', ['tenant_id' => $this->tenantA->id]);
        $this->assertSoftDeleted('sites', ['id' => $site->id]);
        $this->assertSoftDeleted('asset_categories', ['id' => $cat->id]);
        $this->assertSoftDeleted('asset_types', ['id' => $type->id]);
        $this->assertSoftDeleted('assets', ['id' => $asset->id]);
    }

    /**
     * b2: CRUD Site perusahaan beserta kode khusus
     */
    public function test_admin_can_crud_sites_with_custom_code(): void
    {
        // 1. Create Site
        $response = $this->actingAs($this->adminA)->post('/admin/sites', [
            'code'    => 'STE-JKT-01',
            'name'    => 'Headquarters Jakarta',
            'phone'   => '021-1234567',
            'address' => 'Gedung Menara Tower Lt. 10',
        ]);

        $response->assertRedirect('/admin/sites');
        $this->assertDatabaseHas('sites', [
            'tenant_id' => $this->tenantA->id,
            'code'      => 'STE-JKT-01',
            'name'      => 'Headquarters Jakarta',
        ]);

        $site = Site::where('code', 'STE-JKT-01')->first();

        // 2. Update Site
        $updateResponse = $this->actingAs($this->adminA)->put("/admin/sites/{$site->id}", [
            'code'    => 'STE-JKT-01',
            'name'    => 'HQ Jakarta Updated',
            'phone'   => '021-9999999',
            'address' => 'Alamat baru',
        ]);
        $updateResponse->assertRedirect('/admin/sites');
        $this->assertDatabaseHas('sites', ['name' => 'HQ Jakarta Updated']);

        // 3. Delete Site
        $deleteResponse = $this->actingAs($this->adminA)->delete("/admin/sites/{$site->id}");
        $deleteResponse->assertRedirect('/admin/sites');
        $this->assertSoftDeleted('sites', ['id' => $site->id]);
    }

    /**
     * b1: CRUD akun user: nama, id karyawan, no telp, posisi jabatan, site, kode khusus
     */
    public function test_admin_can_crud_users_with_employee_fields(): void
    {
        $site = Site::create([
            'tenant_id' => $this->tenantA->id,
            'code'      => 'STE-SBY',
            'name'      => 'Surabaya Office',
        ]);

        // 1. Create User
        $response = $this->actingAs($this->adminA)->post('/admin/users', [
            'name'        => 'Bambang Sudiro',
            'email'       => 'bambang@alfa.id',
            'password'    => 'rahasia123',
            'role'        => 'user',
            'employee_id' => 'EMP-777',
            'phone'       => '081299998888',
            'position'    => 'Network Specialist',
            'site_id'     => $site->id,
            'custom_code' => 'USR-NET-77',
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'tenant_id'   => $this->tenantA->id,
            'email'       => 'bambang@alfa.id',
            'employee_id' => 'EMP-777',
            'position'    => 'Network Specialist',
            'site_id'     => $site->id,
            'custom_code' => 'USR-NET-77',
        ]);

        $newUser = User::where('email', 'bambang@alfa.id')->first();

        // 2. Update User
        $updateResponse = $this->actingAs($this->adminA)->put("/admin/users/{$newUser->id}", [
            'name'        => 'Bambang Sudiro M.Kom',
            'email'       => 'bambang@alfa.id',
            'role'        => 'user',
            'employee_id' => 'EMP-777',
            'position'    => 'Lead Network Engineer',
            'site_id'     => $site->id,
        ]);
        $updateResponse->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', ['position' => 'Lead Network Engineer']);
    }

    /**
     * b3: CRUD inventaris template: kategori -> jenis barang -> parameter/spesifikasi kustom
     */
    public function test_admin_can_create_inventory_template_with_dynamic_specs(): void
    {
        // 1. Kategori
        $catResponse = $this->actingAs($this->adminA)->post('/admin/categories', [
            'code'        => 'CAT-CMP',
            'name'        => 'Computer Equipment',
            'description' => 'Semua perangkat komputer',
        ]);
        $catResponse->assertRedirect('/admin/categories');
        $category = AssetCategory::where('code', 'CAT-CMP')->first();
        $this->assertNotNull($category);

        // 2. Jenis Barang + Dynamic Spec Fields
        $typeResponse = $this->actingAs($this->adminA)->post('/admin/types', [
            'asset_category_id' => $category->id,
            'code'              => 'TYP-LPT',
            'name'              => 'Laptop Kantor',
            'specs'             => [
                ['name' => 'Processor', 'type' => 'text', 'required' => '1'],
                ['name' => 'RAM', 'type' => 'select', 'options' => '8 GB, 16 GB, 32 GB', 'required' => '1'],
                ['name' => 'Port USB-C', 'type' => 'number', 'required' => '0'],
            ],
        ]);
        $typeResponse->assertRedirect('/admin/types');

        $type = AssetType::where('code', 'TYP-LPT')->first();
        $this->assertNotNull($type);
        $this->assertDatabaseHas('asset_spec_fields', [
            'asset_type_id' => $type->id,
            'name'          => 'RAM',
            'field_type'    => 'select',
        ]);

        // 3. Cek JSON specs endpoint
        $specEndpointResponse = $this->actingAs($this->adminA)->get("/admin/types/{$type->id}/specs");
        $specEndpointResponse->assertStatus(200);
        $specEndpointResponse->assertJsonFragment(['name' => 'Processor']);
    }

    /**
     * c1, c2, c3, c4: Pendaftaran Aset, QR Code, Mutasi, dan Multi-Filter
     */
    public function test_full_asset_lifecycle_mutation_and_qr_scanning(): void
    {
        // Setup master
        $site = Site::create([
            'tenant_id' => $this->tenantA->id,
            'code'      => 'STE-HQ',
            'name'      => 'Jakarta HQ',
        ]);

        $cat = AssetCategory::create([
            'tenant_id' => $this->tenantA->id,
            'code'      => 'CAT-IT',
            'name'      => 'IT Devices',
        ]);

        $type = AssetType::create([
            'tenant_id'         => $this->tenantA->id,
            'asset_category_id' => $cat->id,
            'code'              => 'TYP-NB',
            'name'              => 'Notebook',
        ]);

        // c1: Registrasi Aset Baru dengan Dynamic Specs JSON
        $assetResponse = $this->actingAs($this->adminA)->post('/admin/assets', [
            'asset_type_id' => $type->id,
            'asset_code'    => 'AST-NB-101',
            'name'          => 'Dell XPS 15 9530',
            'serial_number' => 'DEL-XPS-9988',
            'status'        => 'in_use',
            'site_id'       => $site->id,
            'user_id'       => $this->userA->id,
            'purchase_date' => '2024-01-10',
            'specs'         => [
                'Processor' => 'Intel Core i9-13900H',
                'RAM'       => '32 GB',
                'Storage'   => '1 TB NVMe',
            ],
            'notes'         => 'Unit baru untuk Lead Developer',
        ]);

        $asset = Asset::where('asset_code', 'AST-NB-101')->first();
        $this->assertNotNull($asset);
        $this->assertNotNull($asset->qr_token);
        $this->assertEquals('Dell XPS 15 9530', $asset->name);
        $this->assertEquals('32 GB', $asset->specs['RAM']);

        // c2: QR Code Printable Label View
        $labelResponse = $this->actingAs($this->adminA)->get("/admin/assets/{$asset->id}/print-label");
        $labelResponse->assertStatus(200);
        $labelResponse->assertSee('AST-NB-101');
        $labelResponse->assertSee('<svg', false); // Rendered SVG QR code

        // c2: Public QR Scanning via Kamera Smartphone (tanpa login)
        $scanResponse = $this->get("/scan/{$asset->qr_token}");
        $scanResponse->assertStatus(200);
        $scanResponse->assertSee('AST-NB-101');
        $scanResponse->assertSee('Dell XPS 15 9530');
        $scanResponse->assertSee('PT Alfa Digital');
        $scanResponse->assertSee('Staff Alfa');

        // c3: Mutasi Barang ke Lokasi Lain / Pemegang Baru
        $newSite = Site::create([
            'tenant_id' => $this->tenantA->id,
            'code'      => 'STE-BDG',
            'name'      => 'Bandung Hub',
        ]);

        $mutationResponse = $this->actingAs($this->adminA)->post('/admin/mutations', [
            'asset_id'      => $asset->id,
            'to_site_id'    => $newSite->id,
            'to_user_id'    => null, // Kembali ke gudang
            'mutation_date' => '2024-06-01',
            'status'        => 'in_stock',
            'notes'         => 'Dikembalikan ke gudang site Bandung untuk re-imaging.',
        ]);

        $mutationResponse->assertRedirect("/admin/assets/{$asset->id}");

        // Verifikasi aset telah terupdate
        $asset->refresh();
        $this->assertEquals($newSite->id, $asset->site_id);
        $this->assertNull($asset->user_id);
        $this->assertEquals('in_stock', $asset->status);

        // Verifikasi riwayat log mutasi (c3)
        $this->assertDatabaseHas('asset_mutations', [
            'asset_id'   => $asset->id,
            'to_site_id' => $newSite->id,
            'notes'      => 'Dikembalikan ke gudang site Bandung untuk re-imaging.',
        ]);

        // c4: Multi-Filter Overview
        $filterResponse = $this->actingAs($this->adminA)->get('/admin/assets?status=in_stock&search=Dell');
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('AST-NB-101');
    }

    /**
     * Dashboard User: Hanya melihat barang inventaris miliknya
     */
    public function test_tenant_user_dashboard_only_shows_their_own_assigned_assets(): void
    {
        $cat = AssetCategory::create(['tenant_id' => $this->tenantA->id, 'code' => 'C1', 'name' => 'Cat']);
        $type = AssetType::create(['tenant_id' => $this->tenantA->id, 'asset_category_id' => $cat->id, 'code' => 'T1', 'name' => 'Typ']);

        // Aset milik User A
        $assetUserA = Asset::create([
            'tenant_id'     => $this->tenantA->id,
            'asset_type_id' => $type->id,
            'asset_code'    => 'AST-MY-01',
            'name'          => 'Laptop Personal Staff Alfa',
            'status'        => 'in_use',
            'user_id'       => $this->userA->id,
        ]);

        // Aset orang lain
        $assetOther = Asset::create([
            'tenant_id'     => $this->tenantA->id,
            'asset_type_id' => $type->id,
            'asset_code'    => 'AST-OTHER-01',
            'name'          => 'Laptop Orang Lain',
            'status'        => 'in_use',
            'user_id'       => null,
        ]);

        $response = $this->actingAs($this->userA)->get('/my-assets');
        $response->assertStatus(200);
        $response->assertSee('AST-MY-01');
        $response->assertDontSee('AST-OTHER-01');
    }

    /**
     * Data Isolation Multi-Tenant: Tenant B tidak dapat melihat atau mengubah aset Tenant A
     */
    public function test_multi_tenant_data_isolation(): void
    {
        $cat = AssetCategory::create(['tenant_id' => $this->tenantA->id, 'code' => 'C-A', 'name' => 'Cat Alfa']);
        $type = AssetType::create(['tenant_id' => $this->tenantA->id, 'asset_category_id' => $cat->id, 'code' => 'T-A', 'name' => 'Typ Alfa']);

        $assetA = Asset::create([
            'tenant_id'     => $this->tenantA->id,
            'asset_type_id' => $type->id,
            'asset_code'    => 'AST-ALFA-99',
            'name'          => 'Rahasia Perusahaan Alfa',
            'status'        => 'in_use',
        ]);

        // Admin Tenant B mencoba melihat daftar aset Tenant A
        $responseB = $this->actingAs($this->adminB)->get('/admin/assets');
        $responseB->assertStatus(200);
        $responseB->assertDontSee('AST-ALFA-99');
        $responseB->assertDontSee('Rahasia Perusahaan Alfa');

        // Admin Tenant B mencoba akses langsung detail aset Tenant A (404 via global tenant scope)
        $detailResponseB = $this->actingAs($this->adminB)->get("/admin/assets/{$assetA->id}");
        $detailResponseB->assertStatus(404);
    }
}
