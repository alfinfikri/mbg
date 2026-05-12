<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{{ str_replace('_', '-', app()->getLocale()) }}"lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="index, follow" />
    <meta name="generator" content="{{ config('app.version') }}" />
    <meta name="author" content="{{ getSetting('web_author') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="google-site-verification" content="Yo1CfaT3riQJffrz7Wep7rWRsITf9BvDH6Two7vPCEc" />
    {!! SEO::generate() !!}
    <link href="{{ asset('po-admin/lib/datatables.net-dt/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "secondary-fixed-dim": "#78dc77",
                    "surface-container": "#ebeef1",
                    "on-secondary-fixed-variant": "#005313",
                    "primary": "#003f7a",
                    "on-tertiary-container": "#ffbf80",
                    "surface-container-low": "#f1f4f7",
                    "surface": "#f7fafd",
                    "on-tertiary-fixed-variant": "#693c00",
                    "surface-container-high": "#e5e8eb",
                    "primary-fixed": "#d5e3ff",
                    "secondary-fixed": "#94f990",
                    "on-tertiary": "#ffffff",
                    "on-background": "#181c1e",
                    "background": "#f7fafd",
                    "on-surface": "#181c1e",
                    "secondary": "#006e1c",
                    "tertiary-fixed": "#ffdcbe",
                    "on-secondary-fixed": "#002204",
                    "on-secondary": "#ffffff",
                    "outline-variant": "#c2c6d3",
                    "inverse-on-surface": "#eef1f4",
                    "on-tertiary-fixed": "#2c1600",
                    "on-primary-fixed-variant": "#004788",
                    "surface-dim": "#d7dadd",
                    "primary-fixed-dim": "#a7c8ff",
                    "on-surface-variant": "#424751",
                    "inverse-surface": "#2d3133",
                    "on-primary-container": "#b0cdff",
                    "on-primary-fixed": "#001b3b",
                    "on-primary": "#ffffff",
                    "surface-variant": "#e0e3e6",
                    "surface-container-lowest": "#ffffff",
                    "primary-container": "#0056a3",
                    "tertiary-container": "#7f4900",
                    "error-container": "#ffdad6",
                    "surface-tint": "#165fac",
                    "tertiary-fixed-dim": "#ffb870",
                    "on-error-container": "#93000a",
                    "surface-bright": "#f7fafd",
                    "inverse-primary": "#a7c8ff",
                    "outline": "#727782",
                    "tertiary": "#5e3500",
                    "surface-container-highest": "#e0e3e6",
                    "error": "#ba1a1a",
                    "on-secondary-container": "#00731e",
                    "secondary-container": "#91f78e",
                    "on-error": "#ffffff"
            },
            "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
            },
            "fontFamily": {
                    "headline": ["Plus Jakarta Sans"],
                    "body": ["Inter"],
                    "label": ["Inter"]
            }
          },
        },
      }
    </script>
    <style>
        html {
            font-size: 14.4px; /* Global 90% scale from base 16px */
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
        }
        .editorial-gradient {
            background: linear-gradient(135deg, #003f7a 0%, #0056a3 100%);
        }
        .max-w-7xl-compact {
            max-width: 72rem; /* Reduced from 80rem (7xl) to approx 90% */
        }
        #mobile-menu.hidden {
            display: none;
        }
        #mobile-menu:not(.hidden) {
            display: flex;
        }
    </style>
</head>
<body class="bg-surface text-on-surface font-body selection:bg-primary-fixed selection:text-on-primary-fixed">
    <!-- Top Navigation Bar -->
    <header class="fixed top-0 w-full z-50 bg-white/70 backdrop-blur-xl shadow-[0px_12px_32px_rgba(24,28,30,0.06)]">
        <nav class="flex justify-between items-center px-4 md:px-8 h-20 max-w-7xl mx-auto">
            <div class="flex items-center">
                <img
                    src="{{ asset('po-content/uploads/' . getSetting('logo')) }}"
                    alt="Logo"
                    class="h-10 md:h-12 w-auto max-w-[140px] md:max-w-[180px] object-contain"
                >
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-8">
                <a class="{{ request()->is('/') ? 'text-blue-700 border-b-2 border-blue-700 font-bold' : 'text-slate-600 hover:text-blue-600' }} font-headline py-1"
                href="{{ url('/') }}">Home</a>

                <a class="{{ request()->is('sekolah') ? 'text-blue-700 border-b-2 border-blue-700 font-bold' : 'text-slate-600 hover:text-blue-600' }} font-headline"
                href="{{ url('/sekolah') }}">Daftar Sekolah</a>

                <a class="{{ request()->is('sppg*') ? 'text-blue-700 border-b-2 border-blue-700 font-bold' : 'text-slate-600 hover:text-blue-600' }} font-headline"
                href="{{ url('/sppg') }}">Daftar SPPG</a>

                <a class="{{ request()->is('menu') ? 'text-blue-700 border-b-2 border-blue-700 font-bold' : 'text-slate-600 hover:text-blue-600' }} font-headline"
                href="{{ url('/menu') }}">Menu & Gizi</a>

                <a class="{{ request()->is('category/*') ? 'text-blue-700 border-b-2 border-blue-700 font-bold' : 'text-slate-600 hover:text-blue-600' }} font-headline"
                href="{{ url('/category/all') }}">Berita & Edukasi</a>

                <a class="{{ request()->is('contact') ? 'text-blue-700 border-b-2 border-blue-700 font-bold' : 'text-slate-600 hover:text-blue-600' }} font-headline"
                href="{{ url('/contact') }}">Kontak & Pengaduan</a>

                <a class="{{ request()->is('tracking') ? 'text-blue-700 border-b-2 border-blue-700 font-bold' : 'text-slate-600 hover:text-blue-600' }} font-headline"
                href="{{ url('/tracking') }}">Lacak Pengaduan</a>
            </div>

            <div class="flex items-center gap-2 md:gap-4">
                <button class="p-2 hover:bg-slate-100/50 rounded-full transition-colors hidden sm:block">
                    <span class="material-symbols-outlined" data-icon="search">search</span>
                </button>

                <button class="md:hidden p-2 text-primary hover:bg-slate-100 rounded-xl transition-colors" id="menu-toggle">
                    <span class="material-symbols-outlined text-3xl" data-icon="menu">menu</span>
                </button>
            </div>
        </nav>
        <div id="mobile-menu" class="hidden md:hidden flex-col gap-3 px-6 pb-6 bg-white/95 backdrop-blur-xl border-t border-slate-100 shadow-[0px_18px_32px_rgba(24,28,30,0.08)]">
            <a class="{{ request()->is('/') ? 'text-blue-700 font-bold' : 'text-slate-600' }} font-headline py-2"
            href="{{ url('/') }}">Home</a>
            <a class="{{ request()->is('sekolah') ? 'text-blue-700 font-bold' : 'text-slate-600' }} font-headline py-2"
            href="{{ url('/sekolah') }}">Daftar Sekolah</a>
            <a class="{{ request()->is('sppg*') ? 'text-blue-700 font-bold' : 'text-slate-600' }} font-headline py-2"
            href="{{ url('/sppg') }}">Daftar SPPG</a>
            <a class="{{ request()->is('menu') ? 'text-blue-700 font-bold' : 'text-slate-600' }} font-headline py-2"
            href="{{ url('/menu') }}">Menu & Gizi</a>
            <a class="{{ request()->is('category/*') ? 'text-blue-700 font-bold' : 'text-slate-600' }} font-headline py-2"
            href="{{ url('/category/all') }}">Berita & Edukasi</a>
            <a class="{{ request()->is('contact') ? 'text-blue-700 font-bold' : 'text-slate-600' }} font-headline py-2"
            href="{{ url('/contact') }}">Kontak & Pengaduan</a>
            <a class="{{ request()->is('tracking') ? 'text-blue-700 font-bold' : 'text-slate-600' }} font-headline py-2"
            href="{{ url('/tracking') }}">Lacak Pengaduan</a>
        </div>
    </header>

 
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-100 py-16 px-8 mt-auto border-t border-slate-200">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-1">
                <h2 class="text-2xl font-black text-blue-900 font-headline mb-6">Makan Bergizi Gratis</h2>
                <p class="text-slate-500 text-sm leading-relaxed mb-6">
                    Inisiatif nasional untuk mendukung kesehatan dan kecerdasan anak bangsa melalui penyediaan nutrisi berkualitas setiap hari sekolah.
                </p>
                <div class="flex gap-4">
                    <a class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-primary shadow-sm hover:bg-primary hover:text-white transition-all" href="#">
                    <span class="material-symbols-outlined" data-icon="facebook">social_leaderboard</span>
                    </a>
                    <a class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-primary shadow-sm hover:bg-primary hover:text-white transition-all" href="#">
                    <span class="material-symbols-outlined" data-icon="camera">camera</span>
                    </a>
                    <a class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-primary shadow-sm hover:bg-primary hover:text-white transition-all" href="#">
                    <span class="material-symbols-outlined" data-icon="video_library">video_library</span>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-on-surface mb-6">Tautan Cepat</h4>
                <ul class="space-y-4">
                    <li><a class="text-slate-500 hover:text-orange-500 transition-colors text-sm" href="#">Beranda</a></li>
                    <li><a class="text-slate-500 hover:text-orange-500 transition-colors text-sm" href="#">Tentang Program</a></li>
                    <li><a class="text-slate-500 hover:text-orange-500 transition-colors text-sm" href="#">Peta Layanan</a></li>
                    <li><a class="text-slate-500 hover:text-orange-500 transition-colors text-sm" href="#">Standar Gizi</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-on-surface mb-6">Bantuan &amp; Dukungan</h4>
                <ul class="space-y-4">
                    <li><a class="text-slate-500 hover:text-orange-500 transition-colors text-sm" href="#">Kebijakan Privasi</a></li>
                    <li><a class="text-slate-500 hover:text-orange-500 transition-colors text-sm" href="#">Syarat &amp; Ketentuan</a></li>
                    <li><a class="text-slate-500 hover:text-orange-500 transition-colors text-sm" href="#">Peta Situs</a></li>
                    <li><a class="text-slate-500 hover:text-orange-500 transition-colors text-sm" href="#">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-on-surface mb-6">Hubungi Kami</h4>
                <p class="text-slate-500 text-sm leading-relaxed mb-4">
                    {{ getSetting('address') }}
                </p>
                <div class="flex items-center gap-3 text-sm font-semibold text-primary">
                    <span class="material-symbols-outlined text-sm" data-icon="call">call</span>
                        {{ getSetting('telephone') }}
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto mt-16 pt-8 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-slate-500 text-sm">© 2026 Satgas Makan Bergizi Gratis. Dinas Komunikasi dan Informatika Kota Serang.</p>
            <div class="flex gap-6">
                <a class="text-slate-500 hover:text-primary text-xs uppercase tracking-widest font-bold" href="https://ragem.serangkota.go.id/">Layanan Publik</a>
                <a class="text-slate-500 hover:text-primary text-xs uppercase tracking-widest font-bold" href="https://bgn.go.id/">Badan Gizi Nasional</a>
            </div>
        </div>
    </footer>
</body>
    <script src="{{ asset('po-admin/lib/jquery/jquery.min.js') }}"></script>
	<script src="{{ asset('po-admin/lib/datatables.net/js/jquery.dataTables.min.js') }}"></script>
  	<script src="{{ asset('po-admin/lib/datatables.net-dt/js/dataTables.dataTables.min.js') }}"></script>
  	<script src="{{ asset('po-admin/lib/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
  	<script src="{{ asset('po-admin/lib/datatables.net-responsive-dt/js/responsive.dataTables.min.js') }}"></script>
    @stack('scripts')
    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const icon = menuToggle.querySelector('.material-symbols-outlined');

        menuToggle.addEventListener('click', () => {
            const isHidden = mobileMenu.classList.contains('hidden');
            
            if (isHidden) {
                mobileMenu.classList.remove('hidden');
                icon.textContent = 'close';
                document.body.classList.add('overflow-hidden');
            } else {
                mobileMenu.classList.add('hidden');
                icon.textContent = 'menu';
                document.body.classList.remove('overflow-hidden');
            }
        });

        // Close menu on resize if above mobile breakpoint
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                mobileMenu.classList.add('hidden');
                icon.textContent = 'menu';
                document.body.classList.remove('overflow-hidden');
            }
        });
    </script>
</html>
