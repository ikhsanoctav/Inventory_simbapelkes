@php
    use App\Models\Cart;
    $pendingCount = Cart::where('status', 'pending')->count();
    $approvedCount = Cart::whereIn('status', ['approved', 'approved_partially'])
        ->whereHas('user', function ($u) { $u->where('role', 'pegawai'); })
        ->whereHas('cartItems', function ($q) { $q->whereNull('scanned_at'); })
        ->whereDoesntHave('cartItems', function ($q) { $q->whereNotNull('scanned_at'); }, '=', 0)
        ->count();
@endphp

<!-- Sidebar (Desktop) -->
<aside :class="desktopSidebarCollapsed ? 'w-[72px]' : 'w-72'" class="hidden lg:flex flex-col h-screen px-4 py-4 bg-white border-r border-slate-200 overflow-y-auto custom-scrollbar shadow-sm transition-all duration-300 relative">
    
    <div class="flex items-center mt-2 mb-4 px-1 transition-all duration-300">
        <img src="{{ asset('assets/img/icons/simba.png') }}" class="w-10 h-10 object-contain shrink-0 drop-shadow-sm" alt="SIMBA Logo">
        <div class="flex flex-col overflow-hidden ml-3" x-show="!desktopSidebarCollapsed" x-transition.opacity.duration.300ms>
            <span class="text-xl font-bold text-slate-800 tracking-tight whitespace-nowrap">SIMBA</span>
            <span class="text-[10px] text-slate-400 font-medium tracking-widest uppercase whitespace-nowrap">Manajemen Barang</span>
        </div>
    </div>

    <div class="flex flex-col flex-1 gap-3">
        <nav class="space-y-0.5">
            <p x-show="!desktopSidebarCollapsed" class="px-2 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 whitespace-nowrap overflow-hidden">Main Menu</p>
            <div x-show="desktopSidebarCollapsed" class="h-4"></div>
            
            <!-- SUPER ADMIN -->
            @if (auth()->user()->role === 'super_admin')
            <a href="{{ route('super_admin.dashboard') }}" class="flex items-center justify-start gap-3 px-2 py-1.5 rounded-lg transition-all duration-200 {{ Route::is('super_admin.dashboard') ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-orange-600' }}" :title="desktopSidebarCollapsed ? 'Dashboard' : ''">
                <i class="ri-dashboard-line text-xl shrink-0 {{ Route::is('super_admin.dashboard') ? 'text-white' : 'text-slate-500 group-hover:text-orange-600' }}"></i>
                <span x-show="!desktopSidebarCollapsed" class="font-medium text-sm whitespace-nowrap">Dashboard</span>
            </a>
            @endif

            <!-- ADMIN -->
            @if (auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-start gap-3 px-2 py-1.5 rounded-lg transition-all duration-200 {{ Route::is('admin.dashboard') ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-orange-600' }}" :title="desktopSidebarCollapsed ? 'Dashboard' : ''">
                <i class="ri-dashboard-line text-xl shrink-0 {{ Route::is('admin.dashboard') ? 'text-white' : 'text-slate-500 group-hover:text-orange-600' }}"></i>
                <span x-show="!desktopSidebarCollapsed" class="font-medium text-sm whitespace-nowrap">Dashboard</span>
            </a>
            @endif

            <!-- PEGAWAI -->
            @if (auth()->user()->role === 'pegawai')
            <a href="{{ route('pegawai.dashboard') }}" class="flex items-center justify-start gap-3 px-2 py-1.5 rounded-lg transition-all duration-200 {{ Route::is('pegawai.dashboard') ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-orange-600' }}" :title="desktopSidebarCollapsed ? 'Dashboard' : ''">
                <i class="ri-dashboard-line text-xl shrink-0 {{ Route::is('pegawai.dashboard') ? 'text-white' : 'text-slate-500 group-hover:text-orange-600' }}"></i>
                <span x-show="!desktopSidebarCollapsed" class="font-medium text-sm whitespace-nowrap">Dashboard</span>
            </a>
            <a href="{{ route('pegawai.produk') }}" class="flex items-center justify-start gap-3 px-2 py-1.5 rounded-lg transition-all duration-200 {{ Route::is('pegawai.produk') ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-orange-600' }}" :title="desktopSidebarCollapsed ? 'Produk' : ''">
                <i class="ri-shopping-cart-line text-xl shrink-0 {{ Route::is('pegawai.produk') ? 'text-white' : 'text-slate-500 group-hover:text-orange-600' }}"></i>
                <span x-show="!desktopSidebarCollapsed" class="font-medium text-sm whitespace-nowrap">Produk</span>
            </a>
            <a href="{{ route('pegawai.permintaan.pending') }}" class="flex items-center justify-start gap-3 px-2 py-1.5 rounded-lg transition-all duration-200 {{ Route::is('pegawai.permintaan.pending') ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-orange-600' }}" :title="desktopSidebarCollapsed ? 'Permintaan Pending' : ''">
                <i class="ri-time-line text-xl shrink-0 {{ Route::is('pegawai.permintaan.pending') ? 'text-white' : 'text-slate-500 group-hover:text-orange-600' }}"></i>
                <span x-show="!desktopSidebarCollapsed" class="font-medium text-sm whitespace-nowrap">Permintaan Pending</span>
            </a>
            <a href="{{ route('pegawai.permintaan.history') }}" class="flex items-center justify-start gap-3 px-2 py-1.5 rounded-lg transition-all duration-200 {{ Route::is('pegawai.permintaan.history') ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-orange-600' }}" :title="desktopSidebarCollapsed ? 'Riwayat Permintaan' : ''">
                <i class="ri-history-line text-xl shrink-0 {{ Route::is('pegawai.permintaan.history') ? 'text-white' : 'text-slate-500 group-hover:text-orange-600' }}"></i>
                <span x-show="!desktopSidebarCollapsed" class="font-medium text-sm whitespace-nowrap">Riwayat Permintaan</span>
            </a>
            @endif
        </nav>

        <!-- SUPER ADMIN MENU -->
        @if (auth()->user()->role === 'super_admin')
        <nav class="space-y-0.5">
            <p x-show="!desktopSidebarCollapsed" class="px-2 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 whitespace-nowrap overflow-hidden">Master Data</p>
            <div x-show="desktopSidebarCollapsed" class="h-4 border-t border-slate-100 w-full mb-2"></div>
            @php
                $saMenus = [
                    ['route' => 'super_admin.categories.index', 'icon' => 'ri-stack-line', 'label' => 'Kategori', 'active' => Route::is('super_admin.categories.*')],
                    ['route' => 'super_admin.units.index', 'icon' => 'ri-price-tag-3-line', 'label' => 'Satuan Barang', 'active' => Route::is('super_admin.units.*')],
                    ['route' => 'super_admin.suppliers.index', 'icon' => 'ri-briefcase-3-line', 'label' => 'Supplier', 'active' => Route::is('super_admin.suppliers.*')],
                    ['route' => 'super_admin.items.index', 'icon' => 'ri-box-3-line', 'label' => 'Barang', 'active' => Route::is('super_admin.items.*')],
                    ['route' => 'super_admin.item_ins.index', 'icon' => 'ri-inbox-archive-line', 'label' => 'Barang Masuk', 'active' => Route::is('super_admin.item_ins.*')],
                    ['route' => 'super_admin.users.index', 'icon' => 'ri-group-line', 'label' => 'List Pengguna', 'active' => Route::is('super_admin.users.*')],
                    ['route' => 'super_admin.export.index', 'icon' => 'ri-download-2-line', 'label' => 'Ekspor Data', 'active' => Route::is('super_admin.export.index')],
                    ['route' => 'super_admin.manual_book.index', 'icon' => 'ri-book-open-line', 'label' => 'Buku Panduan', 'active' => Route::is('super_admin.manual_book.index')],
                ];
            @endphp
            @foreach($saMenus as $menu)
            <a href="{{ route($menu['route']) }}" class="flex items-center justify-start gap-3 px-2 py-1.5 rounded-lg transition-all duration-200 {{ $menu['active'] ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-orange-600' }}" :title="desktopSidebarCollapsed ? '{{ $menu['label'] }}' : ''">
                <i class="{{ $menu['icon'] }} text-xl shrink-0 {{ $menu['active'] ? 'text-white' : 'text-slate-500 group-hover:text-orange-600' }}"></i>
                <span x-show="!desktopSidebarCollapsed" class="font-medium text-sm whitespace-nowrap">{{ $menu['label'] }}</span>
            </a>
            @endforeach
        </nav>
        @endif

        <!-- ADMIN MENU -->
        @if (auth()->user()->role === 'admin')
        <nav class="space-y-0.5 relative">
            <p x-show="!desktopSidebarCollapsed" class="px-2 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 whitespace-nowrap overflow-hidden">Manajemen</p>
            <div x-show="desktopSidebarCollapsed" class="h-4 border-t border-slate-100 w-full mb-2"></div>
            
            <a href="{{ route('admin.request') }}" class="flex items-center justify-between px-2 py-1.5 rounded-lg transition-all duration-200 relative {{ Route::is('admin.request') ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-orange-600' }}" :title="desktopSidebarCollapsed ? 'Permintaan' : ''">
                <div class="flex items-center gap-3">
                    <i class="ri-file-list-3-line text-xl shrink-0 {{ Route::is('admin.request') ? 'text-white' : 'text-slate-500 group-hover:text-orange-600' }}"></i>
                    <span x-show="!desktopSidebarCollapsed" class="font-medium text-sm whitespace-nowrap">Permintaan</span>
                </div>
                @if($pendingCount > 0)
                    <span x-show="!desktopSidebarCollapsed" class="flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-rose-500 rounded-full shrink-0" id="badgePending">{{ $pendingCount }}</span>
                    <!-- Condensed badge for collapsed state -->
                    <span x-show="desktopSidebarCollapsed" class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 border-2 border-white rounded-full"></span>
                @endif
            </a>

            <a href="{{ route('admin.itemout.index') }}" class="flex items-center justify-between px-2 py-1.5 rounded-lg transition-all duration-200 relative {{ Route::is('admin.itemout.*') ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-orange-600' }}" :title="desktopSidebarCollapsed ? 'ScanQR' : ''">
                <div class="flex items-center gap-3">
                    <i class="ri-qr-scan-2-line text-xl shrink-0 {{ Route::is('admin.itemout.*') ? 'text-white' : 'text-slate-500 group-hover:text-orange-600' }}"></i>
                    <span x-show="!desktopSidebarCollapsed" class="font-medium text-sm whitespace-nowrap">ScanQR</span>
                </div>
                @if($approvedCount > 0)
                    <span x-show="!desktopSidebarCollapsed" class="flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-emerald-500 rounded-full shrink-0" id="badgeApproved">{{ $approvedCount }}</span>
                    <span x-show="desktopSidebarCollapsed" class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                @endif
            </a>

            @php
                $adminMenus = [
                    ['route' => 'admin.guests.index', 'icon' => 'ri-user-line', 'label' => 'List Guest', 'active' => Route::is('admin.guests.index')],
                    ['route' => 'admin.pegawai.index', 'icon' => 'ri-user-star-line', 'label' => 'List Pegawai', 'active' => Route::is('admin.pegawai.index')],
                    ['route' => 'admin.export.out', 'icon' => 'ri-download-2-line', 'label' => 'Export Brg Keluar', 'active' => Route::is('admin.export.out')],
                    ['route' => 'admin.transaksi.out', 'icon' => 'ri-file-text-line', 'label' => 'Data Transaksi', 'active' => Route::is('admin.transaksi.out')],
                    ['route' => 'admin.rejects.scan', 'icon' => 'ri-close-circle-line', 'label' => 'Scan Reject', 'active' => Route::is('admin.rejects.scan')],
                    ['route' => 'admin.rejects.index', 'icon' => 'ri-delete-bin-line', 'label' => 'Data Reject', 'active' => Route::is('admin.rejects.index')],
                    ['route' => 'admin.manual_book.index', 'icon' => 'ri-book-open-line', 'label' => 'Panduan', 'active' => Route::is('admin.manual_book.index')],
                ];
            @endphp
            @foreach($adminMenus as $menu)
            <a href="{{ route($menu['route']) }}" class="flex items-center justify-start gap-3 px-2 py-1.5 rounded-lg transition-all duration-200 {{ $menu['active'] ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-orange-600' }}" :title="desktopSidebarCollapsed ? '{{ $menu['label'] }}' : ''">
                <i class="{{ $menu['icon'] }} text-xl shrink-0 {{ $menu['active'] ? 'text-white' : 'text-slate-500 group-hover:text-orange-600' }}"></i>
                <span x-show="!desktopSidebarCollapsed" class="font-medium text-sm whitespace-nowrap">{{ $menu['label'] }}</span>
            </a>
            @endforeach
        </nav>
        @endif
    </div>

    <!-- Clock -->
    <div class="mt-auto pt-6 pb-2 border-t border-slate-200">
        <div class="flex items-center gap-2 py-2 bg-slate-50 rounded-xl text-slate-500 text-xs font-medium border border-slate-100 transition-all" :class="desktopSidebarCollapsed ? 'justify-center' : 'px-3 justify-start'">
            <i class="ri-time-line text-lg text-slate-400"></i>
            <span id="server-time" x-show="!desktopSidebarCollapsed" class="whitespace-nowrap overflow-hidden">Memuat...</span>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar (handled by Alpine in main layout) -->
<div x-show="sidebarOpen" class="relative z-50 lg:hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
    <div x-show="sidebarOpen" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 left-0 flex max-w-full pr-10">
                <div x-show="sidebarOpen" x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="pointer-events-auto relative w-screen max-w-xs" @click.outside="sidebarOpen = false">
                    <div class="flex h-full flex-col bg-white py-4 shadow-2xl overflow-y-auto">
                        <div class="px-5 flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('assets/img/icons/simba.png') }}" class="w-10 h-10 object-contain shrink-0 drop-shadow-sm" alt="SIMBA Logo">
                                <span class="text-xl font-bold text-slate-800">SIMBA</span>
                            </div>
                            <button @click="sidebarOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="ri-close-line text-2xl"></i>
                            </button>
                        </div>
                        <div class="px-5">
                            <div class="text-sm text-slate-500 mb-4 px-2">Gunakan menu di layar lebar untuk pengalaman terbaik.</div>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-3 px-3 py-1.5 rounded-lg text-rose-600 hover:bg-rose-50 transition-all duration-200">
                                <i class="ri-logout-box-r-line text-lg"></i>
                                <span class="font-medium text-sm">Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const serverTime = new Date("{{ now()->format('Y-m-d H:i:s') }}");
        const timeDisplay = document.getElementById("server-time");
        if (!timeDisplay) return;
        
        let baseTimestamp = serverTime.getTime();
        let baseLocal = Date.now();
    
        function renderTime() {
            const now = Date.now();
            const diff = now - baseLocal;
            const current = new Date(baseTimestamp + diff);
            const h = current.getHours().toString().padStart(2,'0');
            const m = current.getMinutes().toString().padStart(2,'0');
            const s = current.getSeconds().toString().padStart(2,'0');
            timeDisplay.textContent = `${h}:${m}:${s}`;
            requestAnimationFrame(renderTime);
        }
        
        document.addEventListener("visibilitychange", () => {
            if (!document.hidden) {
                baseTimestamp = new Date("{{ now()->format('Y-m-d H:i:s') }}").getTime();
                baseLocal = Date.now();
            }
        });
        renderTime();
    });
</script>
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #cbd5e1; }
</style>

