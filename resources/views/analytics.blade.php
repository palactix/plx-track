@extends('layouts.public')
@section("head")
{{-- Chart.js for beautiful charts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
@endsection

@section('content')
    <livewire:link-analytics :shortCode="$shortCode" />
@endsection
