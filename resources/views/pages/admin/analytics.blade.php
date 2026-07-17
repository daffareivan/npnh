@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Analytics" />
    <div class="grid gap-6 lg:grid-cols-2">
        <x-common.component-card title="Top User">@foreach($topUsers as $row)<div class="flex justify-between rounded-2xl border border-white/[0.05] bg-white/[0.03] p-3 text-sm text-[#A3A3A3]"><span>{{ $row->user?->name ?? 'Guest' }}</span><span class="text-white">{{ $row->total }}</span></div>@endforeach</x-common.component-card>
        <x-common.component-card title="Top Preset">@foreach($topPresets as $row)<div class="flex justify-between rounded-2xl border border-white/[0.05] bg-white/[0.03] p-3 text-sm text-[#A3A3A3]"><span>{{ $row->speed }}x</span><span class="text-white">{{ $row->total }}</span></div>@endforeach</x-common.component-card>
        <x-common.component-card title="Daily / Weekly / Monthly Conversion"><div class="h-40 rounded-[20px] border border-white/[0.05] bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.10),transparent_13rem)]"></div></x-common.component-card>
        <x-common.component-card title="Storage Growth & Download Count"><div class="h-40 rounded-[20px] border border-white/[0.05] bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.10),transparent_13rem)]"></div></x-common.component-card>
    </div>
@endsection
