@forelse($logs as $log)
    <tr class="hover:bg-gray-50/60 transition-colors">

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
        <td class="px-6 py-4 text-sm text-gray-600">
            {{ $log->description }}
        </td>

        {{-- By --}}
        <td class="px-6 py-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-gray-900 text-white flex items-center justify-center text-[10px] font-bold shrink-0">
                    {{ $log->user ? strtoupper(substr($log->user->name, 0, 2)) : 'SY' }}
                </div>
                <span class="text-sm text-gray-500">
                    {{ $log->user->name ?? 'System' }}
                </span>
            </div>
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
        <td colspan="4" class="px-6 py-16 text-center text-gray-400 text-sm">
            No activity yet. Your tour request updates will appear here.
        </td>
    </tr>
@endforelse
