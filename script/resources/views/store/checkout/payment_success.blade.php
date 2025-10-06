@extends('layouts.checkout')
@section('content')
<div class="container py-5 text-center">
    <h1 class="text-success">🎉 Payment Successful!</h1>
    <p>Your payment was completed successfully.</p>
    <p><strong>Payment Intent ID:</strong> {{ $paymentIntentId }}</p>

    <a href="{{ url('/') }}" class="btn btn-primary mt-3">Go to Home</a>
</div>
@endsection