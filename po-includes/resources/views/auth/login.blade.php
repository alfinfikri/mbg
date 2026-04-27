@extends('layouts.auth')
@section('title', __('auth.login_title'))

@section('content')
	<div class="content content-fixed content-auth">
		<div class="container">
			<div class="media align-items-stretch justify-content-center ht-100p pos-relative">
				<div class="media-body align-items-center d-none d-lg-flex">
					<div class="mx-wd-600">
						<img src="{{ asset('po-content/uploads/' . getSetting('logo')) }}" class="img-fluid" alt="">
					</div>
					<div class="pos-absolute b-0 l-0 tx-12 tx-center">
						<a href="{{ getSetting('web_url') }}" target="_blank" rel="nofollow"> {{ getSetting('web_name') }} </a>
					</div>
				</div>

				<div class="sign-wrapper mg-lg-l-50 mg-xl-l-60">
					<div class="wd-100p">
						<h3 class="tx-color-01 mg-b-5">{{ __('auth.signin') }}</h3>
						<p class="tx-color-03 tx-16 mg-b-40">{{ __('auth.signin_intro') }}</p>

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

						<form method="POST" action="{{ route('login') }}">
							@csrf
							<input type="hidden" name="remember" value="">

							<div class="form-group">
								<label>{{ __('auth.email') }}</label>
								<input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="{{ __('auth.email_text') }}">
								@error('email')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
								@enderror
							</div>

							<div class="form-group">
								<div class="d-flex justify-content-between mg-b-5">
									<label class="mg-b-0-f">{{ __('auth.password') }}</label>
									@if (Route::has('password.request'))
									<a href="{{ route('password.request') }}" class="tx-13">{{ __('auth.forgot_question') }}</a>
									@endif
								</div>

								<input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ __('auth.password_text') }}">
								@error('password')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
								@enderror
							</div>

							<button class="btn btn-brand-02 btn-block" type="submit">{{ __('auth.signin') }}</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection