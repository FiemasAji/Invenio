<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetMutation;
use App\Models\AssetSpecField;
use App\Models\AssetType;
use App\Models\Site;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with complete prompt3 multi-tenant IT asset data.
     */
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────────────
        // 1. Super Admin (Global System Controller)
        // ─────────────────────────────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => env('SUPERADMIN_EMAIL', 'superadmin@invenio.app')],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make(env('SUPERADMIN_PASSWORD', 'password')),
                'role'      => 'superadmin',
                'tenant_id' => null,
                'is_active' => true,
            ]
        );

        // ─────────────────────────────────────────────────────────────────────────
        // 2. Primary Tenant: PT Nusantara Tech
        // ─────────────────────────────────────────────────────────────────────────
        $tenant1 = Tenant::updateOrCreate(
            ['slug' => 'nusantara-tech'],
            [
                'name'      => 'PT Nusantara Tech',
                'email'     => 'contact@nusantaratech.id',
                'phone'     => '021-5551234',
                'address'   => 'Jl. Jenderal Sudirman Kav. 25, Lantai 18, Jakarta Selatan',
                'is_active' => true,
            ]
        );

        // Sites untuk Tenant 1 (b2)
        $siteJkt = Site::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'code' => 'SITE-JKT-HQ'],
            [
                'name'      => 'Kantor Pusat Jakarta',
                'phone'     => '021-5551234',
                'address'   => 'Gedung Menara Sudirman Lt. 18, Jakarta Selatan',
                'is_active' => true,
            ]
        );

        $siteSby = Site::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'code' => 'SITE-SBY-BR'],
            [
                'name'      => 'Kantor Cabang Surabaya',
                'phone'     => '031-8889999',
                'address'   => 'Jl. Pemuda No. 45, Genteng, Surabaya',
                'is_active' => true,
            ]
        );

        // Karyawan & Akun Tenant 1 (b1)
        $adminNusantara = User::updateOrCreate(
            ['email' => 'admin@nusantaratech.id'],
            [
                'name'        => 'Budi Santoso',
                'password'    => Hash::make('password'),
                'role'        => 'admin',
                'tenant_id'   => $tenant1->id,
                'employee_id' => 'EMP-001',
                'position'    => 'Head of IT & Infrastructure',
                'phone'       => '081234567801',
                'custom_code' => 'USR-MGR-01',
                'site_id'     => $siteJkt->id,
                'is_active'   => true,
            ]
        );

        $userSiti = User::updateOrCreate(
            ['email' => 'siti@nusantaratech.id'],
            [
                'name'        => 'Siti Rahmawati',
                'password'    => Hash::make('password'),
                'role'        => 'user',
                'tenant_id'   => $tenant1->id,
                'employee_id' => 'EMP-002',
                'position'    => 'Senior Software Engineer',
                'phone'       => '081234567802',
                'custom_code' => 'USR-DEV-01',
                'site_id'     => $siteJkt->id,
                'is_active'   => true,
            ]
        );

        $userRian = User::updateOrCreate(
            ['email' => 'rian@nusantaratech.id'],
            [
                'name'        => 'Rian Pratama',
                'password'    => Hash::make('password'),
                'role'        => 'user',
                'tenant_id'   => $tenant1->id,
                'employee_id' => 'EMP-003',
                'position'    => 'UI/UX Product Designer',
                'phone'       => '081234567803',
                'custom_code' => 'USR-DSG-01',
                'site_id'     => $siteJkt->id,
                'is_active'   => true,
            ]
        );

        $userEko = User::updateOrCreate(
            ['email' => 'eko@nusantaratech.id'],
            [
                'name'        => 'Eko Prasetyo',
                'password'    => Hash::make('password'),
                'role'        => 'user',
                'tenant_id'   => $tenant1->id,
                'employee_id' => 'EMP-004',
                'position'    => 'Network Operations Engineer',
                'phone'       => '081234567804',
                'custom_code' => 'USR-NET-01',
                'site_id'     => $siteSby->id,
                'is_active'   => true,
            ]
        );

        // Kategori & Jenis Barang Template (b3)
        $catComputer = AssetCategory::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'code' => 'CAT-CMP'],
            [
                'name'        => 'Komputer & Laptop',
                'description' => 'Perangkat komputasi utama staf dan workstation rekayasa perangkat lunak.',
            ]
        );

        $catNetwork = AssetCategory::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'code' => 'CAT-NET'],
            [
                'name'        => 'Jaringan & Server',
                'description' => 'Router, switch, access point, dan server rack perusahaan.',
            ]
        );

        // Jenis: Laptop (b3)
        $typeLaptop = AssetType::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'code' => 'TYP-LPT'],
            [
                'asset_category_id' => $catComputer->id,
                'name'              => 'Laptop / Notebook',
                'description'       => 'Laptop portabel standar kerja karyawan.',
            ]
        );

        // Parameter Spesifikasi Laptop (b3: Custom Parameters)
        AssetSpecField::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'asset_type_id' => $typeLaptop->id, 'name' => 'Processor'],
            ['field_type' => 'text', 'options' => null, 'is_required' => true, 'order' => 1]
        );

        AssetSpecField::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'asset_type_id' => $typeLaptop->id, 'name' => 'RAM'],
            ['field_type' => 'select', 'options' => ['8 GB', '16 GB', '32 GB', '64 GB'], 'is_required' => true, 'order' => 2]
        );

        AssetSpecField::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'asset_type_id' => $typeLaptop->id, 'name' => 'Storage'],
            ['field_type' => 'select', 'options' => ['256 GB SSD', '512 GB SSD', '1 TB SSD', '2 TB SSD'], 'is_required' => true, 'order' => 3]
        );

        AssetSpecField::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'asset_type_id' => $typeLaptop->id, 'name' => 'Sistem Operasi'],
            ['field_type' => 'select', 'options' => ['Windows 11 Pro', 'Ubuntu Linux 24.04', 'macOS Sonoma', 'macOS Sequoia'], 'is_required' => true, 'order' => 4]
        );

        // Jenis: Router / Switch (b3)
        $typeRouter = AssetType::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'code' => 'TYP-SWT'],
            [
                'asset_category_id' => $catNetwork->id,
                'name'              => 'Managed Switch & Router',
                'description'       => 'Perangkat jaringan switch gigabit dan router kantor cabang.',
            ]
        );

        AssetSpecField::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'asset_type_id' => $typeRouter->id, 'name' => 'Port Count'],
            ['field_type' => 'number', 'options' => null, 'is_required' => true, 'order' => 1]
        );

        AssetSpecField::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'asset_type_id' => $typeRouter->id, 'name' => 'Bandwidth Throughput'],
            ['field_type' => 'text', 'options' => null, 'is_required' => false, 'order' => 2]
        );

        // ─────────────────────────────────────────────────────────────────────────
        // Data Fisik Inventaris Aset (c1, c2)
        // ─────────────────────────────────────────────────────────────────────────

        // Aset 1: Laptop Siti
        $asset1 = Asset::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'asset_code' => 'AST-LPT-001'],
            [
                'asset_type_id' => $typeLaptop->id,
                'site_id'       => $siteJkt->id,
                'user_id'       => $userSiti->id,
                'name'          => 'Lenovo ThinkPad T14s Gen 4',
                'serial_number' => 'PF49B891X',
                'status'        => 'in_use',
                'purchase_date' => '2024-03-15',
                'notes'         => 'Termasuk adaptor charger 65W USB-C dan tas ransel Lenovo ThinkPad.',
                'qr_token'      => 'token_ast_lpt_001_siti_rahmawati',
                'specs'         => [
                    'Processor'      => 'AMD Ryzen 7 PRO 7840U',
                    'RAM'            => '32 GB',
                    'Storage'        => '1 TB SSD',
                    'Sistem Operasi' => 'Ubuntu Linux 24.04',
                ],
            ]
        );

        // Aset 2: Laptop Rian
        $asset2 = Asset::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'asset_code' => 'AST-LPT-002'],
            [
                'asset_type_id' => $typeLaptop->id,
                'site_id'       => $siteJkt->id,
                'user_id'       => $userRian->id,
                'name'          => 'Apple MacBook Pro 14" M3 Pro',
                'serial_number' => 'C02G9988MD6R',
                'status'        => 'in_use',
                'purchase_date' => '2024-05-10',
                'notes'         => 'Unit warna Space Black, adaptor 96W MagSafe 3.',
                'qr_token'      => 'token_ast_lpt_002_rian_pratama',
                'specs'         => [
                    'Processor'      => 'Apple M3 Pro (11-core CPU, 14-core GPU)',
                    'RAM'            => '36 GB',
                    'Storage'        => '1 TB SSD',
                    'Sistem Operasi' => 'macOS Sequoia',
                ],
            ]
        );

        // Aset 3: Laptop Stock Gudang
        $asset3 = Asset::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'asset_code' => 'AST-LPT-003'],
            [
                'asset_type_id' => $typeLaptop->id,
                'site_id'       => $siteJkt->id,
                'user_id'       => null,
                'name'          => 'Dell Latitude 5440',
                'serial_number' => '8FJK9L2',
                'status'        => 'in_stock',
                'purchase_date' => '2024-08-01',
                'notes'         => 'Siap dialokasikan untuk karyawan baru (onboarding).',
                'qr_token'      => 'token_ast_lpt_003_stock_gudang',
                'specs'         => [
                    'Processor'      => 'Intel Core i5-1335U',
                    'RAM'            => '16 GB',
                    'Storage'        => '512 GB SSD',
                    'Sistem Operasi' => 'Windows 11 Pro',
                ],
            ]
        );

        // Aset 4: Switch Jaringan di Cabang Surabaya
        $asset4 = Asset::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'asset_code' => 'AST-SWT-001'],
            [
                'asset_type_id' => $typeRouter->id,
                'site_id'       => $siteSby->id,
                'user_id'       => $userEko->id,
                'name'          => 'Cisco Catalyst 2960-X Gigabit Switch',
                'serial_number' => 'FCW1948B0KL',
                'status'        => 'in_use',
                'purchase_date' => '2023-11-20',
                'notes'         => 'Terpasang di Server Rack Lt. 2 Surabaya Office.',
                'qr_token'      => 'token_ast_swt_001_cisco_sby',
                'specs'         => [
                    'Port Count'           => 24,
                    'Bandwidth Throughput' => '1000 Mbps (Gigabit)',
                ],
            ]
        );

        // ─────────────────────────────────────────────────────────────────────────
        // Riwayat & Log Mutasi Barang (c3)
        // ─────────────────────────────────────────────────────────────────────────
        AssetMutation::updateOrCreate(
            [
                'tenant_id'     => $tenant1->id,
                'asset_id'      => $asset1->id,
                'notes'         => 'Pencatatan aset awal dan penyerahan unit ke Lead Software Engineer.',
            ],
            [
                'from_site_id'  => null,
                'to_site_id'    => $siteJkt->id,
                'from_user_id'  => null,
                'to_user_id'    => $userSiti->id,
                'mutation_date' => '2024-03-16',
                'created_by'    => $adminNusantara->id,
            ]
        );

        AssetMutation::updateOrCreate(
            [
                'tenant_id'     => $tenant1->id,
                'asset_id'      => $asset2->id,
                'notes'         => 'Penyerahan unit MacBook Pro untuk perancangan UI/UX Desain Aplikasi.',
            ],
            [
                'from_site_id'  => null,
                'to_site_id'    => $siteJkt->id,
                'from_user_id'  => null,
                'to_user_id'    => $userRian->id,
                'mutation_date' => '2024-05-11',
                'created_by'    => $adminNusantara->id,
            ]
        );

        AssetMutation::updateOrCreate(
            [
                'tenant_id'     => $tenant1->id,
                'asset_id'      => $asset4->id,
                'notes'         => 'Pengiriman perangkat switch dari Jakarta HQ ke Cabang Surabaya dan ditugaskan ke Network Engineer.',
            ],
            [
                'from_site_id'  => $siteJkt->id,
                'to_site_id'    => $siteSby->id,
                'from_user_id'  => null,
                'to_user_id'    => $userEko->id,
                'mutation_date' => '2023-11-25',
                'created_by'    => $adminNusantara->id,
            ]
        );

        // ─────────────────────────────────────────────────────────────────────────
        // 3. Sample Tenant 2: CV Karya Mandiri (Uji Isolasi Multi-Tenant)
        // ─────────────────────────────────────────────────────────────────────────
        $tenant2 = Tenant::updateOrCreate(
            ['slug' => 'karya-mandiri'],
            [
                'name'      => 'CV Karya Mandiri',
                'email'     => 'info@karyamandiri.co.id',
                'phone'     => '031-7775678',
                'address'   => 'Jl. Raya Darmo No. 88, Surabaya',
                'is_active' => true,
            ]
        );

        $adminKarya = User::updateOrCreate(
            ['email' => 'admin@karyamandiri.co.id'],
            [
                'name'        => 'Agus Setiawan',
                'password'    => Hash::make('password'),
                'role'        => 'admin',
                'tenant_id'   => $tenant2->id,
                'employee_id' => 'KM-ADM-01',
                'position'    => 'Operational Manager',
                'phone'       => '082199988801',
                'custom_code' => 'KM-ADM',
                'is_active'   => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@karyamandiri.co.id'],
            [
                'name'        => 'Dewi Lestari',
                'password'    => Hash::make('password'),
                'role'        => 'user',
                'tenant_id'   => $tenant2->id,
                'employee_id' => 'KM-STF-01',
                'position'    => 'General Admin',
                'phone'       => '082199988802',
                'custom_code' => 'KM-USR',
                'is_active'   => true,
            ]
        );
    }
}
