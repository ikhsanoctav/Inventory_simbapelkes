<?php

namespace App\Imports;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ItemsImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Data_Barang' => new ItemsSheetImport(),
        ];
    }
}

/* ============================================================
 * CHILD IMPORTER (SHEET: Data_Barang)
 * ============================================================ */
class ItemsSheetImport implements ToModel, WithHeadingRow
{
    private static function generateUniqueCode($categoryId)
    {
        $categoryCode = str_pad($categoryId, 3, '0', STR_PAD_LEFT);

        $lastItem = Item::where('category_id', $categoryId)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastItem && preg_match('/-(\d+)-/', $lastItem->code, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $randomNumber = mt_rand(100, 999);

        return "{$categoryCode}-{$formattedNumber}-{$randomNumber}";
    }

    public function model(array $row)
    {
        $row = array_change_key_case($row, CASE_LOWER);

        if (!isset($row['name']) || empty($row['name'])) {
            return null;
        }

        $categoryId = $row['category_id'] ?? null;

        $code = !empty($row['code'])
            ? $row['code']
            : ($categoryId ? self::generateUniqueCode($categoryId) : null);

        return new Item([
            'name'        => $row['name'],
            'code'        => $code,
            'category_id' => $categoryId,
            'stock'       => $row['stock'] ?? 0,
            'price'       => $row['price'] ?? 0,
            'unit_id'     => $row['unit_id'] ?? null,
            'created_by'  => $row['created_by'] ?? Auth::id(),
            'image'       => $row['image'] ?? null,
        ]);
    }
}