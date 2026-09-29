<?php

namespace App\Repositories\Eloquent;

use App\Models\Cart;
use App\Models\Guest;
use App\Models\Item_out;
use App\Models\User;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * AdminRepository
 *
 * Implementasi repository untuk kebutuhan data dashboard admin.
 * Semua query dioptimalkan untuk menghindari lazy load dan N+1 problem.
 */
class AdminRepository implements AdminRepositoryInterface
{
    /**
     * Menghitung total kuantitas seluruh barang keluar yang pernah tercatat.
     *
     * @return int
     */
    public function getTotalBarangKeluar(): int
    {
        return (int) Item_out::sum('quantity');
    }

    /**
     * Menghitung total jumlah cart (permintaan) yang pernah dibuat.
     *
     * @return int
     */
    public function getTotalRequest(): int
    {
        return Cart::count();
    }

    /**
     * Menghitung total jumlah tamu yang terdaftar.
     *
     * @return int
     */
    public function getTotalGuest(): int
    {
        return Guest::count();
    }

    /**
     * Mengambil data barang keluar terbaru beserta relasi item-nya.
     *
     * Kolom pada relasi 'item' dibatasi hanya yang dibutuhkan (id, name, code)
     * agar tidak memuat seluruh kolom item ke memori.
     *
     * @param  int $limit Jumlah data yang diambil (default: 5)
     * @return Collection
     */
    public function getLatestBarangKeluar(int $limit = 5): Collection
    {
        return Item_out::with('item:id,name,code')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Mengambil data permintaan (cart) terbaru beserta relasi user dan item-nya.
     *
     * Kolom pada relasi 'user' dibatasi hanya yang dibutuhkan untuk tampilan.
     *
     * @param  int $limit Jumlah data yang diambil (default: 5)
     * @return Collection
     */
    public function getLatestRequest(int $limit = 5): Collection
    {
        return Cart::with([
                'user:id,name,email,role',
                'items:id,name,code',
            ])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Mengambil daftar peminjam (pegawai & tamu) dengan total permintaan terbanyak.
     *
     * PERBAIKAN N+1:
     * Sebelumnya, User::find() dipanggil di dalam loop map() untuk setiap baris
     * pegawai — menghasilkan satu query per baris (N+1 problem).
     *
     * Sekarang semua user_id dikumpulkan terlebih dahulu, lalu satu query
     * User::whereIn() dijalankan sekaligus. Hasilnya di-key by ID agar
     * pencarian O(1) tanpa loop tambahan.
     *
     * @param  int $limit Jumlah peminjam teratas yang ditampilkan (default: 5)
     * @return Collection
     */
    public function getTopRequesters(int $limit = 5): Collection
    {
        // --- Query total permintaan pegawai (dari tabel carts + cart_items) ---
        $userRequests = DB::table('carts')
            ->join('cart_items', 'carts.id', '=', 'cart_items.cart_id')
            ->select(
                'carts.user_id as requester_id',
                DB::raw('SUM(cart_items.quantity) as total_quantity')
            )
            ->groupBy('carts.user_id')
            ->get()
            ->map(fn($item) => (object) array_merge((array) $item, ['type' => 'user']));

        // --- Query total permintaan tamu (dari guest_cart_items) ---
        $guestRequests = DB::table('guest_cart_items')
            ->join('guest_carts', 'guest_cart_items.guest_cart_id', '=', 'guest_carts.id')
            ->join('sessions', 'guest_carts.session_id', '=', 'sessions.id')
            ->leftJoin('guests', 'guests.created_by', '=', 'sessions.user_id')
            ->select(
                DB::raw('COALESCE(guests.id, sessions.id) as requester_id'),
                DB::raw('COALESCE(guests.name, "Guest") as name'),
                DB::raw('SUM(guest_cart_items.quantity) as total_quantity')
            )
            ->groupBy('guests.id', 'guests.name', 'sessions.id', 'sessions.ip_address')
            ->get()
            ->map(fn($item) => (object) array_merge((array) $item, ['type' => 'guest']));

        // --- Gabungkan, urutkan, ambil sejumlah limit ---
        $combined = $userRequests
            ->concat($guestRequests)
            ->sortByDesc('total_quantity')
            ->take($limit)
            ->values();

        // --- Kumpulkan semua user_id pegawai dalam satu batch ---
        // Menghindari N+1: satu query User::whereIn() menggantikan User::find() di dalam loop
        $userIds = $combined
            ->where('type', 'user')
            ->pluck('requester_id')
            ->filter()
            ->unique()
            ->values();

        // Ambil semua user sekaligus, di-key by ID untuk lookup O(1)
        $usersById = User::select('id', 'name', 'email', 'role')
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id');

        // --- Format output akhir ---
        return $combined->map(function ($r) use ($usersById) {
            if ($r->type === 'user') {
                // Ambil dari koleksi yang sudah ter-eager load — tanpa query tambahan
                $user = $usersById->get($r->requester_id);

                return [
                    'name'           => $user?->name ?? 'Unknown User',
                    'email'          => $user?->email ?? '-',
                    'role'           => ucfirst($user?->role ?? 'Pegawai'),
                    'total_requests' => (int) $r->total_quantity,
                ];
            }

            return [
                'name'           => $r->name ?? 'Guest',
                'email'          => 'N/A',
                'role'           => 'Guest',
                'total_requests' => (int) $r->total_quantity,
            ];
        })->values();
    }

    /**
     * Mengambil data chart barang keluar berdasarkan rentang waktu.
     *
     * Mendukung tiga rentang:
     *  - 'week'  : 7 hari terakhir, label format 'd M'
     *  - 'month' : 30 hari terakhir, label format 'd M'
     *  - 'year'  : 12 bulan terakhir, label format 'M Y'
     *
     * Data di-pluck dari database lalu di-map ke periode yang lengkap
     * sehingga periode tanpa transaksi tetap muncul dengan nilai 0.
     *
     * @param  string $range Rentang waktu: 'week' | 'month' | 'year'
     * @return array{labels: Collection, keluar: Collection}
     */
    public function getChartDataByRange(string $range): array
    {
        $now = Carbon::now();

        // Tentukan titik awal dan daftar periode berdasarkan rentang
        [$start, $period] = match ($range) {
            'week'  => [
                $now->copy()->subDays(6)->startOfDay(),
                collect(range(0, 6))->map(fn($i) => $now->copy()->subDays(6 - $i)->format('Y-m-d')),
            ],
            'month' => [
                $now->copy()->subDays(29)->startOfDay(),
                collect(range(0, 29))->map(fn($i) => $now->copy()->subDays(29 - $i)->format('Y-m-d')),
            ],
            default => [
                $now->copy()->subMonths(11)->startOfMonth(),
                collect(range(0, 11))->map(fn($i) => $now->copy()->subMonths(11 - $i)->format('Y-m')),
            ],
        };

        // Satu query agregasi — hasilnya di-pluck menjadi ['periode' => total]
        $barangKeluar = Item_out::select(
                DB::raw($range === 'year'
                    ? "DATE_FORMAT(released_at, '%Y-%m') as periode"
                    : "DATE(released_at) as periode"
                ),
                DB::raw('SUM(quantity) as total')
            )
            ->where('released_at', '>=', $start)
            ->groupBy('periode')
            ->pluck('total', 'periode');

        return [
            // Label sumbu X sesuai format lokal
            'labels' => $period->map(fn($p) =>
                $range === 'year'
                    ? Carbon::parse($p . '-01')->translatedFormat('M Y')
                    : Carbon::parse($p)->translatedFormat('d M')
            ),
            // Nilai sumbu Y — periode tanpa data diisi 0
            'keluar' => $period->map(fn($p) => (int) ($barangKeluar[$p] ?? 0)),
        ];
    }
}