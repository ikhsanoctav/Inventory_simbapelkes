<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface AdminRepositoryInterface
{
    /**
     * Menghitung total kuantitas seluruh barang keluar.
     *
     * @return int
     */
    public function getTotalBarangKeluar(): int;

    /**
     * Menghitung total jumlah cart (permintaan).
     *
     * @return int
     */
    public function getTotalRequest(): int;

    /**
     * Menghitung total jumlah tamu terdaftar.
     *
     * @return int
     */
    public function getTotalGuest(): int;

    /**
     * Mengambil data barang keluar terbaru.
     *
     * @param  int $limit
     * @return Collection
     */
    public function getLatestBarangKeluar(int $limit = 5): Collection;

    /**
     * Mengambil data permintaan (cart) terbaru.
     *
     * @param  int $limit
     * @return Collection
     */
    public function getLatestRequest(int $limit = 5): Collection;

    /**
     * Mengambil peminjam dengan total permintaan terbanyak.
     *
     * @param  int $limit
     * @return Collection
     */
    public function getTopRequesters(int $limit = 5): Collection;

    /**
     * Mengambil data chart berdasarkan rentang waktu ('week', 'month', 'year').
     *
     * @param  string $range
     * @return array
     */
    public function getChartDataByRange(string $range): array;
}