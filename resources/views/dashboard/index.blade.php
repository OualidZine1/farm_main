@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @include('dashboard.partials.stats-cards')
    @include('dashboard.partials.charts')
    @include('dashboard.partials.tables')
</div>
@endsection

@section('scripts')
    @include('dashboard.partials.scripts')
@endsection
