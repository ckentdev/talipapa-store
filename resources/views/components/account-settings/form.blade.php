@props([
    'submit',
    'formId',
])

<form id="{{ $formId }}" wire:submit="{{ $submit }}">
    {{ $slot }}
</form>
