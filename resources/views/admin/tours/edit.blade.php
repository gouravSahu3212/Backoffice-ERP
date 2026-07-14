@extends('layouts.dashboard')

@section('page-title','Edit Agent')

@section('content')

<div class="bg-white rounded-lg shadow p-6">

    <form method="POST" action="{{ route('admin.agents.update', $agent) }}">
        @method('PUT')
        @include('admin.agents._form')
    </form>

</div>

@endsection