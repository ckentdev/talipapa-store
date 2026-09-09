@props([
    'name',
    'id' => null,
    'accept' => null,
    'required' => false,
    'hint' => null,
    'placeholder' => 'No file chosen',
    'buttonText' => 'Browse files',
    'preview' => false,
])

@php
    $inputId = $id ?? 'file-' . preg_replace('/[^a-zA-Z0-9_-]/', '-', $name);
@endphp

<div data-file-input {{ $attributes->merge(['class' => 'w-full']) }}>
    <input
        type="file"
        id="{{ $inputId }}"
        name="{{ $name }}"
        @if ($accept) accept="{{ $accept }}" @endif
        @required($required)
        class="sr-only"
    >

    <label
        for="{{ $inputId }}"
        data-file-dropzone
        class="group flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50/60 px-4 py-4 text-center transition hover:border-forest-300 hover:bg-avocado-50/40 focus-within:border-forest-400 focus-within:ring-2 focus-within:ring-forest-100"
    >
        @if ($preview)
            <img
                data-file-preview
                src=""
                alt=""
                class="hidden max-h-24 w-auto rounded-lg border border-gray-200 object-contain"
            >
        @endif

        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-forest-100 text-forest-600 transition group-hover:bg-forest-600 group-hover:text-white">
            <i class="ri-upload-cloud-2-line text-xl" aria-hidden="true"></i>
        </span>

        <span class="text-base text-gray-700">
            <span class="font-semibold text-forest-700">{{ $buttonText }}</span>
            <span class="text-gray-500"> or drag and drop</span>
        </span>

        @if ($hint)
            <span class="text-base text-gray-500">{{ $hint }}</span>
        @endif

        <span data-file-name data-default="{{ $placeholder }}" class="max-w-full truncate text-base font-medium text-gray-600">{{ $placeholder }}</span>
    </label>
</div>
