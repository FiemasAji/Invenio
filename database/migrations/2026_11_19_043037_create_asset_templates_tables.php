<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kategori Barang (e.g. Computer, Network, Office)
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
        });

        // 2. Jenis Barang di bawah Kategori (e.g. Laptop, Desktop PC, Router)
        Schema::create('asset_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('asset_category_id')->constrained('asset_categories')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
        });

        // 3. Parameter Spesifikasi Dinamis per Jenis Barang
        Schema::create('asset_spec_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('asset_type_id')->constrained('asset_types')->cascadeOnDelete();
            $table->string('name');
            $table->string('field_type')->default('text'); // 'text', 'number', 'select'
            $table->json('options')->nullable(); // Pilihan dropdown jika field_type == 'select'
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_spec_fields');
        Schema::dropIfExists('asset_types');
        Schema::dropIfExists('asset_categories');
    }
};
