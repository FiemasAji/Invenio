<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Item Inventaris Fisik
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('asset_type_id')->constrained('asset_types')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('asset_code');
            $table->string('name');
            $table->string('serial_number')->nullable();
            $table->string('status')->default('in_stock'); // 'in_use', 'in_stock', 'maintenance', 'disposed'
            $table->json('specs')->nullable(); // Nilai dinamis spesifikasi: {"RAM": "16GB", "Storage": "512GB SSD"}
            $table->date('purchase_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('qr_token')->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'asset_code']);
        });

        // 2. Tabel Log Mutasi & Perpindahan Barang
        Schema::create('asset_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('from_site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('to_site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('from_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('mutation_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_mutations');
        Schema::dropIfExists('assets');
    }
};
