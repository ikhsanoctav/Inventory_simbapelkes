<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('export_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('super_admin_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['weekly', 'monthly', 'yearly']);
            $table->enum('format', ['pdf', 'excel']);
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_logs');
    }
};