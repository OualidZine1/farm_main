@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h1 class="text-danger">500</h1>
                </div>
                <div class="card-body">
                    <h2 class="text-danger">{{ __('Oops! Something went wrong.') }}</h2>
                    <p>{{ $message ?? __('An unexpected error occurred.') }}</p>
                    <p>{{ __('Please try again later or contact support.') }}</p>
                    <a href="{{ url('/') }}" class="btn btn-primary">
                        {{ __('Back to Home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
