@extends('layouts.user-app')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if(session('status'))
            <div class="mb-5 rounded-2xl border border-white/10 bg-white/[0.06] px-4 py-3 text-sm text-white">{{ session('status') }}</div>
        @endif

        <div class="mb-8 rounded-[32px] border border-white/[0.08] bg-[#111214]/90 p-8 shadow-[0_24px_100px_rgba(0,0,0,.34)]">
            <div class="grid gap-8 lg:grid-cols-[1fr_420px] lg:items-center">
                <div>
                    <p class="text-sm uppercase tracking-[0.24em] text-white/45">Community</p>
                    <h1 class="mt-3 text-4xl font-semibold tracking-tight text-white">Community Reviews</h1>
                    <p class="mt-4 max-w-2xl text-[#A3A3A3]">Read feedback from our creator community and share your own experience using NPNHCREATIVE.</p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="#write-review" class="wx-btn-primary px-5 py-3">Write Review</a>
                        <a href="{{ route('app.community.leaderboard') }}" class="wx-btn-secondary px-5 py-3">Leaderboard</a>
                    </div>
                </div>
                <div class="rounded-[28px] border border-white/[0.08] bg-black/20 p-6">
                    <div class="flex items-end gap-4">
                        <p class="text-6xl font-semibold text-white">{{ number_format($summary['average'], 1) }}</p>
                        <div class="pb-2">
                            <x-community.rating-stars :rating="$summary['average']" size="18" class="whitespace-nowrap" />
                            <p class="mt-1 text-sm text-[#A3A3A3]">{{ number_format($summary['count']) }} Reviews</p>
                        </div>
                    </div>
                    <div class="mt-6 space-y-3">
                        @foreach([5,4,3,2,1] as $rating)
                            <div class="grid grid-cols-[58px_1fr_42px] items-center gap-3 text-xs text-[#A3A3A3]">
                                <span class="whitespace-nowrap">{{ str_repeat('★', $rating) }}</span>
                                <div class="h-2 overflow-hidden rounded-full bg-white/10"><div class="h-full rounded-full bg-white" style="width: {{ $summary['distribution'][$rating]['percent'] }}%"></div></div>
                                <span>{{ $summary['distribution'][$rating]['percent'] }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[390px_1fr]">
            <aside class="space-y-6">
                <div id="write-review" class="rounded-[28px] border border-white/[0.08] bg-[#111214] p-6">
                    <h2 class="text-xl font-semibold text-white">{{ $myReview ? 'Update Review' : 'Write Review' }}</h2>
                    <form method="POST" action="{{ route('app.community.reviews.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                        @csrf
                        <input name="title" value="{{ old('title', $myReview?->title) }}" placeholder="Title" class="h-12 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                        <textarea name="content" rows="5" placeholder="Share your experience" class="w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 py-3 text-sm text-white outline-none focus:border-white/30">{{ old('content', $myReview?->content) }}</textarea>
                        <select name="rating" class="h-12 w-full rounded-2xl border border-white/[0.08] bg-black/20 px-4 text-sm text-white outline-none focus:border-white/30">
                            @foreach([5,4,3,2,1] as $rating)
                                <option value="{{ $rating }}" @selected((int) old('rating', $myReview?->rating ?? 5) === $rating)>{{ $rating }} Stars</option>
                            @endforeach
                        </select>
                        <label class="block rounded-2xl border border-dashed border-white/[0.12] bg-black/20 px-4 py-4 text-sm text-[#A3A3A3]">
                            Upload Screenshot
                            <input name="screenshot" type="file" accept="image/*" class="mt-3 block w-full text-xs text-[#A3A3A3]">
                        </label>
                        <button class="wx-btn-primary w-full px-5 py-3">{{ $myReview ? 'Update Review' : 'Publish Review' }}</button>
                    </form>
                </div>

                <div class="rounded-[28px] border border-white/[0.08] bg-[#111214] p-6">
                    <h2 class="text-xl font-semibold text-white">Filters</h2>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <a href="{{ route('app.community.reviews') }}" class="rounded-full border border-white/10 px-3 py-2 text-xs text-white">All</a>
                        @foreach([5,4,3,2,1] as $rating)
                            <a href="{{ route('app.community.reviews', ['rating' => $rating]) }}" class="whitespace-nowrap rounded-full border border-white/10 px-3 py-2 text-xs text-[#A3A3A3] hover:text-white">{{ str_repeat('★', $rating) }}</a>
                        @endforeach
                        @foreach(['verified' => 'Verified', 'premium' => 'Premium', 'enterprise' => 'Enterprise'] as $slug => $label)
                            <a href="{{ route('app.community.reviews', ['badge' => $slug]) }}" class="rounded-full border border-white/10 px-3 py-2 text-xs text-[#A3A3A3] hover:text-white">{{ $label }}</a>
                        @endforeach
                    </div>
                </div>
            </aside>

            <div>
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach(['newest' => 'Newest', 'oldest' => 'Oldest', 'highest' => 'Highest Rating', 'lowest' => 'Lowest Rating', 'helpful' => 'Most Helpful', 'premium' => 'Premium Users', 'enterprise' => 'Enterprise Users'] as $sort => $label)
                            <a href="{{ route('app.community.reviews', ['sort' => $sort]) }}" class="rounded-full border border-white/10 bg-white/[0.03] px-3 py-2 text-xs text-[#A3A3A3] hover:text-white">{{ $label }}</a>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-5">
                    @forelse($reviews as $review)
                        @include('app.community.review-card', ['review' => $review, 'userStats' => $userStats])
                    @empty
                        <div class="rounded-[28px] border border-white/[0.08] bg-[#111214] p-10 text-center">
                            <p class="text-2xl font-semibold text-white">No reviews yet.</p>
                            <p class="mt-2 text-[#A3A3A3]">Be the first to share your experience.</p>
                            <a href="#write-review" class="mt-6 inline-flex wx-btn-primary px-5 py-3">Write Review</a>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">{{ $reviews->links() }}</div>
            </div>
        </div>
    </section>
@endsection
