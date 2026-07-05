@if(session('success'))

<div class="mb-5 rounded bg-green-100 border border-green-300 p-4 text-green-700">

    {{ session('success') }}

</div>

@endif

@if(session('error'))

<div class="mb-5 rounded bg-red-100 border border-red-300 p-4 text-red-700">

    {{ session('error') }}

</div>

@endif