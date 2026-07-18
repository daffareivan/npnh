@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Community Comments" />
    @if(session('status'))<div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">{{ session('status') }}</div>@endif
    <form class="wx-card mb-5 grid gap-3 p-3 sm:grid-cols-[1fr_180px_auto]">
        <input name="search" value="{{ request('search') }}" class="h-11 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none placeholder:text-[#A3A3A3] focus:border-white/30" placeholder="Search content or user">
        <select name="status" class="h-11 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white">
            <option value="">All status</option>
            @foreach(['approved','pending','hidden','deleted'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button class="wx-btn-secondary px-5 py-3">Filter</button>
    </form>
    <x-common.component-card title="Comments Moderation">
        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full text-sm">
                <thead class="border-b border-white/[0.06] text-left text-xs uppercase tracking-[0.14em] text-[#6B7280]">
                    <tr><th class="px-4 py-3">User</th><th class="px-4 py-3">Review</th><th class="px-4 py-3">Comment</th><th class="px-4 py-3">Moderation</th></tr>
                </thead>
                <tbody class="divide-y divide-white/[0.06]">
                    @foreach($comments as $comment)
                        <tr>
                            <td class="px-4 py-3 text-white">{{ $comment->user?->name }}</td>
                            <td class="px-4 py-3 text-[#A3A3A3]">{{ $comment->review?->title }}</td>
                            <td class="max-w-md px-4 py-3 text-white">{{ $comment->content }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.community.comments.update', $comment) }}" class="flex flex-wrap items-center gap-2">
                                    @csrf @method('PUT')
                                    <select name="status" class="h-10 rounded-xl border border-white/[0.08] bg-black/20 px-3 text-sm text-white">
                                        @foreach(['approved','pending','hidden','deleted'] as $status)<option value="{{ $status }}" @selected($comment->status === $status)>{{ ucfirst($status) }}</option>@endforeach
                                    </select>
                                    <label class="flex items-center gap-2 text-[#A3A3A3]"><input type="checkbox" name="is_locked" value="1" @checked($comment->is_locked)> Lock</label>
                                    <button class="wx-btn-secondary px-4 py-2">Save</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid gap-3 lg:hidden">
            @foreach($comments as $comment)
                <div class="rounded-2xl border border-white/[0.08] bg-black/20 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-medium text-white">{{ $comment->user?->name }}</p>
                        <span class="shrink-0 text-xs text-[#A3A3A3]">{{ $comment->review?->title }}</span>
                    </div>
                    <p class="mt-2 text-sm text-white/85">{{ $comment->content }}</p>
                    <form method="POST" action="{{ route('admin.community.comments.update', $comment) }}" class="mt-3 flex flex-wrap items-center gap-2 border-t border-white/[0.06] pt-3">
                        @csrf @method('PUT')
                        <select name="status" class="h-10 rounded-xl border border-white/[0.08] bg-black/20 px-3 text-sm text-white">
                            @foreach(['approved','pending','hidden','deleted'] as $status)<option value="{{ $status }}" @selected($comment->status === $status)>{{ ucfirst($status) }}</option>@endforeach
                        </select>
                        <label class="flex items-center gap-2 text-[#A3A3A3]"><input type="checkbox" name="is_locked" value="1" @checked($comment->is_locked)> Lock</label>
                        <button class="wx-btn-secondary px-4 py-2">Save</button>
                    </form>
                </div>
            @endforeach
        </div>
        <div class="mt-5">{{ $comments->links() }}</div>
    </x-common.component-card>
@endsection
