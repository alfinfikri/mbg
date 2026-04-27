@extends('layouts.auth')
@section('title', __('auth.reset_title'))

@section('content')
	<div class="content content-fixed content-auth-alt">
		<div class="container d-flex justify-content-center ht-100p">
			<div class="mx-wd-300 wd-sm-450 ht-100p d-flex flex-column align-items-center justify-content-center">
				<div class="wd-80p wd-sm-300 mg-b-15"><img src="{{ asset('po-admin/assets/img/img18.png') }}" class="img-fluid" alt=""></div>

				<h4 class="tx-20 tx-sm-24">{{ __('auth.reset_password') }}</h4>
				<p class="tx-color-03 mg-b-30 tx-center">{{ __('auth.reset_password_intro') }}</p>

				@if(session('status'))
				<p class="alert alert-success">{{ session('status') }}</p>
				@endif

				@if (Session::has('flash_message'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						{{ Session::get('flash_message') }}
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					</div>
				@endif

				@if (Session::has('error'))
					<div class="alert alert-warning alert-dismissible fade show" role="alert">
						{{ Session::get('error') }}
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
				@endif

				@if ($errors->any())
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<ul class="alert-link">
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
						</button>
					</div>
				@endif

				<form method="POST" action="{{ route('password.email') }}">
					@csrf
					<div class="wd-100p mg-b-40">
						<div class="input-group">
							<input type="email" class="form-control rounded @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="{{ __('auth.email_text') }}">
							<button class="btn btn-brand-02 mg-sm-l-10" type="submit">{{ __('auth.reset_title') }}</button>
							@error('email')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>
					</div>
				</form>
				<a href="{{ url('login') }}" rel="nofollow">Kembali Ke Login</a></span>
				<a href="https://serangkota.go.id/" rel="nofollow">IT Diskominfo Kota Serang</a></span>
			</div>
		</div>
	</div>
@endsection
