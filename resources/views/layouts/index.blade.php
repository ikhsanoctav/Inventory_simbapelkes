<!doctype html>
<html
  lang="en"
  data-assets-path="{{asset('assets')}}"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
    $role = auth('web')->check() ? auth('web')->user()->role : 'guest';
    $formattedRole = $role === 'guest'
        ? 'SIMBA'
        : ucwords(str_replace('_', ' ', $role));
    $pageTitle = trim($__env->yieldContent('title') ?: 'Dashboard');
    @endphp

    <title>{{ $formattedRole }} | {{ $pageTitle }}</title>

    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="{{asset('assets/img/icons/simba.png')}}" />
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Old Assets (Bootstrap, Theme for content compatibility) -->
    <link rel="stylesheet" href="{{asset('assets/vendor/css/core.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/demo.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    
    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{asset('assets/vendor/js/helpers.js')}}"></script>
    <script src="{{asset('assets/js/config.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Tailwind & Alpine -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  </head>

  <body class="bg-slate-50 antialiased text-slate-800 font-sans selection:bg-orange-100 selection:text-orange-900 overflow-hidden" x-data="{ sidebarOpen: false, desktopSidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' }" x-init="$watch('desktopSidebarCollapsed', val => localStorage.setItem('sidebarCollapsed', val))">
    
    <!-- App Wrapper -->
    <div class="flex h-screen w-full bg-slate-50 overflow-hidden">
      
      <!-- Sidebar -->
      @include('layouts.components.sidebar')

      <!-- Main Content -->
      <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- Navbar -->
        @include('layouts.components.navbar')

        <!-- Page Content Scrollable Area -->
        <main class="flex-1 overflow-y-auto overflow-x-hidden relative" id="main-content-scroll">
            <div class="p-4 md:p-4 lg:p-8 w-full max-w-7xl mx-auto">
                @yield('content')
            </div>
            
            <!-- Floating Cart for Pegasus -->
            @auth
            @if(Auth::user()->role === 'pegawai')
                @php
                    $cartsitems = \App\Models\Cart::where('user_id', Auth::id())->where('status', 'active')->with('cartItems.item')->first();
                @endphp
                <a href="#offcanvasCart" data-bs-toggle="offcanvas" class="fixed bottom-6 right-6 lg:hidden z-50 flex items-center justify-center w-14 h-14 bg-orange-600 text-white rounded-full shadow-lg hover:bg-orange-700 hover:scale-105 transition-all shadow-orange-500/30">
                    <i class="ri-shopping-cart-2-line text-2xl"></i>
                    @if($cartsitems && $cartsitems->cartItems->count() > 0)
                        <span class="absolute top-0 right-0 flex items-center justify-center w-5 h-5 text-xs font-bold bg-rose-500 rounded-full border-2 border-white">
                            {{ $cartsitems->cartItems->count() }}
                        </span>
                    @endif
                </a>
            @endif
            @endauth
        </main>
      </div>
    </div>

    <!-- Core Scripts needed for older Bootstrap/Theme plugins if any -->
    <script src="{{asset('assets/vendor/libs/jquery/jquery.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
    <script src="{{asset('assets/js/main.js')}}"></script>
    <script src="{{asset('assets/js/dashboards-analytics.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')

    @if(session('swal'))
    <script>
    Swal.fire({
        icon: "{{ session('swal.icon') }}",
        title: "{{ session('swal.title') }}",
        text: "{{ session('swal.text') }}",
        customClass: { confirmButton: 'bg-orange-600 text-white px-4 py-2 rounded-lg font-bold border-0' },
        buttonsStyling: false
    });
    </script>
    @endif

    <script>
        window.Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Global helper for showing toast (useful for realtime notifications)
        window.showToast = function(icon, title) {
            window.Toast.fire({ icon: icon, title: title });
        }
    </script>

    @if(session('success') || session('error') || session('status'))
    <script>
        @if(session('success'))
        window.showToast('success', {!! json_encode(session('success')) !!});
        @endif
        @if(session('error'))
        window.showToast('error', {!! json_encode(session('error')) !!});
        @endif
        @if(session('status'))
        window.showToast('info', {!! json_encode(session('status')) !!});
        @endif
    </script>
    @endif
  </body>
</html>



