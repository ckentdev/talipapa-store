@if (session('success') || session('error') || session('warning') || (isset($errors) && $errors->any()))
    <div class="space-y-3" data-flash-alerts>
        @if (session('success'))
            <x-alert type="success" title="Success">
                {{ session('success') }}
            </x-alert>
        @endif

        @if (session('error'))
            <x-alert type="error" title="Something went wrong">
                {{ session('error') }}
            </x-alert>
        @endif

        @if (session('warning'))
            <x-alert type="warning" title="Attention">
                {{ session('warning') }}
            </x-alert>
        @endif

        @if (isset($errors) && $errors->any())
            <x-alert type="error" title="Please fix the following">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif
    </div>
@endif
