@extends('layouts.dashboard')

@section('page-title', 'Agents')

@section('content')

<div class="bg-white rounded-lg shadow">

    <div class="flex justify-between items-center p-6 border-b">

        <h2 class="text-xl font-semibold">
            Agents
        </h2>

        <a href="{{ route('admin.agents.create') }}"
           class="px-4 py-2 bg-gray-800 text-white rounded">

            + Add Agent

        </a>

    </div>

    <div class="p-6">

        <form method="GET">

            <div class="flex gap-3">

                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search agent..."
                    class="border rounded px-4 py-2 w-80">

                <button
                    class="px-4 py-2 bg-gray-800 text-white rounded">

                    Search

                </button>

            </div>

        </form>

    </div>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead class="bg-gray-100">

            <tr>

                <th class="p-3 text-left">Name</th>

                <th class="p-3 text-left">Email</th>

                <th class="p-3 text-left">Phone</th>

                <th class="p-3 text-left">Status</th>

                <th class="p-3 text-left">Last Login</th>

                <th class="p-3 text-center">Actions</th>

            </tr>

            </thead>

            <tbody>

            @forelse($agents as $agent)

                <tr class="border-t">

                    <td class="p-3">
                        {{ $agent->name }}
                    </td>

                    <td class="p-3">
                        {{ $agent->email }}
                    </td>

                    <td class="p-3">
                        {{ $agent->phone ?: '-' }}
                    </td>

                    <td class="p-3">

                        @if($agent->is_active)

                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">
                                Active
                            </span>

                        @else

                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs">
                                Inactive
                            </span>

                        @endif

                    </td>

                    <td class="p-3">

                        {{ $agent->last_login_at?->diffForHumans() ?? 'Never' }}

                    </td>

                    <td class="p-3 text-center">

                        <a href="{{ route('admin.agents.edit',$agent) }}"
                           class="text-blue-600">

                            Edit

                        </a>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="6" class="text-center p-10 text-gray-500">

                        No agents found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    <div class="p-6">

        {{ $agents->links() }}

    </div>

</div>

@endsection