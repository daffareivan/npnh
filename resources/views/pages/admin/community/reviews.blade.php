@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Homepage Reviews" />
    @if(session('status'))<div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">{{ session('status') }}</div>@endif
    <form class="wx-card mb-5 grid gap-3 p-3 sm:grid-cols-[1fr_180px_auto]">
        <input name="search" value="{{ request('search') }}" class="h-11 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none placeholder:text-[#A3A3A3] focus:border-white/30" placeholder="Search title, content, or user">
        <select name="status" class="h-11 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white">
            <option value="">All status</option>
            @foreach(['approved','pending','rejected','hidden','deleted'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button class="wx-btn-secondary px-5 py-3">Filter</button>
    </form>
    <x-common.component-card title="Reviews Moderation">
        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full text-sm">
                <thead class="border-b border-white/[0.06] text-left text-xs uppercase tracking-[0.14em] text-[#6B7280]">
                    <tr><th class="px-4 py-3">User</th><th class="px-4 py-3">Review</th><th class="px-4 py-3">Rating</th><th class="px-4 py-3">Helpful</th><th class="px-4 py-3">Moderation</th></tr>
                </thead>
                <tbody class="divide-y divide-white/[0.06]">
                    @foreach($reviews as $review)
                        <tr>
                            <td class="px-4 py-3 text-white">
                                {{ $review->user?->name }}
                                <div class="mt-1 flex flex-wrap gap-1">@foreach($review->user?->badges ?? [] as $badge)<x-community.badge :badge="$badge" />@endforeach</div>
                            </td>
                            <td class="max-w-md px-4 py-3"><p class="font-semibold text-white">{{ $review->title }}</p><p class="line-clamp-2 text-[#A3A3A3]">{{ $review->content }}</p></td>
                            <td class="px-4 py-3 text-white">{{ $review->rating }}</td>
                            <td class="px-4 py-3 text-[#A3A3A3]">{{ $review->helpful_count }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.content.homepage-reviews.update', $review) }}" class="flex flex-wrap items-center gap-2">
                                    @csrf @method('PUT')
                                    <select name="status" class="h-10 rounded-xl border border-white/[0.08] bg-black/20 px-3 text-sm text-white">
                                        @foreach(['approved','pending','rejected','hidden','deleted'] as $status)<option value="{{ $status }}" @selected($review->status === $status)>{{ ucfirst($status) }}</option>@endforeach
                                    </select>
                                    <label class="flex items-center gap-2 text-[#A3A3A3]"><input type="checkbox" name="is_pinned" value="1" @checked($review->is_pinned)> Pin</label>
                                    <label class="flex items-center gap-2 text-[#A3A3A3]"><input type="checkbox" name="is_featured" value="1" @checked($review->is_featured)> Feature</label>
                                    <button class="wx-btn-secondary px-4 py-2">Save</button>
                                </form>
                                @if($review->user)
                                    <form method="POST" action="{{ route('admin.community.users.ban', $review->user) }}" class="mt-2">
                                        @csrf
                                        <button class="rounded-xl border border-rose-400/20 bg-rose-400/10 px-4 py-2 text-sm text-rose-200">{{ $review->user->community_banned_at ? 'Unban Community' : 'Ban Community' }}</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid gap-3 lg:hidden">
            @foreach($reviews as $review)
                <div class="rounded-2xl border border-white/[0.08] bg-black/20 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-white">{{ $review->user?->name }}</p>
                            <div class="mt-1 flex flex-wrap gap-1">@foreach($review->user?->badges ?? [] as $badge)<x-community.badge :badge="$badge" />@endforeach</div>
                        </div>
                        <span class="shrink-0 text-xs text-[#A3A3A3]">★ {{ $review->rating }} · {{ $review->helpful_count }} helpful</span>
                    </div>
                    <div class="mt-3">
                        <p class="font-semibold text-white">{{ $review->title }}</p>
                        <p class="line-clamp-2 text-sm text-[#A3A3A3]">{{ $review->content }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.content.homepage-reviews.update', $review) }}" class="mt-3 flex flex-wrap items-center gap-2 border-t border-white/[0.06] pt-3">
                        @csrf @method('PUT')
                        <select name="status" class="h-10 rounded-xl border border-white/[0.08] bg-black/20 px-3 text-sm text-white">
                            @foreach(['approved','pending','rejected','hidden','deleted'] as $status)<option value="{{ $status }}" @selected($review->status === $status)>{{ ucfirst($status) }}</option>@endforeach
                        </select>
                        <label class="flex items-center gap-2 text-[#A3A3A3]"><input type="checkbox" name="is_pinned" value="1" @checked($review->is_pinned)> Pin</label>
                        <label class="flex items-center gap-2 text-[#A3A3A3]"><input type="checkbox" name="is_featured" value="1" @checked($review->is_featured)> Feature</label>
                        <button class="wx-btn-secondary px-4 py-2">Save</button>
                    </form>
                    @if($review->user)
                        <form method="POST" action="{{ route('admin.community.users.ban', $review->user) }}" class="mt-2">
                            @csrf
                            <button class="w-full rounded-xl border border-rose-400/20 bg-rose-400/10 px-4 py-2 text-sm text-rose-200">{{ $review->user->community_banned_at ? 'Unban Community' : 'Ban Community' }}</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-5">{{ $reviews->links() }}</div>
    </x-common.component-card>
@endsection
