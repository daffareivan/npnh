@extends('layouts.user-app')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm uppercase tracking-[0.24em] text-white/45">Community</p>
            <h1 class="mt-3 text-4xl font-semibold text-white">Leaderboard</h1>
        </div>
        <div class="grid gap-5 lg:grid-cols-2">
            @foreach(['topReviewer' => 'Top Reviewer', 'mostHelpful' => 'Most Helpful', 'mostDownloads' => 'Most Downloads', 'mostActive' => 'Most Active'] as $key => $title)
                <div class="rounded-[28px] border border-white/[0.08] bg-[#111214] p-6">
                    <h2 class="text-xl font-semibold text-white">{{ $title }}</h2>
                    <div class="mt-5 space-y-3">
                        @foreach($leaderboard[$key] as $index => $user)
                            <div class="flex items-center justify-between rounded-2xl border border-white/[0.06] bg-black/20 p-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-[#A3A3A3]">#{{ $index + 1 }}</span>
                                    <span class="font-semibold text-white">{{ $user->name }}</span>
                                </div>
                                <span class="text-sm text-[#A3A3A3]">{{ $user->reviews_count ?? $user->review_comments_count ?? $user->audio_files_count ?? $user->helpful_total ?? 0 }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        @if($leaderboard['highestRatedReview'])
            <div class="mt-5 rounded-[28px] border border-white/[0.08] bg-[#111214] p-6">
                <p class="text-sm text-[#A3A3A3]">Highest Rated Review</p>
                <h2 class="mt-2 text-2xl font-semibold text-white">{{ $leaderboard['highestRatedReview']->title }}</h2>
                <p class="mt-2 text-white">{{ str_repeat('★', $leaderboard['highestRatedReview']->rating) }}</p>
            </div>
        @endif
    </section>
@endsection
