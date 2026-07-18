@php
    $navigation = app(\App\Services\NavigationService::class);
    $navigationMenus = $navigation->adminTree(auth()->user());
@endphp

<aside id="sidebar"
    class="fixed left-0 top-0 z-99999 flex h-screen flex-col border-r px-4 backdrop-blur-xl transition-all duration-300 ease-in-out"
    style="background: color-mix(in srgb, var(--sidebar) 95%, transparent); border-color: var(--border); color: var(--sidebar-foreground);"
    :class="{
        'w-[290px]': $store.sidebar.isExpanded || $store.sidebar.isMobileOpen || $store.sidebar.isHovered,
        'w-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
        'translate-x-0': $store.sidebar.isMobileOpen,
        '-translate-x-full md:translate-x-0': !$store.sidebar.isMobileOpen
    }"
    @mouseenter="if (!$store.sidebar.isExpanded) $store.sidebar.setHovered(true)"
    @mouseleave="$store.sidebar.setHovered(false)">
    <div class="flex pb-7 pt-8" :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'md:justify-center' : 'justify-start'">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <span class="grid size-10 place-items-center overflow-hidden rounded-full">
                <img src="{{ asset('images/logo.png') }}" alt="NPNHCREATIVE" class="size-full object-contain">
            </span>
            <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="font-semibold tracking-tight">NPNHCREATIVE</span>
        </a>
    </div>

    <div class="flex flex-1 flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <nav class="mb-6">
            <div class="flex flex-col gap-4">
                @forelse($navigationMenus as $section)
                    <div>
                        <h2 class="mb-4 flex text-xs uppercase leading-[20px]" style="color: var(--muted-foreground);" :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'md:justify-center' : 'justify-start'">
                            <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="tracking-[0.22em]">{{ $navigation->title($section) }}</span>
                            <span x-show="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">{!! \App\Helpers\MenuHelper::getIconSvg($section->icon ?: 'pages') !!}</span>
                        </h2>
                        <ul class="flex flex-col gap-1">
                            @foreach($section->children as $item)
                                @include('layouts.partials.sidebar-menu-item', ['item' => $item])
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <p class="wx-panel rounded-2xl p-4 text-sm" style="color: var(--muted-foreground);">No navigation menus available.</p>
                @endforelse
            </div>
        </nav>

        <div x-data x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" x-transition class="mt-auto">
            @include('layouts.sidebar-widget')
        </div>
    </div>
</aside>

<div x-show="$store.sidebar.isMobileOpen" @click="$store.sidebar.setMobileOpen(false)" class="fixed z-50 h-screen w-full" style="background: color-mix(in srgb, var(--background) 70%, transparent);"></div>
