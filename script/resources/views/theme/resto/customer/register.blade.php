@extends('theme.resto.layouts.app')
@section('content')
<!-- start Registration section -->
<section class="login section">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2 col-12">
				<div class="form-head">
					<h4 class="title">{{ __('Create Your Account') }}</h4>

					@if(Session::has('error') || Session::has('msg'))
						<div class="alert alert-danger" role="alert" style="margin-bottom: 20px;">
							{{ Session::get('error') ?: Session::get('msg') }}
						</div>
					@endif
					@if(Session::has('success'))
						<div class="alert alert-success" role="alert" style="margin-bottom: 20px;">
							{{ Session::get('success') }}
						</div>
					@endif

					<div class="mb-4 text-center">
						<a href="{{ url('/customer/login') }}" class="btn w-100 d-flex align-items-center justify-content-center" style="background: #007bff; color: #fff; font-weight: 600; padding: 12px; border-radius: 6px; text-decoration: none;">
							<span style="letter-spacing: 0.5px;">{{ __('Sign up / Login with your Booostr User Account') }}</span>
						</a>
						<div style="display: flex; align-items: center; text-align: center; margin: 20px 0;">
							<hr style="flex: 1; border: none; border-top: 1px solid #e0e0e0; margin: 0;">
							<span style="padding: 0 15px; color: #888; font-size: 13px; font-weight: bold;">OR</span>
							<hr style="flex: 1; border: none; border-top: 1px solid #e0e0e0; margin: 0;">
						</div>
					</div>

					<form action="{{ route('customer.register') }}" method="post">
						@csrf
						<div class="row">
							<div class="col-md-6 col-12">
								<div class="form-group">
									<label>{{ __('First Name') }}</label>
									<input class="margin-5px-bottom" value="{{ old('fname') }}" type="text" name="fname" required placeholder="First Name"/>
									@error('fname')
									<span class="text-danger small" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror
								</div>
							</div>
							<div class="col-md-6 col-12">
								<div class="form-group">
									<label>{{ __('Last Name') }}</label>
									<input class="margin-5px-bottom" value="{{ old('lname') }}" type="text" name="lname" required placeholder="Last Name"/>
									@error('lname')
									<span class="text-danger small" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror
								</div>
							</div>
						</div>

						<div class="form-group">
							<label>{{ __('Username') }}</label>
							<input class="margin-5px-bottom" value="{{ old('username') }}" type="text" name="username" required placeholder="Username"/>
							@error('username')
							<span class="text-danger small" role="alert">
								<strong>{{ $message }}</strong>
							</span>
							@enderror
						</div>

						<div class="form-group">
							<label>{{ __('Email') }}</label>
							<input class="margin-5px-bottom" value="{{ old('email') }}" type="email" name="email" required placeholder="Email"/>
							@error('email')
							<span class="text-danger small" role="alert">
								<strong>{{ $message }}</strong>
							</span>
							@enderror
						</div>

						<div class="form-group">
							<label>{{ __('Password') }}</label>
							<input class="margin-5px-bottom" name="password" type="password" required placeholder="Password (minimum 8 characters)"/>
							@error('password')
							<span class="text-danger small" role="alert">
								<strong>{{ $message }}</strong>
							</span>
							@enderror
						</div>

						<div class="form-group">
							<label>{{ __('Confirm Password') }}</label>
							<input class="margin-5px-bottom" required name="password_confirmation" type="password" placeholder="Confirm Password"/>
							@error('password_confirmation')
							<span class="text-danger small" role="alert">
								<strong>{{ $message }}</strong>
							</span>
							@enderror
						</div>

						<div class="form-group">
							<label>{{ __('Age') }}</label>
							<select name="age" class="form-control margin-5px-bottom" required style="height: 48px; border: 1px solid #eee; border-radius: 6px; padding: 0 15px;">
								<option value="" disabled {{ old('age') ? '' : 'selected' }}>{{ __('Choose Age') }}</option>
								<option value="child" {{ old('age') == 'child' ? 'selected' : '' }}>{{ __('Above 13 - Under 19') }}</option>
								<option value="adult" {{ old('age') == 'adult' ? 'selected' : '' }}>{{ __('Above 19') }}</option>
							</select>
							@error('age')
							<span class="text-danger small" role="alert">
								<strong>{{ $message }}</strong>
							</span>
							@enderror
						</div>
						
						<div class="button mt-4">
							<button type="submit" class="btn">{{ __('Register') }}</button>
						</div>
						<p class="outer-link">{{ __('Already have an account?') }} <a href="{{ url('/customer/login') }}">{{ __('Login') }}</a></p>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- End Registration section -->
@endsection
