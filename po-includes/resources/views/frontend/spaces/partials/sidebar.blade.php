<!-- start sidebar -->
 <aside class="lg:col-span-4 space-y-12">
  <!-- Related Articles Widget -->
  <div class="bg-surface-container-low p-8 rounded-2xl">
    <h3 class="font-headline text-xl font-bold mb-8 flex items-center gap-3">
      <span class="w-2 h-6 bg-primary rounded-full"></span>
      Berita Terkait
    </h3>
    <div class="space-y-8">
      @foreach(postByCategory($post->category_id,3) as $key => $latestpost)
        <a class="group flex gap-4" href="{{ prettyUrl($latestpost) }}">
          <img class="w-24 h-24 object-cover rounded-xl shrink-0 group-hover:scale-105 transition-transform" data-alt="{{ $latestpost->title }}" src="{{ getPicture($latestpost->picture, null, $latestpost->updated_by) }}" />
          <div class="space-y-1">
            <h4 class="font-headline text-sm font-bold text-on-surface leading-tight group-hover:text-primary transition-colors">{{ $latestpost->title }}</h4>
            <span class="text-xs text-on-surface-variant/70">{{ date('d F Y' , strtotime($latestpost->created_at)) }}</span>
          </div>
        </a>
      @endforeach
    </div>
  </div>
  <!-- Newsletter Widget -->
  <div class="bg-primary p-8 rounded-2xl text-white relative overflow-hidden">
    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
    <h3 class="font-headline text-xl font-bold mb-4 relative z-10">Update Mingguan</h3>
    <p class="text-white/80 text-sm mb-6 relative z-10 leading-relaxed">
      Dapatkan informasi terbaru tentang perkembangan gizi nasional langsung ke email Anda.
    </p>
    <form class="space-y-3 relative z-10">
      <input
        class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-secondary-container outline-none placeholder:text-white/50"
        placeholder="Email Anda" type="email" />
      <button
        class="w-full bg-secondary-container text-on-secondary-container py-3 rounded-xl font-bold text-sm hover:brightness-105 transition-all">
        Berlangganan
      </button>
    </form>
  </div>
  <!-- Featured Menu Card -->
  <div class="border-2 border-surface-container-high p-8 rounded-2xl">
    <div class="flex items-center justify-between mb-6">
      <h3 class="font-headline text-lg font-bold">Menu Hari Ini</h3>
      <span class="material-symbols-outlined text-secondary">restaurant</span>
    </div>
    <div class="space-y-4">
        @forelse($menus as $menu)
            <div class="flex justify-between items-center py-2 border-b border-surface-container-high">
                <span class="text-sm font-medium">
                    {{ $menu->nama }}
                </span>
                <span class="text-xs font-bold text-on-surface-variant bg-surface-container-high px-2 py-0.5 rounded">
                    {{ ($menu->kecil_energi + $menu->besar_energi) / 2 }} kkal
                </span>
            </div>
        @empty
            <div class="text-sm text-gray-500">
                Data menu belum tersedia
            </div>
        @endforelse
    </div>
    <a href="{{ url('/menu') }}"> 
      <button class="mt-8 w-full border border-primary text-primary font-bold py-3 rounded-xl text-sm hover:bg-primary/5 transition-all">
        Jadwal Menu 
      </button>
    </a>
  </div>
</aside>

<!-- end sidebar -->