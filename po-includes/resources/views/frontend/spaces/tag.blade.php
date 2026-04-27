@extends(getTheme('layouts.app'))

@section('content')
	<!-- start tag -->
	<section class="section-header bg-primary text-white pb-10 pb-sm-8 pb-lg-11">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-12 col-md-8 text-center">
					<h2 class="display-2 mb-4"> {{ $tag->title }} ({{ $posts->total() }}) </h2>
				</div>
			</div>
		</div>
	</section>

	<section class="section section-lg line-bottom-light">
		<div class="container mt-n10 mt-lg-n12 z-2">
			<div class="row">
				
				@foreach($posts as $post)
				<div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-5">
					<div class="card bg-white border-light p-4 rounded">
						<a href="{{ prettyUrl($post) }}"><img src="{{ getPicture($post->picture, '', $post->updated_by) }}" class="card-img-top rounded" alt="{{ $post->title }}"></a>
						<div class="card-body p-0 pt-4"><a href="{{ prettyUrl($post) }}" class="h4 text-truncate text-maxline-2">{{ $post->title }}</a>
							<div class="d-flex align-items-center my-4"><img class="avatar avatar-sm rounded-circle" src="{{ getPicture($post->userpicture, null, $post->updated_by) }}">
								<h3 class="h6 small ml-2 mb-0">{{ $post->name }}</h3>
								<span class="h6 text-muted small font-weight-normal mb-0 ml-auto">{{ date('d F Y' , strtotime($post->tanggal)) }}</span>
							</div>
							<p class="mb-0">{!! \Illuminate\Support\Str::words(strip_tags(htmlspecialchars_decode($post->content)), 20, '...') !!}</p>
						</div>
					</div>
				</div>
				@endforeach

				<div class="col-12">
					<div class="d-flex justify-content-center">
						<nav aria-label="Page navigation example" class="table-responsive">
							<ul class="pagination">
								{{ $posts->links() }}
							</ul>
						</nav>
					</div>
				</div>
				
			</div>
		</div>
	</section>
@endsection