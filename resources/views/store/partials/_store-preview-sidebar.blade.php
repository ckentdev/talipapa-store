@php
    use App\Enums\ApprovalStatus;
@endphp

<div class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
    <div class="aspect-[4/3] overflow-hidden bg-gray-100">
        @if ($store && filled($store->cover_path))
            <x-img
                :src="$store->coverUrl()"
                :alt="$store->store_name"
                type="store_cover"
                class="h-full w-full object-cover"
            />
        @else
            <div class="flex h-full items-center justify-center bg-gradient-to-br from-avocado-50 to-forest-50 text-forest-400">
                <i class="ri-store-2-line text-4xl" aria-hidden="true"></i>
            </div>
        @endif
    </div>

    <div class="relative border-t border-gray-200 px-4 pb-4 pt-10">
        <div class="absolute -top-8 left-4 flex h-16 w-16 items-center justify-center overflow-hidden rounded-xl bg-white shadow-sm ring-2 ring-white">
            @if ($store && filled($store->logo_path))
                <x-img
                    :src="$store->logoUrl()"
                    :alt="$store->store_name"
                    type="store_logo"
                    class="h-full w-full object-cover"
                />
            @else
                <span class="text-xl font-bold text-forest-600">
                    {{ strtoupper(substr($store?->store_name ?? $user->name, 0, 1)) }}
                </span>
            @endif
        </div>

        <p class="truncate text-base font-bold text-gray-900">{{ $store?->store_name ?? 'Your store' }}</p>
        <p class="mt-0.5 truncate text-base text-gray-500">{{ $user->email }}</p>

        @if ($store)
            <div class="mt-2 flex flex-wrap items-center gap-2">
                <span @class(['inline-flex rounded-full px-2.5 py-0.5 text-base font-semibold capitalize', $statusClasses])>
                    {{ $store->status->value }}
                </span>
                @if ($store->status === ApprovalStatus::Approved)
                    <a
                        href="{{ route('stores.show', $store) }}"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex items-center gap-1 text-base font-medium text-brand-600 hover:text-brand-700"
                    >
                        <i class="ri-external-link-line" aria-hidden="true"></i>
                        Public page
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

@if ($address)
    <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 bg-white">
        <div class="flex items-center gap-2 border-b border-gray-100 bg-gray-50/80 px-3 py-2.5">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-forest-600/10 text-forest-600">
                <i class="ri-map-pin-2-line text-base" aria-hidden="true"></i>
            </span>
            <h3 class="text-base font-bold text-gray-900">Location</h3>
        </div>
        <div class="space-y-2 p-3 text-base text-gray-600">
            <p>{{ $address->fullAddress() }}</p>
            @if ($address->landmark)
                <p class="flex items-start gap-1.5 text-gray-500">
                    <i class="ri-signpost-line mt-0.5 shrink-0" aria-hidden="true"></i>
                    {{ $address->landmark }}
                </p>
            @endif
        </div>
    </div>
@endif

@if ($requirements ?? null)
    <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 bg-white">
        <div class="flex items-center gap-2 border-b border-gray-100 bg-gray-50/80 px-3 py-2.5">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-forest-600/10 text-forest-600">
                <i class="ri-file-upload-line text-base" aria-hidden="true"></i>
            </span>
            <h3 class="text-base font-bold text-gray-900">Documents</h3>
        </div>
        <dl class="space-y-3 p-3 text-base">
            <div>
                <dt class="font-medium text-gray-500">Business permit</dt>
                <dd class="mt-0.5">
                    @if (filled($requirements->business_permit_path))
                        <span class="inline-flex items-center gap-1 font-medium text-forest-700">
                            <i class="ri-check-line" aria-hidden="true"></i>
                            Uploaded
                        </span>
                    @else
                        <span class="text-gray-400">Not uploaded</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Valid ID</dt>
                <dd class="mt-0.5">
                    @if (filled($requirements->valid_id_path))
                        <span class="inline-flex items-center gap-1 font-medium text-forest-700">
                            <i class="ri-check-line" aria-hidden="true"></i>
                            Uploaded
                        </span>
                    @else
                        <span class="text-gray-400">Not uploaded</span>
                    @endif
                </dd>
            </div>
            @if ($requirements->notes)
                <div>
                    <dt class="font-medium text-gray-500">Notes</dt>
                    <dd class="mt-0.5 rounded-lg bg-avocado-50/60 px-3 py-2 text-gray-700">{{ $requirements->notes }}</dd>
                </div>
            @endif
        </dl>
    </div>
@endif
