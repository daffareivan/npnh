@props(['code' => 500, 'icon' => 'file-warning', 'tone' => 'warning'])

@php
    $title = __("errors.{$code}.title");
    $description = __("errors.{$code}.description");
    $requestId = request()->header('X-Request-Id');
    $user = auth()->user();
@endphp

<x-error-layout :code="$code" :title="$title">
    <x-error-card :code="$code">
        <span class="wx-pill inline-flex rounded-full px-4 py-1.5 text-xs font-medium uppercase tracking-[0.18em] text-muted-foreground">
            {{ __('errors.badge') }} {{ $code }}
        </span>

        <div class="mt-6 flex justify-center">
            <x-error-icon :name="$icon" :tone="$tone" />
        </div>

        <p class="mt-6 text-6xl font-semibold tracking-tight text-foreground sm:text-7xl" aria-hidden="true">{{ $code }}</p>
        <h1 id="error-title" class="mt-3 text-2xl font-semibold tracking-tight text-foreground sm:text-3xl">{{ $title }}</h1>
        <p class="mx-auto mt-4 max-w-sm text-sm leading-7 text-muted-foreground">{{ $description }}</p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <x-error-button href="{{ route('home') }}" variant="primary">
                {{ __('errors.actions.back_home') }}
            </x-error-button>

            <x-error-button variant="secondary" onclick="window.history.back()">
                {{ __('errors.actions.go_back') }}
            </x-error-button>

            @if($user)
                <x-error-button href="{{ route('app.dashboard') }}" variant="secondary">
                    {{ __('errors.actions.dashboard') }}
                </x-error-button>

                @if($user->can('admin.access'))
                    <x-error-button href="{{ route('admin.dashboard.show') }}" variant="secondary">
                        {{ __('errors.actions.admin_dashboard') }}
                    </x-error-button>
                @endif
            @endif
        </div>

        <div class="mt-8 space-y-1 border-t border-border pt-6 text-xs text-muted-foreground">
            <p>{{ __('errors.extra.timestamp') }}: {{ now()->toDayDateTimeString() }}</p>
            @if($requestId)
                <p>{{ __('errors.extra.request_id') }}: {{ $requestId }}</p>
            @endif
            @unless(app()->environment('production'))
                <p>{{ __('errors.extra.environment') }}: {{ app()->environment() }}</p>
            @endunless
        </div>

        <p class="mt-6 text-xs text-muted-foreground">
            &copy; {{ date('Y') }} {{ config('app.name') }} &middot; {{ __('errors.version') }} {{ config('app.version') }}
        </p>
    </x-error-card>
</x-error-layout>
