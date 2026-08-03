@extends('layouts.app')

@section('body')

<div class="min-h-screen">

    <div
        x-data="{
            sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen') ?? 'true'),

            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
                localStorage.setItem('sidebarOpen', JSON.stringify(this.sidebarOpen));
            }
        }"
        class="flex h-screen"
    >

        <x-sidebar />

        <div class="flex-1 flex flex-col min-w-0 h-screen">

            <x-header />

            <div class="p-6 flex-1 overflow-y-auto">

                <x-flash-message />

                @yield('content')

            </div>

        </div>

    </div>

</div>

@endsection