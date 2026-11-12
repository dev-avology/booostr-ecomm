@extends('theme.resto.layouts.app')

@section('content')
<!-- Pre-Checkout Authentication Gate -->
<section class="login section py-5" style="background-color: #f9f9f9; min-height: 65vh;">
    <div class="container" style="max-width: 980px;">
        
        <!-- Instruction Heading -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <p class="mb-0" style="font-size: 16px; color: #2c3e50; font-weight: 500;">
                    {{ __('Complete your purchase by logging in to your existing account, or creating a new account, or checking out as a guest.') }}
                </p>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger text-center mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Two Columns (Cards) -->
        <div class="row align-items-stretch">
            
            <!-- Left Column: Log In -->
            <div class="col-md-6 mb-4 mb-md-0 d-flex">
                <div class="form-head w-100 p-4 p-lg-5" style="background: #ffffff; border-radius: 10px; box-shadow: 0px 0px 30px rgba(0, 0, 0, 0.07); border: 1px solid #f0f0f0; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <h4 class="title text-center mb-4" style="font-size: 24px; font-weight: 600; color: #081828;">{{ __('Log In') }}</h4>
                        
                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            
                            <div class="form-group mb-3">
                                <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500; color: #081828;">{{ __('Email or Username') }}</label>
                                <input type="text" name="email" class="form-control" placeholder="{{ __('Enter Email or Username') }}" value="{{ old('email') }}" required style="width: 100%; min-height: 48px; padding: 10px 16px; border: 1px solid #e5e5e5; border-radius: 5px; background-color: #f8f9fa; font-size: 14px; color: #081828;">
                            </div>
                            
                            <div class="form-group mb-4">
                                <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500; color: #081828;">{{ __('Password') }}</label>
                                <input type="password" name="password" class="form-control" placeholder="{{ __('Enter Password') }}" required style="width: 100%; min-height: 48px; padding: 10px 16px; border: 1px solid #e5e5e5; border-radius: 5px; background-color: #f8f9fa; font-size: 14px; color: #081828;">
                            </div>
                            
                            <div class="button mt-2">
                                <button type="submit" class="btn primary animate w-100" style="background: var(--primary); color: #ffffff; height: 50px; font-weight: 600; font-size: 14px; border: none; border-radius: 5px; text-transform: uppercase; letter-spacing: 0.5px; transition: all 0.3s ease;">
                                    {{ __('Sign In') }}
                                </button>
                            </div>
                            
                            <p class="outer-link text-center mt-3 mb-0" style="font-size: 13px; color: #666;">
                                {{ __('Do not have an account?') }} 
                                <a href="{{ url('/customer/register') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">{{ __('Create an account.') }}</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Guest Checkout -->
            <div class="col-md-6 d-flex">
                <div class="form-head w-100 p-4 p-lg-5" style="background: #ffffff; border-radius: 10px; box-shadow: 0px 0px 30px rgba(0, 0, 0, 0.07); border: 1px solid #f0f0f0; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <h4 class="title text-center mb-4" style="font-size: 24px; font-weight: 600; color: #081828;">{{ __("Don't Want to Create an Account?") }}</h4>
                        <p class="text-center text-muted" style="font-size: 14px; line-height: 1.6; margin-top: 20px;">
                            {{ __('Checkout quickly as a guest without creating an account. All order tracking and receipt notifications will be sent directly to your email.') }}
                        </p>
                    </div>
                    
                    <div>
                        <div class="button mt-4">
                            <a href="{{ url('/checkout?guest=1') }}" class="btn primary animate w-100 d-flex align-items-center justify-content-center" style="background: var(--primary); color: #ffffff; height: 50px; font-weight: 600; font-size: 14px; border-radius: 5px; text-decoration: none; transition: all 0.3s ease;">
                                {{ __('Checkout as a Guest') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer / Booster signup note -->
        <div class="row mt-5 pt-3">
            <div class="col-12 text-center">
                <p class="mb-0 text-muted" style="font-size: 13.5px; line-height: 1.6;">
                    {{ __('Do you manage a booster club, PTO, or small nonprofit? Get started with Booostr for FREE for your organization:') }} 
                    <a href="https://booostr.co" target="_blank" style="color: var(--primary); font-weight: 600; text-decoration: none;">{{ __('Add Your Organization.') }}</a>
                </p>
            </div>
        </div>

    </div>
</section>
@endsection
