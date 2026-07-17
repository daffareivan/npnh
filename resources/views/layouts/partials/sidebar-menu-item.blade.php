@php
    $navigation = app(\App\Services\NavigationService::class);
    $hasChildren = $item->children->isNotEmpty();
    $active = $navigation->isActive($item);
    $href = $navigation->href($item);
@endphp

<li x-data="{ open: {{ $active ? 'true' : 'false' }} }">
    @if($hasChildren)
        <button
            type="button"
            @click="open = !open"
            class="menu-item group w-full"
            :class="open ? 'menu-item-active' : 'menu-item-inactive'"
        >
            <span :class="open ? 'menu-item-icon-active' : 'menu-item-icon-inactive'">{!! \App\Helpers\MenuHelper::getIconSvg($item->icon ?: 'pages') !!}</span>
            <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text flex items-center gap-2">
                {{ $navigation->title($item) }}
                @if($item->badge)
                    <span class="wx-pill ml-auto rounded-full px-2 py-0.5 text-[11px]">{{ $item->badge }}</span>
                @endif
            </span>
            <svg x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="ml-auto size-4 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
        </button>
        <ul x-show="open && ($store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen)" x-transition class="ml-9 mt-2 space-y-1">
            @foreach($item->children as $child)
                @include('layouts.partials.sidebar-menu-item', ['item' => $child])
            @endforeach
        </ul>
    @else
        <a
            href="{{ $href }}"
            @if($item->open_in_new_tab) target="_blank" rel="noopener" @endif
            class="menu-item group"
            :class="[
                {{ $active ? 'true' : 'false' }} ? 'menu-item-active' : 'menu-item-inactive',
                (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'
            ]"
        >
            <span class="{{ $active ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}">{!! \App\Helpers\MenuHelper::getIconSvg($item->icon ?: 'pages') !!}</span>
            <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text flex items-center gap-2">
                {{ $navigation->title($item) }}
                @if($item->badge)
                    <span class="wx-pill ml-auto rounded-full px-2 py-0.5 text-[11px]">{{ $item->badge }}</span>
                @endif
            </span>
        </a>
    @endif
</li>
