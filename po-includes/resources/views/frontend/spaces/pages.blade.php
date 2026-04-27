@extends(getTheme('layouts.app'))

@section('content')
	<section class="section-header bg-primary text-white pb-9 pb-lg-12 mb-4 mb-lg-6">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-12 col-md-8 text-center">
					<div class="mb-4"> <a href="#" class="badge bg-secondary text-uppercase px-3">{{ $pages->seotitle }}</a></div>
					<h1 class="display-2 mb-3">{{ $pages->title }}</h1>
					<div class="post-meta"> Tanggal di buat : <span class="post-date mr-2"></span> <span class="post-date mr-3"><span class="far fa-calendar-alt mr-2"></span> @if ($pages->created_at == null) - @else {{ date('d F Y H:i', strtotime($pages->created_at)) }} @endif</span> | <span class="post-date mr-2"></span> Tanggal di edit : <span class="post-date mr-2"></span> <span class="post-date mr-3"><span class="far fa-calendar-alt mr-2"></span> @if($pages->updated_at == null) - @else {{ date('d F Y H:i', strtotime($pages->updated_at)) }} @endif</span> </div>
				</div>
			</div>
		</div>
	</section>
	
	<!-- isi kontent -->
	<div class="section section-lg pt-0">
		<div class="container mt-n8 mt-lg-n12 z-2">
			<div class="row">
				
				<div class="col-12 col-lg-8 card border-light">
					<div class="row mb-5">
						<div class="col-12 ">
							<div class="fancy-gallery mb-5 p-3">
								<div class="row">
									<img class="d-block rounded w-100" src="{{ getPicture($pages->picture, null, $pages->updated_by) }}" alt="{{ $pages->title }}">
								</div>
							</div>
							
							<div class="table-responsive">
							<div class="blog-text justify-content">
								{!! html_entity_decode($pages->content) !!}
							</div>
							</div>
						</div>
					</div>
				</div>
	
				@include(getTheme('partials.sidebar'))
	
			</div>
		</div>
	</div>
@endsection