@extends(getTheme('layouts.app'))

@section('content')
<!-- Hero Section -->
<section class="max-w-7xl mx-auto px-8 py-12 pt-32">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary to-primary-container p-12 md:p-16 text-white shadow-xl">

        <div class="relative z-10 max-w-2xl">
            <span class="inline-block px-4 py-1.5 rounded-full bg-secondary-container text-on-secondary-container text-sm font-bold mb-6">
                Wawasan &amp; Kabar Terbaru
            </span>

            <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight mb-6 leading-[1.1]">
                Berita &amp; Edukasi
            </h1>

            <p class="text-lg md:text-xl text-primary-fixed leading-relaxed opacity-90">
                Update terbaru mengenai pelaksanaan program Makan Bergizi Gratis di Kota Serang dan tips gizi seimbang untuk generasi emas.
            </p>
        </div>

        <div class="absolute right-0 top-0 h-full w-1/2 opacity-20 pointer-events-none">
            <img
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuArko5sz8QLwdPhnn3N_T6OxTe76etweiJ8tJQCqP3Ixyw9d07svJOdq6GN2t10w0HHnVjKZUFFLgijeA4usT6i1IMtcIwp_BW4lxN_s2mkbHpKoBgWfLo4Ozt33sVGpzrzhbCby2tcgg_D-1Z58dfuI04O4_KjO3GRMDF965s0aaLOkkrOforgqDRBlAbbyNt6Doa3mVlqrnpnkaoSBveC1mMZNfj0Drjge27rMQjb7JCR0pQ_p2bGwKBbX9_9JitnU3UpeL7dz4lq"
                alt="Fresh Healthy Food"
                class="object-cover h-full w-full"
            />
        </div>

    </div>
</section>

<!-- Filter & Search -->
<section class="max-w-7xl mx-auto px-8 mb-12">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">

        <div class="flex flex-wrap gap-3">

            {{-- SEMUA --}}
            <a href="{{ url('category/all') }}"
            class="px-6 py-2.5 rounded-full font-semibold shadow-md
            {{ request()->segment(2) == 'all' ? 'bg-primary text-white' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high' }}">
                Semua
            </a>

            {{-- LOOP KATEGORI --}}
             @foreach(getCategory(7) as $category)
                <a href="{{ url('category/'.$category->seotitle) }}"
                class="px-6 py-2.5 rounded-full
                {{ request()->segment(2) == $category->seotitle
                        ? 'bg-primary text-white'
                        : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high' }}">
                    {{ $category->title }}
                </a>
            @endforeach

        </div>

        <div class="relative max-w-xs w-full">
            <form method="GET" action="{{ url()->current() }}" class="relative max-w-xs w-full">

                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">
                    search
                </span>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari artikel..."
                    class="w-full pl-12 pr-4 py-3 bg-surface-container-low border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-sm"
                />
            </form>
        </div>

    </div>
</section>

<!-- Featured Article -->
<section class="max-w-7xl mx-auto px-8 mb-16">
    <div class="group grid md:grid-cols-2 overflow-hidden rounded-[2rem] bg-surface-container-lowest shadow-2xl">
        @foreach(headlinePost(1,0) as $latestpost)
        <div class="aspect-video md:aspect-auto h-full overflow-hidden">
            <img
                src="{{ getPicture($latestpost->picture, '', $latestpost->updated_by) }}"
                alt="{{ $latestpost->title }}"
                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
            />
        </div>

        <div class="p-10 md:p-14 flex flex-col justify-center border-l border-surface-container-low">
            
            <div class="flex items-center gap-3 mb-6">
                <span class="px-3 py-1 rounded-lg bg-tertiary-fixed text-on-tertiary-fixed text-xs font-bold uppercase">
                    {{ $latestpost->category->title }}
                </span>

                <span class="text-sm text-outline flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">calendar_today</span>
                    {{ date('d F Y' , strtotime($latestpost->tanggal)) }}
                </span>
            </div>

            <h2 class="text-3xl md:text-4xl font-bold text-primary mb-6 leading-tight">
                {{ $latestpost->title }}
            </h2>

            <p class="text-on-surface-variant text-lg mb-8 leading-relaxed">
                {!! \Illuminate\Support\Str::words(strip_tags(htmlspecialchars_decode($latestpost->content)), 20, '...') !!}
            </p>

            <a href="{{ prettyUrl($latestpost) }}" class="flex items-center gap-2 text-primary font-bold">
                Baca Selengkapnya
                <span class="material-symbols-outlined">arrow_forward</span>
            </a>

        </div>
        @endforeach
    </div>
</section>

<!-- Articles Grid -->
<section class="max-w-7xl mx-auto px-8 mb-24">

    <h3 class="text-2xl font-bold text-primary mb-10 flex items-center gap-3">
        <div class="w-2 h-8 bg-secondary rounded-full"></div>
        Artikel Terbaru
    </h3>

    @php
        $posts = latestPostWithPaging(3);
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        @foreach($posts as $latestpost)
        <div class="group bg-surface-container-lowest rounded-3xl overflow-hidden shadow-sm flex flex-col">
            <a href="{{ prettyUrl($latestpost) }}">
                <div class="relative h-56 overflow-hidden">
                    <img
                        src="{{ getPicture($latestpost->picture, '', $latestpost->updated_by) }}"
                        alt="{{ $latestpost->title }}"
                        class="w-full h-full object-cover transition-transform group-hover:scale-110"
                    />

                    <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-white/90 text-primary text-xs font-bold">
                        {{ $latestpost->category->title ?? '-' }}
                    </span>
                </div>

                <div class="p-6 flex flex-col flex-grow">
                    <span class="text-xs text-outline mb-3">
                        {{ date('d F Y' , strtotime($latestpost->tanggal)) }}
                    </span>

                    <h4 class="text-xl font-bold mb-3">
                        {{ $latestpost->title }}
                    </h4>

                    <p class="text-sm text-on-surface-variant mb-6">
                        {!! \Illuminate\Support\Str::words(strip_tags(htmlspecialchars_decode($latestpost->content)), 15, '...') !!}
                    </p>
                </div>
            </a>
        </div>
        @endforeach

    </div>

    <div class="mt-16 flex justify-center">
        {{ $posts->appends([
            'search' => request('search')
        ])->links('pagination.custom') }}
    </div>

</section>

@endsection