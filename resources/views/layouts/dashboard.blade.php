@extends('layouts.app')

@section('body')

<div class="min-h-screen bg-gray-100">

    <div class="flex">

        <x-sidebar />

        <div class="flex-1 min-h-screen">

            <x-header />

            <div class="p-6">

                <x-flash-message />

                @yield('content')

            </div>

        </div>

    </div>

</div>

@endsection