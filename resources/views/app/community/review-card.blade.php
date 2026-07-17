@php
    $primaryBadge = $review->user->badges->first();
    $stats = $userStats($review->user);
@endphp

<article class="group rounded-[28px] border border-white/[0.08] bg-[#111214] p-6 shadow-[0_18px_70px_rgba(0,0,0,.24)] transition duration-300 hover:border-white/20">
    <div class="flex gap-4">
        <div class="relative">
            <div class="grid size-12 place-items-center rounded-full border border-white/10 bg-white/[0.07] text-sm font-semibold text-white">{{ strtoupper(substr($review->user->name, 0, 1)) }}</div>
            <div class="pointer-events-none absolute left-0 top-14 z-20 hidden w-72 rounded-2xl border border-white/10 bg-[#111214] p-4 shadow-2xl group-hover:block">
                <div class="flex items-center gap-3">
                    <div class="grid size-11 place-items-center rounded-full bg-white/10 text-white">{{ strtoupper(substr($review->user->name, 0, 1)) }}</div>
                    <div>
                        <p class="font-semibold text-white">{{ $review->user->name }}</p>
                        @if($primaryBadge)<x-community.badge :badge="$primaryBadge" class="mt-1" />@endif
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2 text-xs text-[#A3A3A3]">
                    <p>Joined <span class="block text-white">{{ $stats['joined'] }}</span></p>
                    <p>Conversions <span class="block text-white">{{ $stats['conversions'] }}</span></p>
                    <p>Downloads <span class="block text-white">{{ $stats['downloads'] }}</span></p>
                    <p>Credits Used <span class="block text-white">{{ $stats['credits_used'] }}</span></p>
                    <p class="col-span-2">Helpful Received <span class="block text-white">{{ $stats['helpful_received'] }}</span></p>
                </div>
            </div>
        </div>
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <p class="font-semibold text-white">{{ $review->user->name }}</p>
                @foreach($review->user->badges->take(2) as $badge)
                    <x-community.badge :badge="$badge" />
                @endforeach
            </div>
            <div class="mt-2 flex flex-wrap items-center gap-3 text-sm">
                <span class="text-white">{{ str_repeat('★', $review->rating) }}<span class="text-white/20">{{ str_repeat('★', 5 - $review->rating) }}</span></span>
                <span class="text-[#A3A3A3]">{{ $review->created_at->diffForHumans() }}</span>
            </div>
            <h3 class="mt-4 text-xl font-semibold text-white">{{ $review->title }}</h3>
            <p class="mt-2 leading-7 text-[#D4D4D4]">{{ $review->content }}</p>
            @if($review->screenshot_path)
                <img src="{{ Storage::url($review->screenshot_path) }}" alt="" class="mt-4 max-h-72 rounded-2xl border border-white/10 object-cover">
            @endif
            <div class="mt-5 flex flex-wrap items-center gap-3 text-sm">
                <form method="POST" action="{{ route('app.community.reviews.helpful', $review) }}">
                    @csrf
                    <button class="rounded-full border border-white/10 px-4 py-2 {{ $review->helped_by_user ? 'bg-white text-black' : 'bg-white/[0.03] text-white' }}">Helpful</button>
                </form>
                <span class="text-[#A3A3A3]">{{ $review->helpful_count }} Helpful</span>
                <a href="#comment-{{ $review->id }}" class="text-[#A3A3A3] hover:text-white">Reply</a>
                <button class="text-[#A3A3A3] hover:text-white">Report</button>
            </div>

            <div class="mt-6 space-y-4 border-t border-white/[0.06] pt-5">
                @foreach($review->comments as $comment)
                    @include('app.community.comment', ['comment' => $comment, 'review' => $review, 'depth' => 1])
                @endforeach
                <form id="comment-{{ $review->id }}" method="POST" action="{{ route('app.community.comments.store', $review) }}" class="flex gap-3">
                    @csrf
                    <input name="content" placeholder="Write a comment" class="h-11 flex-1 rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                    <button class="wx-btn-secondary px-4">Reply</button>
                </form>
            </div>
        </div>
    </div>
</article>
