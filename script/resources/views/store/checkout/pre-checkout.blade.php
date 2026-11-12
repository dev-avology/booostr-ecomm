@extends('theme.resto.layouts.app')

@section('content')
<div class="pre-checkout-wrapper py-5" style="background-color: #ffffff; min-height: 60vh;">
    <div class="container" style="max-width: 960px;">
        
        <!-- Instruction Heading -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <p class="mb-0" style="font-size: 1.15rem; color: #333; font-weight: 400;">
                    Complete your purchase by logging in to your existing account, or creating a new account, or checking out as a guest.
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

        <!-- Two Columns -->
        <div class="row mt-4 align-items-start">
            
            <!-- Left Column: Log In -->
            <div class="col-md-6 mb-5 mb-md-0 px-md-4">
                <h2 class="text-center mb-4" style="font-size: 1.75rem; font-weight: 700; color: #1a1a1a;">Log In</h2>
                
                <form action="{{ route('login') }}" method="POST" class="auth-gate-form">
                    @csrf
                    
                    <div class="form-group mb-3">
                        <input type="text" name="email" class="form-control" placeholder="Email or Username" value="{{ old('email') }}" required style="background-color: #eef3fc; border: 1px solid #d4def0; border-radius: 4px; height: 48px; padding: 10px 16px; font-size: 15px; color: #333;">
                    </div>
                    
                    <div class="form-group mb-4">
                        <input type="password" name="password" class="form-control" placeholder="Password" required style="background-color: #eef3fc; border: 1px solid #d4def0; border-radius: 4px; height: 48px; padding: 10px 16px; font-size: 15px; color: #333;">
                    </div>
                    
                    <button type="submit" class="btn w-100" style="background-color: #ff5722; border: none; color: #ffffff; font-weight: 700; font-size: 15px; padding: 13px 20px; border-radius: 4px; letter-spacing: 0.5px; text-transform: uppercase;">
                        SIGN IN
                    </button>
                    
                    <div class="text-center mt-3">
                        <span style="color: #666; font-size: 14px;">Do not have an account?</span>
                        <a href="{{ url('/customer/register') }}" style="color: #007bff; font-weight: 500; font-size: 14px; text-decoration: none;">Create an account.</a>
                    </div>
                </form>
            </div>

            <!-- Right Column: Guest Checkout -->
            <div class="col-md-6 px-md-4">
                <h2 class="text-center mb-4" style="font-size: 1.75rem; font-weight: 700; color: #1a1a1a;">Don't Want to Create an Account?</h2>
                
                <div class="guest-gate-wrapper" style="margin-top: 85px;">
                    <a href="{{ url('/checkout?guest=1') }}" class="btn w-100 d-flex align-items-center justify-content-center" style="background-color: #ff5722; border: none; color: #ffffff; font-weight: 700; font-size: 15px; height: 48px; border-radius: 4px; text-decoration: none;">
                        Checkout as a Guest
                    </a>
                </div>
            </div>

        </div>

        <!-- Footer / Booster signup note -->
        <div class="row mt-5 pt-4">
            <div class="col-12 text-center">
                <p class="mb-0 text-muted" style="font-size: 13.5px; line-height: 1.6;">
                    Do you manage a booster club, PTO, or small nonprofit? Get started with Booostr for FREE for your organization: 
                    <a href="https://booostr.co" target="_blank" style="color: #007bff; text-decoration: none; font-weight: 500;">Add Your Organization</a>.
                </p>
            </div>
        </div>

    </div>
</div>
@endsection

