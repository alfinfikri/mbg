@extends(getTheme('layouts.app'))

@section('content')

	<section class="relative h-[614px] min-h-[500px] w-full flex items-end overflow-hidden">
		<img alt="Healthy School Meal" class="absolute inset-0 w-full h-full object-cover" data-alt="{{ $post->title }}" src="{{ getPicture($post->picture, null, $post->updated_by) }}" />
		<div class="absolute inset-0 bg-gradient-to-t from-[#181c1e] via-[#181c1e]/40 to-transparent"></div>
		<div class="relative max-w-7xl mx-auto px-8 pb-16 w-full">
			<div class="flex flex-wrap gap-3 mb-6">
				<span class="bg-secondary-container text-on-secondary-container px-4 py-1 rounded-full text-xs font-bold tracking-wider uppercase">Inisiatif
					Nasional</span>
				<span class="bg-tertiary-fixed text-on-tertiary-fixed px-4 py-1 rounded-full text-xs font-bold tracking-wider uppercase">{{ $post->ctitle }}</span>
			</div>
			<h1 class="font-headline text-5xl md:text-6xl font-bold text-white max-w-4xl leading-[1.1] mb-6">
				{{ $post->title }}
			</h1>
			<div class="flex items-center gap-6 text-white/80">
				<div class="flex items-center gap-3">
					<img alt="Author" class="w-10 h-10 rounded-full border-2 border-white/20"
						data-alt="Professional portrait of a male nutritionist in his 40s wearing a clean white shirt, soft studio lighting"
						src="https://lh3.googleusercontent.com/aida-public/AB6AXuCqrCpsYpS9gTwqzc6JzY4A3l7nxAHYSRF6vYUQl9ihvUcWD-aVeB-VHOINI6WpjcAVIIGHIBulnlRww3RlfZ71oLne3wHWpgmQJGf9ebo3R-Q_Cx9dOa8C36SLzlEpDjZCtCrv5XuXv3ZtPcZXxoSjfNK2OoIhMbXnOB8d2G0vFsOBGcVACqDYX8TDmZsefNHz3RSLDHcAoKK3kW_HOe32H8MLJcGjjg2avc5DsJqmFTZjU6ryjDDdZi9KYM2CdVjfoUtoXzIKfj7W" />
					<span class="text-sm font-medium">Oleh {{ $post->name }}</span>
				</div>
				<div class="h-4 w-[1px] bg-white/20"></div>
				<span class="text-sm">{{ \Carbon\Carbon::parse($post->tanggal)->translatedFormat('d F Y') }}</span>
			</div>
		</div>
	</section>
	<!-- Content & Sidebar Layout -->
	<section class="max-w-7xl mx-auto px-8 py-20">
		<div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
			<!-- Main Editorial Content -->
			<article class="lg:col-span-8 space-y-12">
				<div class="space-y-6 text-lg leading-relaxed text-on-background/90">
					{!! html_entity_decode($content) !!}
				</div>
				<!-- Tags & Social -->
				<div class="pt-12 border-t border-surface-container-high flex flex-wrap justify-between items-center gap-6">
					<div class="flex gap-2">
						@foreach(getTag(10) as $tag)
						<span class="bg-surface-container-low px-4 py-1.5 rounded-lg text-sm font-medium text-on-surface-variant"> {{ $tag->title }} </span>
						@endforeach
					</div>
					<div class="flex items-center gap-4">
						<span class="text-sm font-semibold text-on-surface-variant">Bagikan:</span>
						<button
							class="w-10 h-10 flex items-center justify-center rounded-full bg-surface-container-low hover:bg-primary hover:text-white transition-all duration-300">
							<span class="material-symbols-outlined text-[20px]">share</span>
						</button>
						<button
							class="w-10 h-10 flex items-center justify-center rounded-full bg-surface-container-low hover:bg-primary hover:text-white transition-all duration-300">
							<span class="material-symbols-outlined text-[20px]">bookmark</span>
						</button>
					</div>
				</div>
			</article>
			<!-- Sidebar -->
			@include(getTheme('partials.sidebar'))
		</div>
	</section>


@endsection