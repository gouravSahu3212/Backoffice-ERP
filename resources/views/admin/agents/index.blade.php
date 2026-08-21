@extends('layouts.dashboard')

@section('page-title', 'Agents')

@section('content')

{{-- Page header --}}
<div class="flex justify-between items-center mb-6">

    <h1 class="text-2xl font-bold text-gray-900">Agents</h1>

    <button
        id="open-create-agent-modal"
        type="button"
        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        Create Agent
    </button>

</div>

{{-- Agents table --}}
<div class="bg-white border border-gray-100 rounded-md shadow-sm overflow-hidden">

    <table class="w-full text-sm">

        <thead>
            <tr class="border-b border-gray-100">
                <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">Name</th>
                <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">Email</th>
                <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">Phone</th>
                <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">Status</th>
                <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">Created</th>
                <th class="text-right text-xs font-semibold text-gray-400 uppercase tracking-wider px-6 py-3">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-50" id="agents-table-body">

            @forelse($agents as $agent)

                <tr class="hover:bg-gray-50/60 transition-colors" id="agent-row-{{ $agent->id }}">

                    <td class="px-6 py-4 font-medium text-gray-900" data-field="name">
                        {{ $agent->name }}
                    </td>

                    <td class="px-6 py-4 text-blue-500" data-field="email">
                        {{ $agent->email }}
                    </td>

                    <td class="px-6 py-4 text-gray-600" data-field="phone">
                        {{ $agent->phone ?: '-' }}
                    </td>

                    <td class="px-6 py-4" data-field="status">
                        @if($agent->is_active)
                            <span class="inline-flex items-center bg-gray-900 text-white text-xs font-medium px-2.5 py-1 rounded-lg">
                                active
                            </span>
                        @else
                            <span class="inline-flex items-center bg-gray-100 text-gray-500 text-xs font-medium px-2.5 py-1 rounded-lg">
                                inactive
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-gray-500">
                        {{ $agent->created_at->format('Y-m-d') }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-3">

                            {{-- Edit (opens modal) --}}
                            <button
                                type="button"
                                title="Edit agent"
                                class="open-edit-modal p-1.5 text-gray-400 hover:text-gray-700 transition-colors"
                                data-id="{{ $agent->id }}"
                                data-name="{{ $agent->name }}"
                                data-username="{{ $agent->username }}"
                                data-email="{{ $agent->email }}"
                                data-phone="{{ $agent->phone }}"
                                data-is-active="{{ $agent->is_active ? '1' : '0' }}"
                                data-update-url="{{ route('admin.agents.update', $agent) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil h-4 w-4">
                                    <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"></path>
                                    <path d="m15 5 4 4"></path>
                                </svg>
                            </button>

                            {{-- Toggle Status (AJAX) --}}
                            <button
                                type="button"
                                title="{{ $agent->is_active ? 'Deactivate' : 'Activate' }}"
                                class="toggle-status-btn p-1.5 text-gray-400 hover:text-gray-700 transition-colors"
                                data-id="{{ $agent->id }}"
                                data-is-active="{{ $agent->is_active ? '1' : '0' }}"
                                data-toggle-url="{{ route('admin.agents.toggle-status', $agent) }}">
                                @if($agent->is_active)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-toggle-right h-4 w-4"><rect width="20" height="12" x="2" y="6" rx="6" ry="6"></rect><circle cx="16" cy="12" r="2"></circle></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-toggle-left h-4 w-4"><rect width="20" height="12" x="2" y="6" rx="6" ry="6"></rect><circle cx="8" cy="12" r="2"></circle></svg>
                                @endif
                            </button>

                            {{-- Delete --}}
                            <button
                                type="button"
                                title="Delete agent"
                                class="open-delete-modal p-1.5 text-gray-400 hover:text-red-600 transition-colors"
                                data-id="{{ $agent->id }}"
                                data-name="{{ $agent->name }}"
                                data-delete-url="{{ route('admin.agents.destroy', $agent) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18"></path>
                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                </svg>
                            </button>

                        </div>
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-400 text-sm">
                        No agents found.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

    @if($agents->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $agents->links() }}
        </div>
    @endif

</div>

{{-- ============================================================
     CREATE AGENT MODAL
     ============================================================ --}}
<div
    id="create-agent-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center"
    aria-modal="true"
    role="dialog">

    {{-- Backdrop --}}
    <div class="create-modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

    {{-- Panel --}}
    <div class="relative bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 p-7">

        {{-- Close --}}
        <button class="close-create-modal absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h2 class="text-xl font-bold text-gray-900 mb-6">Create Agent</h2>

        <form id="create-agent-form" method="POST" action="{{ route('admin.agents.store') }}" class="space-y-4">
            @csrf

            {{-- Full Name --}}
            <div>
                <label for="c-name" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input id="c-name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="c-email" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Email <span class="text-red-500">*</span>
                </label>
                <input id="c-email" type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label for="c-phone" class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                <input id="c-phone" type="text" name="phone" value="{{ old('phone') }}"
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Username --}}
            <div>
                <label for="c-username" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Username <span class="text-red-500">*</span>
                </label>
                <input id="c-username" type="text" name="username" value="{{ old('username') }}" required
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                @error('username')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Temporary Password --}}
            @php
                $defaultPassword = 'Tmp' . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT) . '!';
            @endphp
            <div>
                <label for="c-password" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Temporary Password <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input id="c-password" type="text" name="password" value="{{ old('password', $defaultPassword) }}" required
                            class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                    </div>
                    <button type="button" id="toggle-password-btn" title="Toggle password visibility"
                        class="px-3 py-2.5 border border-gray-200 rounded-lg hover:bg-gray-50 transition text-gray-500">
                        {{-- Eye open icon --}}
                        <svg id="eye-open-icon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{-- Eye closed icon --}}
                        <svg id="eye-closed-icon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                    <button type="button" id="generate-password-btn" title="Generate password"
                        class="px-3 py-2.5 border border-gray-200 rounded-lg hover:bg-gray-50 transition text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </button>
                </div>
                <input type="hidden" name="password_confirmation" id="c-password-confirm" value="{{ old('password', $defaultPassword) }}">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="c-status" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                <select id="c-status" name="is_active"
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" class="close-create-modal px-5 py-2.5 text-sm font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-700 rounded-lg transition">
                    Save
                </button>
            </div>

        </form>

    </div>

</div>

{{-- ============================================================
     EDIT AGENT MODAL (AJAX)
     ============================================================ --}}
<div
    id="edit-agent-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center"
    aria-modal="true"
    role="dialog">

    {{-- Backdrop --}}
    <div class="edit-modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

    {{-- Panel --}}
    <div class="relative bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 p-7">

        {{-- Close --}}
        <button class="close-edit-modal absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h2 class="text-xl font-bold text-gray-900 mb-6">Edit Agent</h2>

        {{-- Error banner --}}
        <div id="edit-error-banner" class="hidden mb-4 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-600">
        </div>

        {{-- Success banner --}}
        <div id="edit-success-banner" class="hidden mb-4 bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-sm text-green-700">
            Agent updated successfully.
        </div>

        <form id="edit-agent-form" class="space-y-4" novalidate>
            @csrf
            <input type="hidden" id="e-agent-id">

            {{-- Full Name --}}
            <div>
                <label for="e-name" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input id="e-name" type="text" name="name" required
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                <p class="edit-field-error hidden text-red-500 text-xs mt-1" data-for="name"></p>
            </div>

            {{-- Email --}}
            <div>
                <label for="e-email" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Email <span class="text-red-500">*</span>
                </label>
                <input id="e-email" type="email" name="email" required
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                <p class="edit-field-error hidden text-red-500 text-xs mt-1" data-for="email"></p>
            </div>

            {{-- Phone --}}
            <div>
                <label for="e-phone" class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                <input id="e-phone" type="text" name="phone"
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                <p class="edit-field-error hidden text-red-500 text-xs mt-1" data-for="phone" id="e-phone-error"></p>
            </div>

            {{-- Username --}}
            <div>
                <label for="e-username" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Username <span class="text-red-500">*</span>
                </label>
                <input id="e-username" type="text" name="username" required
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                <p class="edit-field-error hidden text-red-500 text-xs mt-1" data-for="username"></p>
            </div>

            {{-- Status --}}
            <div>
                <label for="e-status" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                <select id="e-status" name="is_active"
                    class="w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition bg-white">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" class="close-edit-modal px-5 py-2.5 text-sm font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" id="edit-save-btn" class="px-5 py-2.5 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-700 rounded-lg transition flex items-center gap-2">
                    Save
                </button>
            </div>

        </form>

    </div>

</div>

{{-- ============================================================
     DELETE AGENT MODAL
     ============================================================ --}}
<div id="delete-agent-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center"
    aria-modal="true" role="dialog">
    <div class="delete-modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-7">
        <h2 class="text-lg font-bold text-gray-900 mb-2">Delete Agent?</h2>
        <p class="text-sm text-gray-500 mb-6" id="delete-modal-msg">This action cannot be undone. The agent will be notified via email.</p>
        <div class="flex justify-end gap-3">
            <button type="button" class="close-delete-modal px-5 py-2.5 text-sm font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </button>
            <form id="delete-agent-form" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition flex items-center justify-center gap-2">
                    <svg class="delete-spinner w-4 h-4 text-white animate-spin hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="delete-text">Delete</span>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    // ── Helpers ────────────────────────────────────────────────────────
    function openModal(el)  { el.classList.remove('hidden'); el.classList.add('flex'); document.body.classList.add('overflow-hidden'); }
    function closeModal(el) { el.classList.add('hidden'); el.classList.remove('flex'); document.body.classList.remove('overflow-hidden'); }

    // ── CREATE modal ───────────────────────────────────────────────────
    const createModal  = document.getElementById('create-agent-modal');
    const createForm   = document.getElementById('create-agent-form');
    const pwdInput     = document.getElementById('c-password');
    const pwdConfirm   = document.getElementById('c-password-confirm');
    const genPwdBtn    = document.getElementById('generate-password-btn');
    const togglePwdBtn = document.getElementById('toggle-password-btn');
    const eyeOpen      = document.getElementById('eye-open-icon');
    const eyeClosed    = document.getElementById('eye-closed-icon');

    document.getElementById('open-create-agent-modal').addEventListener('click', () => openModal(createModal));
    document.querySelectorAll('.close-create-modal, .create-modal-backdrop').forEach(el =>
        el.addEventListener('click', () => closeModal(createModal))
    );

    // Toggle password visibility
    togglePwdBtn.addEventListener('click', function () {
        const isPassword = pwdInput.type === 'password';
        pwdInput.type = isPassword ? 'text' : 'password';
        eyeOpen.classList.toggle('hidden', !isPassword);
        eyeClosed.classList.toggle('hidden', isPassword);
    });

    genPwdBtn.addEventListener('click', function () {
        const chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$';
        let pwd = '';
        for (let i = 0; i < 12; i++) pwd += chars.charAt(Math.floor(Math.random() * chars.length));
        pwdInput.type  = 'text';
        pwdInput.value = pwd;
        pwdConfirm.value = pwd;
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    });

    // Sync confirmation on manual password edits
    pwdInput.addEventListener('input', function () {
        pwdConfirm.value = pwdInput.value;
    });

    @if($errors->any())
        openModal(createModal);
    @endif

    // ── EDIT modal ─────────────────────────────────────────────────────
    const editModal      = document.getElementById('edit-agent-modal');
    const editForm       = document.getElementById('edit-agent-form');
    const editAgentId    = document.getElementById('e-agent-id');
    const editName       = document.getElementById('e-name');
    const editEmail      = document.getElementById('e-email');
    const editPhone      = document.getElementById('e-phone');
    const editUsername   = document.getElementById('e-username');
    const editStatus     = document.getElementById('e-status');
    const editSaveBtn    = document.getElementById('edit-save-btn');
    const editErrBanner  = document.getElementById('edit-error-banner');
    const editOkBanner   = document.getElementById('edit-success-banner');

    document.querySelectorAll('.close-edit-modal, .edit-modal-backdrop').forEach(el =>
        el.addEventListener('click', () => closeModal(editModal))
    );

    // Open & pre-fill edit modal
    document.querySelectorAll('.open-edit-modal').forEach(function (btn) {
        btn.addEventListener('click', function () {
            // Reset banners & field errors
            editErrBanner.classList.add('hidden');
            editOkBanner.classList.add('hidden');
            document.querySelectorAll('.edit-field-error').forEach(el => { el.textContent = ''; el.classList.add('hidden'); });

            // Populate fields from data-* attrs
            editAgentId.value  = this.dataset.id;
            editName.value     = this.dataset.name     || '';
            editEmail.value    = this.dataset.email    || '';
            editPhone.value    = this.dataset.phone    || '';
            editUsername.value = this.dataset.username || '';
            editStatus.value   = this.dataset.isActive || '1';

            // Store url on form for submit handler
            editForm.dataset.url = this.dataset.updateUrl;

            openModal(editModal);
            editName.focus();
        });
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModal(createModal);
            closeModal(editModal);
        }
    });

    // ── TOGGLE STATUS (AJAX) ───────────────────────────────────────────
    const TOGGLE_RIGHT_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-toggle-right h-4 w-4"><rect width="20" height="12" x="2" y="6" rx="6" ry="6"></rect><circle cx="16" cy="12" r="2"></circle></svg>';
    const TOGGLE_LEFT_SVG  = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-toggle-left h-4 w-4"><rect width="20" height="12" x="2" y="6" rx="6" ry="6"></rect><circle cx="8" cy="12" r="2"></circle></svg>';
    const SPINNER_SVG      = '<svg class="animate-spin h-4 w-4 text-gray-500 toggle-spinner" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><radialGradient id="a18" cx=".66" fx=".66" cy=".3125" fy=".3125" gradientTransform="scale(1.5)"><stop offset="0" stop-color="currentColor"></stop><stop offset=".3" stop-color="currentColor" stop-opacity=".9"></stop><stop offset=".6" stop-color="currentColor" stop-opacity=".6"></stop><stop offset=".8" stop-color="currentColor" stop-opacity=".3"></stop><stop offset="1" stop-color="currentColor" stop-opacity="0"></stop></radialGradient><circle transform-origin="center" fill="none" stroke="url(#a18)" stroke-width="15" stroke-linecap="round" stroke-dasharray="200 1000" stroke-dashoffset="0" cx="100" cy="100" r="70"><animateTransform type="rotate" attributeName="transform" calcMode="spline" dur="2" values="360;0" keyTimes="0;1" keySplines="0 0 1 1" repeatCount="indefinite"></animateTransform></circle><circle transform-origin="center" fill="none" opacity=".2" stroke="currentColor" stroke-width="15" stroke-linecap="round" cx="100" cy="100" r="70"></circle></svg>';

    document.querySelectorAll('.toggle-status-btn').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const url     = btn.dataset.toggleUrl;
            const agentId = btn.dataset.id;
            // Remember current icon so we can restore it on failure
            const previousIcon = btn.innerHTML;
            btn.disabled  = true;
            btn.innerHTML = SPINNER_SVG;

            try {
                const res  = await fetch(url, {
                    method:  'POST',
                    headers: {
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: '_method=PATCH',
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    const isActive = data.is_active;

                    // Update data attr
                    btn.dataset.isActive = isActive ? '1' : '0';
                    btn.title = isActive ? 'Deactivate' : 'Activate';

                    // Swap to correct icon
                    btn.innerHTML = isActive ? TOGGLE_RIGHT_SVG : TOGGLE_LEFT_SVG;

                    // Update status badge in the same row
                    const row        = document.getElementById('agent-row-' + agentId);
                    const statusCell = row ? row.querySelector('[data-field="status"]') : null;
                    if (statusCell) {
                        statusCell.innerHTML = isActive
                            ? '<span class="inline-flex items-center bg-gray-900 text-white text-xs font-medium px-2.5 py-1 rounded-lg">active</span>'
                            : '<span class="inline-flex items-center bg-gray-100 text-gray-500 text-xs font-medium px-2.5 py-1 rounded-lg">inactive</span>';
                    }

                    // Sync edit-button data-is-active so modal opens correctly
                    const editBtn = row ? row.querySelector('.open-edit-modal') : null;
                    if (editBtn) editBtn.dataset.isActive = isActive ? '1' : '0';
                } else {
                    // Restore previous icon on failure
                    btn.innerHTML = previousIcon;
                }
            } catch (err) {
                console.error('Toggle failed', err);
                btn.innerHTML = previousIcon;
            } finally {
                btn.disabled = false;
            }
        });
    });

    // AJAX submit
    editForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Clear previous errors
        editErrBanner.classList.add('hidden');
        editOkBanner.classList.add('hidden');
        document.querySelectorAll('.edit-field-error').forEach(el => { el.textContent = ''; el.classList.add('hidden'); });

        // Frontend Phone validation
        const phoneVal = editPhone.value.trim();
        if (!validatePhoneNumber(phoneVal)) {
            const errEl = document.getElementById('e-phone-error');
            if (errEl) {
                errEl.textContent = 'The phone must be a valid phone number for Saudi, Jordon, Morocco, Egypt, Turkey, or UAE.';
                errEl.classList.remove('hidden');
            }
            editPhone.focus();
            return;
        }

        editSaveBtn.disabled = true;
        editSaveBtn.textContent = 'Saving…';

        const url  = editForm.dataset.url;
        const body = new FormData(editForm);
        body.append('_method', 'PUT');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body,
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Show success
                editOkBanner.classList.remove('hidden');

                // Update the row in the table without a page reload
                const agentId = editAgentId.value;
                const row = document.getElementById('agent-row-' + agentId);
                if (row) {
                    row.querySelector('[data-field="name"]').textContent  = data.agent.name;
                    row.querySelector('[data-field="email"]').textContent = data.agent.email;
                    row.querySelector('[data-field="phone"]').textContent = data.agent.phone || '-';

                    const statusCell = row.querySelector('[data-field="status"]');
                    if (data.agent.is_active) {
                        statusCell.innerHTML = '<span class="inline-flex items-center bg-gray-900 text-white text-xs font-medium px-2.5 py-1 rounded-lg">active</span>';
                    } else {
                        statusCell.innerHTML = '<span class="inline-flex items-center bg-gray-100 text-gray-500 text-xs font-medium px-2.5 py-1 rounded-lg">inactive</span>';
                    }

                    // Sync the edit button data attrs for next open
                    const editBtn = row.querySelector('.open-edit-modal');
                    if (editBtn) {
                        editBtn.dataset.name      = data.agent.name;
                        editBtn.dataset.username  = data.agent.username;
                        editBtn.dataset.email     = data.agent.email;
                        editBtn.dataset.phone     = data.agent.phone || '';
                        editBtn.dataset.isActive  = data.agent.is_active ? '1' : '0';
                    }
                }

                // Close after short delay
                setTimeout(() => closeModal(editModal), 900);

            } else if (response.status === 422 && data.errors) {
                // Show field-level validation errors
                Object.entries(data.errors).forEach(([field, messages]) => {
                    const errEl = editForm.querySelector('.edit-field-error[data-for="' + field + '"]');
                    if (errEl) {
                        errEl.textContent = messages[0];
                        errEl.classList.remove('hidden');
                    } else {
                        editErrBanner.textContent = messages[0];
                        editErrBanner.classList.remove('hidden');
                    }
                });
            } else {
                editErrBanner.textContent = data.message || 'Something went wrong. Please try again.';
                editErrBanner.classList.remove('hidden');
            }
        } catch (err) {
            editErrBanner.textContent = 'Network error. Please try again.';
            editErrBanner.classList.remove('hidden');
        } finally {
            editSaveBtn.disabled = false;
            editSaveBtn.textContent = 'Save';
        }
    });

    // ── DELETE modal ────────────────────────────────────────────────────
    const deleteModal = document.getElementById('delete-agent-modal');
    const deleteForm  = document.getElementById('delete-agent-form');
    const deleteMsg   = document.getElementById('delete-modal-msg');

    document.querySelectorAll('.open-delete-modal').forEach(function (btn) {
        btn.addEventListener('click', function () {
            deleteForm.action = this.dataset.deleteUrl;
            deleteMsg.textContent = 'Delete "' + this.dataset.name + '"? This cannot be undone. The agent will be notified via email.';
            openModal(deleteModal);
        });
    });

    document.querySelectorAll('.close-delete-modal, .delete-modal-backdrop').forEach(el =>
        el.addEventListener('click', () => closeModal(deleteModal))
    );

    // Handle delete form submission with loader
    deleteForm.addEventListener('submit', function (e) {
        const btn = deleteForm.querySelector('button[type="submit"]');
        const spinner = btn.querySelector('.delete-spinner');
        const text = btn.querySelector('.delete-text');

        if (spinner) {
            spinner.classList.remove('hidden');
        }
        if (text) {
            text.textContent = 'Deleting...';
        }
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');

        const cancelBtn = deleteModal.querySelector('.close-delete-modal');
        if (cancelBtn) {
            cancelBtn.disabled = true;
            cancelBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }

        e.preventDefault();
        deleteForm.submit();
    });

    // ── KEYBOARD ESC ───────────────────────────────────────────────────
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModal(createModal);
            closeModal(editModal);
            closeModal(deleteModal);
        }
    });
})();
</script>
@endpush

@endsection