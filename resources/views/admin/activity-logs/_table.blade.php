@forelse($logs as $log)
    @php
        $isNew = isset($lastSeenAt) && $log->created_at->gt($lastSeenAt);
    @endphp
    <tr class="{{ $isNew ? 'bg-blue-50/60 border-l-2 border-l-blue-400' : '' }} hover:bg-gray-50/60 transition-colors">

        {{-- User --}}
        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center text-xs font-bold shrink-0">
                    {{ $log->user ? strtoupper(substr($log->user->name, 0, 2)) : 'SY' }}
                </div>
                <span class="text-sm font-medium text-gray-900">
                    {{ $log->user->name ?? 'System' }}
                </span>
            </div>
        </td>

        {{-- Action --}}
        <td class="px-6 py-4">
            @php
                $actionColors = [
                    'created' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'updated' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'deleted' => 'bg-red-50 text-red-700 border-red-200',
                    'toggled_status' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'logged_in' => 'bg-violet-50 text-violet-700 border-violet-200',
                    'logged_out' => 'bg-gray-50 text-gray-600 border-gray-200',
                ];
                $colorClass = $actionColors[$log->action] ?? 'bg-gray-50 text-gray-600 border-gray-200';
            @endphp
            <span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-lg border {{ $colorClass }}">
                {{ str_replace('_', ' ', $log->action) }}
            </span>
        </td>

        {{-- Description --}}
        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
            {{ $log->description }}
        </td>

        {{-- Subject --}}
        <td class="px-6 py-4 text-sm text-gray-500">
            @if($log->subject_type)
                <span class="text-xs text-gray-400">{{ class_basename($log->subject_type) }}</span>
                <span class="text-xs text-gray-300">#{{ $log->subject_id }}</span>
            @else
                <span class="text-xs text-gray-300">—</span>
            @endif
        </td>

        {{-- IP --}}
        <td class="px-6 py-4 text-sm text-gray-400 font-mono text-xs">
            {{ $log->ip_address ?? '—' }}
        </td>

        {{-- Date --}}
        <td class="px-6 py-4 text-sm text-gray-500">
            <span title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                {{ $log->created_at->diffForHumans() }}
            </span>
        </td>

    </tr>
@empty
    <tr>
        <td colspan="6" class="px-6 py-16 text-center text-gray-400 text-sm">
            No activity logs found.
        </td>
    </tr>
@endforelse
