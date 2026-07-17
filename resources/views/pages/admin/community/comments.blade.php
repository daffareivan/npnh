@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Community Comments" />
    @if(session('status'))<div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.05] px-4 py-3 text-sm text-white">{{ session('status') }}</div>@endif
    <x-common.component-card title="Comments Moderation">
        <div class="overflow-x-auto">
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
        <div class="mt-5">{{ $comments->links() }}</div>
    </x-common.component-card>
@endsection
