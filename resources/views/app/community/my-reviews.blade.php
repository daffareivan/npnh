@extends('layouts.user-app')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <x-wx.section-card>
            <h1 class="text-3xl font-semibold text-white">My Reviews</h1>
            @if($review)
                <div class="mt-6 rounded-2xl border border-white/10 bg-black/20 p-5">
                    <p class="text-white">{{ str_repeat('★', $review->rating) }}</p>
                    <h2 class="mt-3 text-xl font-semibold text-white">{{ $review->title }}</h2>
                    <p class="mt-2 text-[#A3A3A3]">{{ $review->content }}</p>
                    <a href="{{ route('app.community.reviews') }}#write-review" class="mt-5 inline-flex wx-btn-primary px-5 py-3">Update Review</a>
                </div>
            @else
                <p class="mt-4 text-[#A3A3A3]">You have not written a review yet.</p>
                <a href="{{ route('app.community.reviews') }}#write-review" class="mt-5 inline-flex wx-btn-primary px-5 py-3">Write Review</a>
            @endif
        </x-wx.section-card>
    </section>
@endsection
