@extends('layouts.dashboard')

@section('page-title','Create Agent')

@section('content')

<div class="bg-white rounded-lg shadow p-6">

    <form method="POST" action="{{ route('admin.agents.store') }}">
        @include('admin.agents._form')
    </form>

</div>

@endsection