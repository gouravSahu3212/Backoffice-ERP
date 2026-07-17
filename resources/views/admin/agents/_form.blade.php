@csrf

<div class="grid grid-cols-2 gap-6">

    <div>
        <label class="block mb-2 font-medium">
            Name
        </label>

        <input
            type="text"
            name="name"
            class="w-full border rounded-lg px-4 py-2"
            value="{{ old('name', $agent->name ?? '') }}">

        @error('name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>

        <label class="block mb-2 font-medium">
            Email
        </label>

        <input
            type="email"
            name="email"
            class="w-full border rounded-lg px-4 py-2"
            value="{{ old('email', $agent->email ?? '') }}">

        @error('email')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label class="block mb-2 font-medium">
            Phone
        </label>

        <input
            id="f-phone"
            type="text"
            name="phone"
            class="w-full border rounded-lg px-4 py-2"
            value="{{ old('phone', $agent->phone ?? '') }}">

        @error('phone')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
        <p id="f-phone-error" class="text-red-500 text-sm mt-1 hidden"></p>

    </div>

    <div>

        <label class="block mb-2 font-medium">
            Password
        </label>

        <input
            type="password"
            name="password"
            class="w-full border rounded-lg px-4 py-2">

        @if(!isset($agent))
            <small>Password must be at least 8 characters.</small>
        @else
            <small>Leave blank to keep the existing password.</small>
        @endif

    </div>

    <div>

        <label class="block mb-2 font-medium">
            Confirm Password
        </label>

        <input
            type="password"
            name="password_confirmation"
            class="w-full border rounded-lg px-4 py-2">

    </div>

</div>

<div class="mt-8">
    <button class="bg-gray-800 hover:bg-gray-700 text-white px-6 py-2 rounded">
        Save Agent
    </button>
</div>

@push('scripts')
<script>
(function () {
    const phoneInput = document.getElementById('f-phone');
    if (!phoneInput) return;

    const form = phoneInput.closest('form');
    if (!form) return;

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

    form.addEventListener('submit', function (e) {
        const phoneVal = phoneInput.value.trim();
        const errEl = document.getElementById('f-phone-error');
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
            phoneInput.focus();
        }
    });
})();
</script>
@endpush