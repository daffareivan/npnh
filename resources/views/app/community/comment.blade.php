<div class="{{ $depth > 1 ? 'ml-6 border-l border-white/[0.07] pl-4' : '' }}">
    <div class="flex gap-3">
        <div class="grid size-8 shrink-0 place-items-center rounded-full border border-white/10 bg-white/[0.06] text-xs font-semibold text-white">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</div>
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <p class="text-sm font-semibold text-white">{{ $comment->user->name }}</p>
                @if($comment->user->badges->first())
                    <x-community.badge :badge="$comment->user->badges->first()" />
                @endif
                <span class="text-xs text-[#A3A3A3]">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <p class="mt-1 text-sm leading-6 text-[#D4D4D4]">{{ $comment->content }}</p>
            <div class="mt-2 flex gap-3 text-xs text-[#A3A3A3]">
                <button>Like</button>
                @if($depth < 3 && ! $comment->is_locked)
                    <button type="button" onclick="document.getElementById('reply-{{ $comment->id }}').classList.toggle('hidden')">Reply</button>
                @endif
            </div>
            @if($depth < 3 && ! $comment->is_locked)
                <form id="reply-{{ $comment->id }}" method="POST" action="{{ route('app.community.comments.store', $review) }}" class="mt-3 hidden flex gap-2">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <input name="content" placeholder="Write a reply" class="h-10 flex-1 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-xs text-white outline-none focus:border-white/30">
                    <button class="wx-btn-secondary px-3 text-xs">Send</button>
                </form>
            @endif
        </div>
    </div>
    <div class="mt-3 space-y-3">
        @foreach($comment->replies as $reply)
            @include('app.community.comment', ['comment' => $reply, 'review' => $review, 'depth' => $depth + 1])
        @endforeach
    </div>
</div>
