<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new TemplateSheet(),
            new SopSheet(),
            new CategorySheet(),
            new UnitSheet(),
        ];
    }
}

/* ============================================================
 * SHEET 1 — DATA BARANG (SINKRON IMPORT)
 * ============================================================ */
class TemplateSheet implements FromArray, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'Data_Barang';
    }

    public function headings(): array
    {
        return [
            'name',
            'code',
            'category_id',
            'stock',
            'price',
            'unit_id',
            'created_by',
            'image',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Contoh Barang', 
                '',             // code → bisa auto generate 
                1,              // category_id
                10,             // stock
                10000,          // price
                1,              // unit_id
                1,              // created_by (ID admin)
                '',             // image filename
            ],
        ];
    }
}

/* ============================================================
 * SHEET 2 — SOP PENGISIAN DATA
 * ============================================================ */
class SopSheet implements FromArray, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'SOP_Pengisian_Data';
    }

    public function headings(): array
    {
        return ["Bagian / Langkah", "Penjelasan Detail"];
    }

    public function array(): array
    {
        return [
            ["=== GUIDELINE ===", ""],
            ["Deskripsi", "Pastikan mengikuti referensi kategori dan satuan."],
            ["Format Harga", "Hanya angka tanpa simbol."],
            ["Format Gambar", "Nama file dalam folder images/items/."],
            ["Auto Code", "Kosongkan kolom code jika ingin dibuat otomatis."],
            ["Review Data", "Pastikan tidak kosong pada kolom wajib."],
        ];
    }
}

/* ============================================================
 * SHEET 3 — REFERENSI KATEGORI
 * ============================================================ */
class CategorySheet implements FromCollection, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'Referensi_Kategori';
    }

    public function headings(): array
    {
        return ["ID", "Nama Kategori"];
    }

    public function collection()
    {
        return DB::table('categories')
            ->select('id', 'name')
            ->orderBy('id')
            ->get();
    }
}

/* ============================================================
 * SHEET 4 — REFERENSI SATUAN
 * ============================================================ */
class UnitSheet implements FromCollection, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'Referensi_Satuan';
    }

    public function headings(): array
    {
        return ["ID", "Nama Satuan"];
    }

    public function collection()
    {
        return DB::table('units')
            ->select('id', 'name')
            ->orderBy('id')
            ->get();
    }
}