@props([
    'name',
    'class' => '',
])

<i {{ $attributes->merge(['class' => trim($name.' '.$class)]) }} aria-hidden="true"></i>
