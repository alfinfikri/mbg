@extends(getTheme('layouts.app'))

@section('content')
<section class="section-header bg-primary text-white pb-10 pb-sm-8 pb-lg-11">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 text-center">
	  	@if(isset($album))
		  <h1 class="display-2 mb-4">{{ $album->title }} ({{ $gallerys->total() }})</h1>
		@else
		<h1 class="display-2 mb-4">Semua Album</h1>
		@endif
        <p class="lead">Lihat foto kegiatan di {{ getSetting('web_name') }}.</p>
      </div>
    </div>
  </div>
</section>

<section class="section section-lg line-bottom-light">
  <div class="container mt-n10 mt-lg-n12 z-2">
    <div class="row">

	{{-- Album all --}}
	@if(isset($album))
		@foreach($gallerys as $gallery)
		<div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-5">		
		<div class="card bg-white border-light p-4 rounded">
			<a href="{{ url('po-content/uploads/'.$gallery->picture) }}" title="{{ $gallery->title }}" rel="album-gallery">
				<img src="{{ getPicture($gallery->picture, 'original', $gallery->updated_by) }}" class="card-galeri" alt="{{ $gallery->title }}">
			</a>
			<div class="card-body p-0 pt-4"><a href="{{ url('po-content/uploads/'.$gallery->picture) }}" class="h4 text-truncate text-maxline-2">{{ $gallery->title }}</a>
				<div class="d-flex align-items-center my-4"><img class="avatar avatar-sm rounded-circle" src="{{ getPicture($gallery->userpicture, null, $gallery->updated_by) }}">
					<h3 class="h6 small ml-2 mb-0">{{ $gallery->name }}</h3>
					<span class="h6 text-muted small font-weight-normal mb-0 ml-auto">
						@if($gallery->tanggal == null)
						{{ date('d F Y' , strtotime(now())) }}
						@else
						{{ date('d F Y' , strtotime($gallery->tanggal)) }}
						@endif
					</span>
				</div>
			</div>
		</div>
		</div>
		@endforeach
	@else
		@foreach($gallerys as $gallery)
		<div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-5">				
		<div class="card bg-white border-light p-4 rounded">
			<a href="{{ url('album/'.$gallery->seotitle) }}" title="{{ $gallery->title }}" rel="album-gallery">
				<img src=" {{ url('po-content/uploads/'.$gallery->gambar) }} " class="card-galeri" alt="{{ $gallery->title }}">
			</a>
			<div class="card-body p-0 pt-4"><a href="{{ url('album/'.$gallery->seotitle) }}" class="h4 text-truncate text-maxline-2">{{ $gallery->title }}</a>
				<div class="d-flex align-items-center my-4"><img class="avatar avatar-sm rounded-circle" src="{{ getPicture($gallery->picture, null, $gallery->updated_by) }}">
					<h3 class="h6 small ml-2 mb-0">{{ $gallery->name }}</h3>
					<span class="h6 text-muted small font-weight-normal mb-0 ml-auto">
						@if($gallery->tanggal == null)
						{{ date('d F Y' , strtotime(now())) }}
						@else
						{{ date('d F Y' , strtotime($gallery->tanggal)) }}
						@endif
					</span>
				</div>
			</div>
		</div>
		</div>
		@endforeach
	@endif
	<div class="col-12">
	<div class="d-flex justify-content-center">
		<nav aria-label="Page navigation example" class="table-responsive">
		<ul class="pagination">
			{{ $gallerys->links() }}
		</ul>
		</nav>
	</div>
	</div>

	</div>
  </div>
</section>
@endsection