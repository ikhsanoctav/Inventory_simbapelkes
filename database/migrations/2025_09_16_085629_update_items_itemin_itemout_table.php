<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan foreign key ke tabel items
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'supplier_id')) {
                $table->foreign('supplier_id')
                      ->references('id')->on('suppliers')
                      ->onDelete('set null');
            }

            if (Schema::hasColumn('items', 'unit_id')) {
                $table->foreign('unit_id')
                      ->references('id')->on('units')
                      ->onDelete('set null');
            }
        });

        // Tambahkan foreign key ke tabel item_ins
        Schema::table('item_ins', function (Blueprint $table) {
            if (Schema::hasColumn('item_ins', 'supplier_id')) {
                $table->foreign('supplier_id')
                      ->references('id')->on('suppliers')
                      ->onDelete('set null');
            }
        });

        // Tambahkan foreign key ke tabel item_out
        Schema::table('item_outs', function (Blueprint $table) {
            if (Schema::hasColumn('item_outs', 'unit_id')) {
                $table->foreign('unit_id')
                      ->references('id')->on('units')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'supplier_id')) {
                $table->dropForeign(['supplier_id']);
            }
            if (Schema::hasColumn('items', 'unit_id')) {
                $table->dropForeign(['unit_id']);
            }
        });

        Schema::table('item_ins', function (Blueprint $table) {
            if (Schema::hasColumn('item_ins', 'supplier_id')) {
                $table->dropForeign(['supplier_id']);
            }
        });

        Schema::table('item_outs', function (Blueprint $table) {
            if (Schema::hasColumn('item_outs', 'unit_id')) {
                $table->dropForeign(['unit_id']);
            }
        });
    }
};
