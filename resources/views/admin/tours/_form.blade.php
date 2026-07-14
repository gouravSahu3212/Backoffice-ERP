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
            type="text"
            name="phone"
            class="w-full border rounded-lg px-4 py-2"
            value="{{ old('phone', $agent->phone ?? '') }}">

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