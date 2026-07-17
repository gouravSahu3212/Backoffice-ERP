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
<div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">

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
                            <span class="inline-flex items-center bg-gray-900 text-white text-xs font-medium px-2.5 py-1 rounded-full">
                                active
                            </span>
                        @else
                            <span class="inline-flex items-center bg-gray-100 text-gray-500 text-xs font-medium px-2.5 py-1 rounded-full">
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
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-7">

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
                <p id="c-phone-error" class="text-red-500 text-xs mt-1 hidden"></p>
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
            <div>
                <label for="c-password" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Temporary Password <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2">
                    <input id="c-password" type="password" name="password" required
                        class="flex-1 border border-gray-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition">
                    <button type="button" id="generate-password-btn" title="Generate password"
                        class="px-3 py-2.5 border border-gray-200 rounded-lg hover:bg-gray-50 transition text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </button>
                </div>
                <input type="hidden" name="password_confirmation" id="c-password-confirm">
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
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-7">

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

@push('scripts')
<script>
(function () {
    // ── Helpers ────────────────────────────────────────────────────────
    function openModal(el)  { el.classList.remove('hidden'); el.classList.add('flex'); document.body.classList.add('overflow-hidden'); }
    function closeModal(el) { el.classList.add('hidden'); el.classList.remove('flex'); document.body.classList.remove('overflow-hidden'); }

    function validatePhoneNumber(value) {
        if (!value) {
            return true;
        }
        const normalized = value.replace(/[\s\-\(\)]+/g, '');
        const patterns = {
            Saudi:   /^(?:\+?966|0)?5\d{8}$/,
            Jordon:  /^(?:\+?962|0)?7[789]\d{7}$/,
            Morocco: /^(?:\+?212|0)?[67]\d{8}$/,
            Egypt:   /^(?:\+?20|0)?1[0125]\d{8}$/,
            Turkey:  /^(?:\+?90|0)?5\d{9}$/,
            UAE:     /^(?:\+?971|0)?5[024568]\d{7}$/
        };
        for (const country in patterns) {
            if (patterns[country].test(normalized)) {
                return true;
            }
        }
        return false;
    }

    // ── CREATE modal ───────────────────────────────────────────────────
    const createModal  = document.getElementById('create-agent-modal');
    const createForm   = document.getElementById('create-agent-form');
    const pwdInput     = document.getElementById('c-password');
    const pwdConfirm   = document.getElementById('c-password-confirm');
    const genPwdBtn    = document.getElementById('generate-password-btn');

    document.getElementById('open-create-agent-modal').addEventListener('click', () => openModal(createModal));
    document.querySelectorAll('.close-create-modal, .create-modal-backdrop').forEach(el =>
        el.addEventListener('click', () => closeModal(createModal))
    );

    genPwdBtn.addEventListener('click', function () {
        const chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$';
        let pwd = '';
        for (let i = 0; i < 12; i++) pwd += chars.charAt(Math.floor(Math.random() * chars.length));
        pwdInput.type  = 'text';
        pwdInput.value = pwd;
        pwdConfirm.value = pwd;
    });

    @if($errors->any())
        openModal(createModal);
    @endif

    createForm.addEventListener('submit', function (e) {
        const phoneVal = document.getElementById('c-phone').value.trim();
        const errEl = document.getElementById('c-phone-error');
        if (errEl) {
            errEl.textContent = '';
            errEl.classList.add('hidden');
        }

        if (!validatePhoneNumber(phoneVal)) {
            e.preventDefault();
            if (errEl) {
                errEl.textContent = 'The phone must be a valid phone number for Saudi, Jordon, Morocco, Egypt, Turkey, or UAE.';
                errEl.classList.remove('hidden');
            }
            document.getElementById('c-phone').focus();
        }
    });

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

    document.querySelectorAll('.toggle-status-btn').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const url     = btn.dataset.toggleUrl;
            const agentId = btn.dataset.id;
            btn.disabled  = true;

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

                    // Swap icon
                    btn.innerHTML = isActive ? TOGGLE_RIGHT_SVG : TOGGLE_LEFT_SVG;

                    // Update status badge in the same row
                    const row        = document.getElementById('agent-row-' + agentId);
                    const statusCell = row ? row.querySelector('[data-field="status"]') : null;
                    if (statusCell) {
                        statusCell.innerHTML = isActive
                            ? '<span class="inline-flex items-center bg-gray-900 text-white text-xs font-medium px-2.5 py-1 rounded-full">active</span>'
                            : '<span class="inline-flex items-center bg-gray-100 text-gray-500 text-xs font-medium px-2.5 py-1 rounded-full">inactive</span>';
                    }

                    // Sync edit-button data-is-active so modal opens correctly
                    const editBtn = row ? row.querySelector('.open-edit-modal') : null;
                    if (editBtn) editBtn.dataset.isActive = isActive ? '1' : '0';
                }
            } catch (err) {
                console.error('Toggle failed', err);
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
                        statusCell.innerHTML = '<span class="inline-flex items-center bg-gray-900 text-white text-xs font-medium px-2.5 py-1 rounded-full">active</span>';
                    } else {
                        statusCell.innerHTML = '<span class="inline-flex items-center bg-gray-100 text-gray-500 text-xs font-medium px-2.5 py-1 rounded-full">inactive</span>';
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
})();
</script>
@endpush

@endsection