@auth
    @php
        // Global variables for all authenticated users
        $notifications = \App\Models\Notification::where('user_id', Auth::id())
            ->where('status', 'unread')
            ->latest()
            ->take(5)
            ->get();
        $notifCount = $notifications->count();
    @endphp

    @if(Auth::user()->role === 'pegawai' || Auth::user()->role === 'admin')
        @php
            $cartsitems = \App\Models\Cart::where('user_id', Auth::id())
                ->where('status', 'active')
                ->with('cartItems.item')
                ->first();
            
            $assignedCategories = collect();
            if (Auth::user()->role === 'pegawai') {
                $assignedCategories = Auth::user()->categories;
            } elseif (Auth::user()->role === 'admin' && request()->is('admin/pegawai/*/produk')) {
                $pegawaiId = request()->segment(3);
                $pegawai = \App\Models\User::find($pegawaiId);
                if ($pegawai) $assignedCategories = $pegawai->categories;
            } else {
                $assignedCategories = \App\Models\Category::all();
            }
        @endphp
        
        <!-- Keranjang Offcanvas (Tetap Pakai Bootstrap JS Tapi Styling Tailwind) -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCart" aria-labelledby="offcanvasCartLabel" style="width: 420px; border: none; box-shadow: -10px 0 30px rgba(0,0,0,0.1);">
            <div class="flex items-center justify-between px-4 py-2 border-b border-slate-100 bg-white">
                <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i class="ri-shopping-cart-2-line text-orange-600"></i>
                    Keranjang
                </h5>
                <button type="button" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-colors" data-bs-dismiss="offcanvas" aria-label="Close">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
            
            <div class="offcanvas-body p-6 bg-slate-50/50">
                <!-- Limit Info -->
                @php
                    $countThisWeek = 0;
                    if (\Illuminate\Support\Facades\Auth::check()) {
                        $now = \Carbon\Carbon::now('Asia/Jakarta');
                        $daysToSubtract = ($now->dayOfWeek === \Carbon\Carbon::SUNDAY) ? 6 : $now->dayOfWeek - 1;
                        $startOfWeek = $now->copy()->subDays($daysToSubtract)->startOfDay();
                        $endOfWeek   = $startOfWeek->copy()->addDays(6)->endOfDay();
                        $countThisWeek = \App\Models\Cart::where('user_id', \Illuminate\Support\Facades\Auth::id())
                            ->whereIn('status', ['pending', 'approved'])
                            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                            ->count();
                    }
                    $maxLimit = 5;
                    $progress = ($maxLimit > 0) ? ($countThisWeek / $maxLimit) * 100 : 0;
                    $isLimitReached = $countThisWeek >= $maxLimit;
                @endphp
                
                <div class="mb-6 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm font-semibold text-slate-600 flex items-center gap-2">
                            <i class="ri-calendar-line text-orange-500"></i> Pengajuan Minggu Ini
                        </span>
                        <span class="text-sm font-bold text-slate-800">{{ $countThisWeek }}/{{ $maxLimit }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="h-2.5 rounded-full transition-all duration-500 {{ $isLimitReached ? 'bg-rose-500' : 'bg-orange-500' }}" style="width: {{ $progress }}%"></div>
                    </div>
                    @if($isLimitReached)
                        <div class="mt-3 p-3 bg-rose-50 text-rose-600 rounded-xl text-sm font-medium flex items-start gap-2">
                            <i class="ri-error-warning-line mt-0.5"></i>
                            Batas maksimal pengajuan minggu ini telah tercapai.
                        </div>
                    @endif
                </div>
                
                <!-- Items list -->
                <div class="space-y-3 mb-6">
                    @if($cartsitems && $cartsitems->cartItems->count() > 0)
                        @foreach($cartsitems->cartItems as $item)
                        <div class="flex items-center p-3 bg-white border border-slate-100 rounded-2xl shadow-sm gap-3">
                            <div class="flex-1 min-w-0">
                                <h6 class="text-sm font-bold text-slate-800 truncate">{{ $item->item->name }}</h6>
                                <p class="text-xs text-slate-400 truncate">{{ $item->item->category->name ?? '-' }}</p>
                            </div>
                            
                            <form action="{{ route('pegawai.permintaan.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="number" name="quantity" value="{{ $item->quantity }}" class="w-16 h-8 text-center text-sm font-semibold bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all" required>
                                <button type="submit" class="p-1.5 text-orange-600 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors">
                                    <i class="ri-check-line"></i>
                                </button>
                            </form>
                            
                            <form action="{{ route('pegawai.cart.destroy', $item->id) }}" method="POST" class="confirm-delete-form" data-item-name="{{ $item->item->name }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-rose-500 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    @else
                        <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                            <i class="ri-shopping-cart-line text-4xl mb-3 text-slate-200"></i>
                            <p class="text-sm font-medium">Keranjang Anda kosong</p>
                        </div>
                    @endif
                </div>
                
                @if($cartsitems && $cartsitems->cartItems->count() > 0)
                    @if($isLimitReached)
                        <button class="w-full py-3.5 px-4 bg-slate-200 text-slate-500 font-bold rounded-xl cursor-not-allowed flex justify-center items-center gap-2" disabled>
                            <i class="ri-lock-line"></i> Limit Tercapai
                        </button>
                    @else
                        <form action="{{ route('pegawai.permintaan.submit', $cartsitems->id ?? 0) }}" method="POST" class="confirm-form">
                            @csrf
                            <button type="submit" class="w-full py-3.5 px-4 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all flex justify-center items-center gap-2">
                                <i class="ri-send-plane-fill"></i> Ajukan Permintaan
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    @endif
@endauth

<!-- Top Navbar -->
<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200 shadow-sm w-full">
    <div class="flex items-center justify-between px-4 py-2">
        
        <!-- Left Section -->
        <div class="flex items-center gap-4">
            <!-- Mobile Toggle -->
            <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 rounded-xl transition-colors">
                <i class="ri-menu-line text-xl"></i>
            </button>
            <!-- Desktop Toggle -->
            <button @click="desktopSidebarCollapsed = !desktopSidebarCollapsed" class="hidden lg:block p-2 -ml-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 rounded-xl transition-colors" title="Toggle Sidebar">
                <i class="ri-menu-fold-line text-xl" x-show="!desktopSidebarCollapsed"></i>
                <i class="ri-menu-unfold-line text-xl" x-show="desktopSidebarCollapsed" style="display: none;"></i>
            </button>
            
            @auth
            @if(Auth::user()->role === 'pegawai' || Auth::user()->role === 'admin')
            @php
                $showSearch = request()->is('pegawai/produk*') || request()->is('admin/request*') || request()->is('admin/itemout*') || request()->is('admin/guests*') || request()->is('admin/pegawai*') || request()->is('admin/rejects*') || request()->is('admin/produk*') || request()->is('admin/transaksi*');
            @endphp
            @if($showSearch)
            <!-- Search Bar -->
            <form action="{{ url()->current() }}" method="GET" class="hidden md:flex items-center bg-slate-50 border border-slate-200 rounded-full px-4 py-2 focus-within:ring-2 focus-within:ring-orange-500/20 focus-within:border-orange-500 transition-all shadow-inner w-96">
                <i class="ri-search-line text-slate-400 mr-2"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari data..." class="bg-transparent border-none outline-none w-full text-sm text-slate-700 placeholder-slate-400 focus:ring-0 p-0">
            </form>
            @endif
            @endif
            @endauth
        </div>
        
        <!-- Right Section -->
        <div class="flex items-center gap-3 md:gap-5">
            @auth
            @if(Auth::user()->role === 'pegawai')
            
            <!-- Cart Icon (Floating style replacing the old fixed badge) -->
            <a href="#offcanvasCart" data-bs-toggle="offcanvas" role="button" class="relative p-2 text-slate-500 hover:bg-slate-100 rounded-full transition-colors group">
                <i class="ri-shopping-cart-2-line text-xl group-hover:text-orange-600 transition-colors"></i>
                @if(isset($cartsitems) && $cartsitems->cartItems->count() > 0)
                <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white animate-pulse"></span>
                @endif
            </a>
            @endif

            <!-- Notification (Alpine Dropdown for All Auth Users) -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open; if(open) markNotificationsAsRead()" @click.outside="open = false" class="relative p-2 text-slate-500 hover:bg-slate-100 rounded-full transition-colors group" id="notification-btn">
                    <i class="ri-notification-3-line text-xl group-hover:text-orange-600 transition-colors"></i>
                    @if($notifCount > 0)
                    <span class="absolute top-0 right-0 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-rose-500 rounded-full border-2 border-white">
                        {{ $notifCount }}
                    </span>
                    @endif
                </button>
                
                <div x-show="open" x-transition.opacity.duration.200ms class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50" style="display: none;">
                    <div class="p-4 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                        <h6 class="text-sm font-bold text-slate-800 m-0">Notifikasi</h6>
                    </div>
                    <div class="max-h-[300px] overflow-y-auto">
                        @forelse($notifications ?? [] as $notif)
                        <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors cursor-pointer">
                            <p class="text-sm font-bold text-slate-800 mb-1">{{ $notif->title ?? 'Pemberitahuan' }}</p>
                            <p class="text-xs text-slate-500 line-clamp-2">{{ $notif->message ?? '-' }}</p>
                        </div>
                        @empty
                        <div class="p-8 text-center text-slate-400">
                            <i class="ri-notification-badge-line text-3xl mb-2 text-slate-200"></i>
                            <p class="text-sm font-medium">Tidak ada notifikasi</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endauth
            
            <!-- Divider -->
            <div class="hidden md:block w-px h-6 bg-slate-200"></div>
            
            <!-- User Profile Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-3 p-1 pr-3 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-full transition-all">
                    <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Profile" class="w-8 h-8 rounded-full border border-white shadow-sm object-cover">
                    <span class="hidden md:block text-sm font-bold text-slate-700 truncate max-w-[120px]">
                        {{ Auth::user()->name ?? 'User' }}
                    </span>
                    <i class="ri-arrow-down-s-line text-slate-400"></i>
                </button>
                
                <div x-show="open" x-transition.opacity.duration.200ms class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50" style="display: none;">
                    <div class="p-4 border-b border-slate-50 bg-slate-50/50">
                        <p class="text-sm font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="p-2">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-orange-600 rounded-xl transition-colors">
                            <i class="ri-user-settings-line"></i> Profile
                        </a>
                        <div class="h-px bg-slate-100 my-1"></div>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
                            <i class="ri-logout-box-r-line"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</header>

@push('scripts')
<script>
window.markNotificationsAsRead = function() {
    // Only show toast and do fetch if there is actually a notification badge
    const badge = document.querySelector('#notification-btn span');
    if (badge) {
        @if(Auth::user()->role === 'pegawai')
        fetch('{{ route("pegawai.notifications.read") }}').catch(()=>{});
        @endif
        
        // Hide badge visually right away
        badge.style.display = 'none';
        
        // Trigger Toast (assuming window.showToast is defined globally)
        if (typeof window.showToast === 'function') {
            window.showToast('info', 'Semua notifikasi telah dibaca');
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.confirm-form');
    const deleteForms = document.querySelectorAll('.confirm-delete-form');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Yakin ingin mengajukan permintaan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ajukan!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'bg-orange-600 text-white px-4 py-2 rounded-lg font-bold border-0',
                    cancelButton: 'bg-slate-100 text-slate-600 px-4 py-2 rounded-lg font-bold border-0 ml-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    deleteForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const itemName = form.dataset.itemName;
            Swal.fire({
                title: 'Konfirmasi!',
                text: `Yakin ingin menghapus ${itemName} dari keranjang?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'bg-rose-600 text-white px-4 py-2 rounded-lg font-bold border-0',
                    cancelButton: 'bg-slate-100 text-slate-600 px-4 py-2 rounded-lg font-bold border-0 ml-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
<style>
/* Adjust swal to show nicely above everything */
.swal2-container { z-index: 10000 !important; }
</style>
@endpush



