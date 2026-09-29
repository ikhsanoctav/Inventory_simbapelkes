<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Item_in;
use App\Models\Item_out;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuperAdminRepository
{
    public function getCategoriesCount()
    {
        return Category::count();
    }

    public function getItemsCount()
    {
        return Item::count();
    }

    public function getSuppliersCount()
    {
        return Supplier::count();
    }

    public function getUsersCount()
    {
        return User::count();
    }

    public function getLatestItemIns($limit = 5)
    {
        return Item_in::with('item')->latest()->take($limit)->get();
    }

    public function getLatestItemOuts($limit = 5)
    {
        return Item_out::with('item')->latest()->take($limit)->get();
    }

    public function getExpiredSoon($days = 30)
    {
        return Item_in::whereNotNull('expired_at')
            ->whereBetween('expired_at', [Carbon::now(), Carbon::now()->addDays($days)])
            ->with('item')
            ->get();
    }

    public function getDashboardData($period = 'weekly')
    {
        $now = Carbon::now();

        switch ($period) {
            case 'weekly':
                $start = $now->copy()->subDays(7)->startOfDay();
                break;
            case 'monthly':
                $start = $now->copy()->subMonth()->startOfDay();
                break;
            case 'yearly':
                $start = $now->copy()->subYear()->startOfDay();
                break;
            default:
                $start = $now->copy()->subDays(7)->startOfDay();
                break;
        }

        $end = $now->copy()->endOfDay();

        // item_in → pakai created_at
        $itemIns = Item_in::selectRaw('DATE(created_at) as date, SUM(quantity) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'date')
            ->toArray();

        // item_out → pakai released_at kalau ada, fallback created_at
        $itemOuts = Item_out::selectRaw('DATE(COALESCE(released_at, created_at)) as date, SUM(quantity) as total')
            ->whereBetween(DB::raw('COALESCE(released_at, created_at)'), [$start, $end])
            ->groupBy(DB::raw('DATE(COALESCE(released_at, created_at))'))
            ->pluck('total', 'date')
            ->toArray();

        // generate semua tanggal di periode
        $periodDates = [];
        $cursor = $start->copy();
        while ($cursor <= $end) {
            $periodDates[] = $cursor->format('Y-m-d');
            $cursor->addDay();
        }

        // isi data sesuai dengan range
        $itemInsData = [];
        $itemOutsData = [];

        foreach ($periodDates as $date) {
            $itemInsData[] = $itemIns[$date] ?? 0;
            $itemOutsData[] = $itemOuts[$date] ?? 0;
        }

        return [
            'labels'   => $periodDates,
            'itemIns'  => $itemInsData,
            'itemOuts' => $itemOutsData,
        ];
    }

    public function getTopRequesters($limit = 5)
    {
        $userRequests = DB::table('users as u')
            ->join('carts as c', 'c.user_id', '=', 'u.id')
            ->select('u.name as requester', 'u.email as contact', DB::raw('COUNT(c.id) as total_requests'), DB::raw("'User' as type"))
            ->groupBy('u.id', 'u.name', 'u.email');

        $guestRequests = DB::table('guests as g')
            ->join('guest_carts as gc', 'gc.session_id', '=', 'g.id')
            ->select('g.name as requester', 'g.phone as contact', DB::raw('COUNT(gc.id) as total_requests'), DB::raw("'Guest' as type"))
            ->groupBy('g.id', 'g.name', 'g.phone');

        return $userRequests->unionAll($guestRequests)
            ->orderBy('total_requests', 'desc')
            ->limit($limit)
            ->get();
    }

}
