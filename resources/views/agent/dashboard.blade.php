@extends('layouts.dashboard')

@section('content')

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Agent Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Welcome back, {{ auth()->user()->name }}</p>
    </div>

    {{-- Recent Activity --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
            <a href="{{ route('agent.activity-logs.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                View all →
            </a>
        </div>

        <div class="bg-white border border-gray-100 rounded-md shadow-sm overflow-hidden">
            @forelse($recentActivities as $activity)
                <div class="flex items-start gap-4 px-6 py-4 {{ !$loop->last ? 'border-b border-gray-50' : '' }} hover:bg-gray-50/60 transition-colors">

                    {{-- Icon --}}
                    @php
                        $iconColors = [
                            'created' => 'bg-emerald-50 text-emerald-600',
                            'updated' => 'bg-blue-50 text-blue-600',
                            'deleted' => 'bg-red-50 text-red-600',
                            'toggled_status' => 'bg-amber-50 text-amber-600',
                        ];
                        $iconColor = $iconColors[$activity->action] ?? 'bg-gray-50 text-gray-600';
                    @endphp
                    <div class="w-8 h-8 rounded-full {{ $iconColor }} flex items-center justify-center shrink-0 mt-0.5">
                        @if($activity->action === 'created')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        @elseif($activity->action === 'updated')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        @elseif($activity->action === 'deleted')
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900">
                            {{ $activity->description }}
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            @php
                                $actionColors = [
                                    'created' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'updated' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'deleted' => 'bg-red-50 text-red-700 border-red-200',
                                    'toggled_status' => 'bg-amber-50 text-amber-700 border-amber-200',
                                ];
                                $colorClass = $actionColors[$activity->action] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                            @endphp
                            <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded border {{ $colorClass }}">
                                {{ str_replace('_', ' ', $activity->action) }}
                            </span>
                            <span class="text-xs text-gray-400">
                                by {{ $activity->user->name ?? 'System' }} · {{ $activity->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-400 text-sm">
                    No recent activity yet. Updates on your tour requests will appear here.
                </div>
            @endforelse
        </div>
    </div>

@endsection