@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('dashboard.partials.stats-cards')
    </div>

    @include('dashboard.partials.charts')

    @include('dashboard.partials.tables')
</div>

@section('scripts')
    @include('dashboard.partials.scripts')
@endsection

@endsection
