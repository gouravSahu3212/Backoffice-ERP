@props([
    'name' => 'file',
    'label' => 'Upload file',
    'accept' => null,
    'maxSize' => 10,
    'multiple' => false,
    'maxFiles' => null,
    'description' => null,
    'existingFiles' => [],
])

@php
    $fieldName = str_contains($name, '[]') ? str_replace('[]', '', $name) : $name;
    $uploaderId = $attributes->get('id') ?? 'file-upload-' . uniqid();
    $attributes = $attributes->except('id');
    $existingFileData = collect($existingFiles)->map(function ($file) {
        return [
            'path' => $file,
            'name' => basename($file),
            'url' => \Illuminate\Support\Facades\Storage::url($file),
        ];
    })->all();
@endphp

<div id="{{ $uploaderId }}" x-data="fileUploader({ multiple: {{ $multiple ? 'true' : 'false' }}, maxSize: {{ $maxSize }}, maxFiles: @json($maxFiles), existingFiles: @json($existingFileData) })"
    x-init="init()" {{ $attributes->merge(['class' => 'mt-4']) }}>
    <div class="mb-2 text-sm font-medium text-gray-700">{{ $label }}</div>

    <div @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
        @drop.prevent="dropFile($event)"
        :class="dragging ? 'border-gray-900 bg-gray-50 shadow-sm' : 'border-gray-200 bg-white'"
        class="relative rounded-3xl border border-gray-200 p-5 transition duration-200 hover:border-gray-300">

        <input x-ref="input" type="file" name="{{ $name }}"
            @if ($accept) accept="{{ $accept }}" @endif @if ($multiple) multiple @endif
            class="sr-only"
            @change="selectFile($event)">

        <div class="flex items-center gap-4">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-2xl border border-gray-200 bg-gray-50 text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                    <path d="M12 3v13"></path>
                    <path d="M8 9l4-4 4 4"></path>
                    <path d="M4 21h16"></path>
                </svg>
            </div>
            <div class="flex-1 space-y-1">
                <p class="font-semibold text-sm text-gray-900" x-text="labelText"></p>
                <p class="text-xs text-gray-500" x-text="helpText"></p>
            </div>
            <button type="button" @click="openFileBrowser()"
                class="rounded-full border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                {{ $multiple ? 'Add files' : 'Choose file' }}
            </button>
        </div>
        <p class="text-xs text-gray-500">Drag & drop files here or use the button to add them one by one.</p>

        <template x-if="existingItems.length">
            <div class="mt-4 rounded-3xl border border-gray-200 bg-gray-50 p-4">
                <div class="flex items-center justify-between text-xs font-medium text-gray-600">
                    <span x-text="existingItems.length + (existingItems.length === 1 ? ' existing file' : ' existing files')"></span>
                    <button type="button" @click.stop.prevent="removeAllExisting()"
                        class="rounded-full border border-gray-300 bg-white px-3 py-1 text-xs text-gray-700 hover:bg-gray-100">
                        Clear
                    </button>
                </div>
                <ul class="mt-3 grid grid-cols-1 gap-2 text-sm text-gray-700">
                    <template x-for="(item, index) in existingItems" :key="'existing-' + index">
                        <li class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-3 py-2">
                            <div class="flex items-center gap-3">
                                <template x-if="item.name.toLowerCase().endsWith('.pdf')">
                                    <a :href="item.url" target="_blank" rel="noopener noreferrer"
                                        @click.stop
                                        class="flex items-center gap-3 rounded-xl border border-gray-200 bg-red-50 px-3 py-2 hover:bg-red-100 transition">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-red-200 bg-red-100 text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="h-5 w-5">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <path d="M14 2v6h6"></path>
                                                <path d="M16 13h-4"></path>
                                                <path d="M16 17h-4"></path>
                                                <path d="M10 9h1"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            {{-- <p class="truncate font-medium text-gray-900" x-text="item.name"></p> --}}
                                            <p class="text-xs text-gray-500">Open PDF</p>
                                        </div>
                                    </a>
                                </template>
                                <template x-if="!item.name.toLowerCase().endsWith('.pdf')">
                                    <template x-if="item.url">
                                        <img :src="item.url"
                                            class="h-12 w-12 rounded-xl object-cover border border-gray-200" />
                                    </template>
                                    <span class="truncate" x-text="item.name"></span>
                                </template>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click.stop.prevent="removeExisting(index)"
                                    class="text-red-600 hover:text-red-700 text-xs font-medium">
                                    Remove
                                </button>
                                <input type="hidden" :name="'{{ $name }}'" :value="item.path">
                            </div>
                        </li>
                    </template>
                </ul>
            </div>
        </template>

        <template x-if="files.length">
            <div class="mt-4 rounded-3xl border border-gray-200 bg-gray-50 p-4">
                <div class="flex items-center justify-between text-xs font-medium text-gray-600">
                    <span x-text="files.length + (multiple ? ' new files selected' : ' new file selected')"></span>
                    <button type="button" @click.stop.prevent="removeAll()"
                        class="rounded-full border border-gray-300 bg-white px-3 py-1 text-xs text-gray-700 hover:bg-gray-100">
                        Clear
                    </button>
                </div>
                <ul class="mt-3 space-y-2 text-sm text-gray-700">
                    <template x-for="(file, index) in files" :key="index">
                        <li class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-3 py-2">
                            <span class="truncate" x-text="file.name"></span>
                            <span class="text-xs text-gray-500" x-text="formatSize(file.size)"></span>
                        </li>
                    </template>
                </ul>
            </div>
        </template>

        <template x-if="error">
            <p class="mt-4 text-sm text-red-600" x-text="error"></p>
        </template>
    </div>

    @if ($description)
        <p class="mt-2 text-xs text-gray-500">{{ $description }}</p>
    @endif

    @error($fieldName)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @error($fieldName . '.*')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<script>
    function fileUploader({ multiple = false, maxSize = 10, maxFiles = null, existingFiles = [] }) {
        return {
            dragging: false,
            files: [],
            existingItems: existingFiles,
            error: '',
            labelText: multiple ? (maxFiles ? `Click or drop up to ${maxFiles} files to upload` : 'Click or drop files to upload') : 'Click or drop a file to upload',
            helpText: `Allowed type${multiple ? 's' : ''}. Max ${maxSize} MB each.${multiple && maxFiles ? ' Up to ' + maxFiles + ' files.' : ''}`,
            multiple,
            maxSize,
            maxFiles,
            init() {
                if (this.$el.id) {
                    window.fileUploaderComponents = window.fileUploaderComponents || {};
                    window.fileUploaderComponents[this.$el.id] = this;
                }
            },
            openFileBrowser() {
                this.$refs.input.click();
            },
            selectFile(event) {
                const selectedFiles = Array.from(event.target.files || []);
                if (!selectedFiles.length) {
                    return;
                }

                this.validate(selectedFiles);
                this.syncInputFiles();
            },
            dropFile(event) {
                this.dragging = false;
                const droppedFiles = Array.from(event.dataTransfer.files || []);
                if (!droppedFiles.length) {
                    return;
                }

                this.validate(droppedFiles);
                this.syncInputFiles();
            },
            validate(files) {
                this.error = '';

                if (!files.length) {
                    return;
                }

                if (!this.multiple && files.length > 1) {
                    this.error = 'Please select only one file.';
                    return;
                }

                if (this.multiple && this.maxFiles && this.files.length + this.existingItems.length + files.length > this.maxFiles) {
                    this.error = `Please select no more than ${this.maxFiles} files.`;
                    return;
                }

                const invalidFile = files.find(file => file.size > this.maxSize * 1024 * 1024);
                if (invalidFile) {
                    this.error = `Each file must be ${this.maxSize} MB or smaller.`;
                    return;
                }

                if (this.multiple) {
                    this.files = [...this.files, ...files];
                } else {
                    this.files = files;
                }
            },
            syncInputFiles() {
                if (!this.$refs.input) {
                    return;
                }

                const dataTransfer = new DataTransfer();
                this.files.forEach(file => dataTransfer.items.add(file));
                this.$refs.input.files = dataTransfer.files;
            },
            removeAll() {
                this.files = [];
                this.error = '';
                if (this.$refs.input) {
                    this.$refs.input.value = '';
                }
            },
            removeFile(index) {
                this.files.splice(index, 1);
                this.syncInputFiles();
            },
            removeExisting(index) {
                this.existingItems.splice(index, 1);
            },
            removeAllExisting() {
                this.existingItems = [];
            },
            setExistingFiles(existingFiles) {
                this.existingItems = existingFiles;
            },
            formatSize(bytes) {
                if (bytes < 1024) return `${bytes} B`;
                if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
                return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
            },
        };
    }
</script>
