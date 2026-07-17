<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="NPNHCREATIVE is a fast Roblox audio converter trusted by creators, with premium conversion presets, credits, and community reviews.">
    <link rel="canonical" href="<?php echo e(route('home')); ?>">
    <meta property="og:title" content="NPNHCREATIVE - Fast Audio Converter for Roblox">
    <meta property="og:description" content="Convert Roblox audio in seconds and see why creators love NPNHCREATIVE.">
    <meta property="og:url" content="<?php echo e(route('home')); ?>">
    <meta property="og:type" content="website">
    <title>NPNHCREATIVE - Fast Audio Converter for Roblox</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "NPNHCREATIVE",
            "applicationCategory": "MultimediaApplication",
            "operatingSystem": "Web",
            "description": "Fast Roblox audio converter with optimized presets.",
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "<?php echo e(number_format($reviewSummary['average'] ?: 4.9, 1)); ?>",
                "reviewCount": "<?php echo e($reviewSummary['count']); ?>"
            },
            "review": <?php echo json_encode($reviewSchema, 15, 512) ?>
        }
    </script>
</head>
<body class="wx-shell min-h-screen antialiased" x-data="{ menu: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 16">
    <header class="fixed inset-x-0 top-0 z-50 transition-all duration-300" :class="scrolled ? 'border-b border-white/8 bg-[#050505]/78 shadow-2xl shadow-black/20 backdrop-blur-xl' : 'bg-transparent'">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Main navigation">
            <a href="<?php echo e(route('home')); ?>" class="group flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-2xl border border-white/8 bg-white/5 text-white transition group-hover:bg-white/[0.05]">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 11v2"/><path d="M7 7v10"/><path d="M11 4v16"/><path d="M15 8v8"/><path d="M19 10v4"/></svg>
                </span>
                <span class="text-base font-semibold tracking-tight">NPNHCREATIVE</span>
            </a>

            <div class="hidden items-center gap-7 text-sm text-[#A3A3A3] md:flex">
                <?php $__currentLoopData = ['Home' => '#home', 'Features' => '#features', 'Pricing' => '#pricing', 'Documentation' => route('app.documentation'), 'Changelog' => '#changelog']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $href): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($href); ?>" class="transition hover:text-white"><?php echo e($label); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                <a href="<?php echo e(route('signin')); ?>" class="wx-btn-secondary px-4 py-2.5 text-sm">Login</a>
                <a href="<?php echo e(route('signup')); ?>" class="wx-btn-primary px-4 py-2.5 text-sm">Get Started</a>
            </div>

            <button class="grid size-10 place-items-center rounded-2xl border border-white/8 bg-white/5 md:hidden" @click="menu = !menu" aria-label="Open menu">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h16"/></svg>
            </button>
        </nav>
        <div x-show="menu" x-transition class="mx-4 mb-4 rounded-3xl border border-white/8 bg-[#0B0B0C]/95 p-4 backdrop-blur md:hidden">
            <div class="grid gap-2 text-sm text-[#A3A3A3]">
                <a href="#home" class="rounded-2xl px-3 py-2 hover:bg-white/5 hover:text-white">Home</a>
                <a href="#features" class="rounded-2xl px-3 py-2 hover:bg-white/5 hover:text-white">Features</a>
                <a href="#pricing" class="rounded-2xl px-3 py-2 hover:bg-white/5 hover:text-white">Pricing</a>
                <a href="<?php echo e(route('app.documentation')); ?>" class="rounded-2xl px-3 py-2 hover:bg-white/5 hover:text-white">Documentation</a>
                <a href="#changelog" class="rounded-2xl px-3 py-2 hover:bg-white/5 hover:text-white">Changelog</a>
                <a href="<?php echo e(route('signup')); ?>" class="wx-btn-primary mt-2 px-4 py-3 text-center">Get Started</a>
            </div>
        </div>
    </header>

    <main id="home" class="overflow-hidden">
        <section class="relative mx-auto grid min-h-screen max-w-7xl items-center gap-12 px-4 pb-20 pt-32 sm:px-6 lg:grid-cols-[1fr_500px] lg:px-8">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-[44rem] bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.12),transparent_36rem)]"></div>
            <div class="wx-reveal">
                <span class="inline-flex rounded-full border border-white/10 bg-white/[0.05] px-4 py-2 text-sm font-medium text-white">Optimized for Roblox Audio</span>
                <h1 class="mt-7 max-w-4xl text-5xl font-semibold tracking-[-0.045em] text-white sm:text-6xl lg:text-7xl">Convert Roblox Audio<br class="hidden sm:block"> in Seconds.</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-[#A3A3A3]">Upload your MP3, WAV, M4A, or OGG file, choose an optimized preset, and let NPNHCREATIVE generate a Roblox-ready audio file in just a few seconds.</p>
                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="<?php echo e(route('signup')); ?>" class="wx-btn-primary px-6 py-3.5">Start Converting</a>
                    <a href="<?php echo e(route('app.documentation')); ?>" class="wx-btn-secondary px-6 py-3.5">View Documentation</a>
                </div>
                <p class="mt-6 text-sm text-[#A3A3A3]">Thousands of audio conversions completed.</p>
            </div>

            <div class="relative wx-reveal rounded-[24px] border border-white/[0.07] bg-[#0b0b0d] p-5 shadow-[0_24px_120px_rgba(255,255,255,.08)] before:pointer-events-none before:absolute before:inset-0 before:rounded-[24px] before:bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.16),transparent_18rem)] sm:p-6" style="animation-delay: 120ms">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white">Active Preset</p>
                        <div class="mt-3 flex items-end gap-3"><span class="text-5xl font-semibold">2.3x</span><span class="pb-2 text-[#A3A3A3]">Amplify -4 dB</span></div>
                    </div>
                    <span class="rounded-full border border-white/10 bg-white/[0.05] px-3 py-1 text-xs text-white">Live Preview</span>
                </div>
                <div class="relative grid grid-cols-3 gap-2">
                    <?php $__currentLoopData = [['2.3x','-4 dB'], ['2.5x','-6 dB'], ['2.7x','-8 dB']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$speed, $amp]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button class="wx-hover-lift rounded-[18px] border border-white/[0.08] bg-white/[0.035] p-4 text-left first:border-white/25 first:bg-white/[0.08]">
                            <span class="block text-lg font-semibold"><?php echo e($speed); ?></span>
                            <span class="text-sm text-[#A3A3A3]"><?php echo e($amp); ?></span>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="relative mt-5 grid min-h-56 place-items-center rounded-[20px] border border-dashed border-white/10 bg-black/30 p-8 text-center">
                    <div>
                        <div class="mx-auto grid size-14 place-items-center rounded-2xl bg-white/[0.05] text-white"><svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 16V4"/><path d="m7 9 5-5 5 5"/><path d="M20 16v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3"/></svg></div>
                        <p class="mt-4 font-semibold">Drop your audio here</p>
                        <p class="mt-1 text-sm text-[#A3A3A3]">or click to browse</p>
                        <div class="mt-4 flex flex-wrap justify-center gap-2 text-xs text-[#A3A3A3]">
                            <?php $__currentLoopData = ['OGG','MP3','WAV','M4A']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $format): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="rounded-full border border-white/8 px-3 py-1"><?php echo e($format); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
                <button class="wx-btn-primary mt-5 w-full px-5 py-3.5">Convert Audio</button>
            </div>
        </section>

        <section id="features" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-3">
                <?php $__currentLoopData = [
                    ['Automated conversion', 'Preset-driven audio processing with minimal manual effort.', 'M8 12h8M12 8v8'],
                    ['Multi-format compatibility', 'Convert OGG, MP3, WAV, and M4A smoothly across your workflow.', 'M4 7h16M7 12h10M10 17h4'],
                    ['Secure and compliant', 'Temporary storage and signed downloads help keep files protected.', 'M12 3l7 4v5c0 5-3 8-7 9-4-1-7-4-7-9V7l7-4Z'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$title, $copy, $path]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="wx-reveal wx-hover-lift relative min-h-64 overflow-hidden rounded-[22px] border border-white/[0.07] bg-[#0b0b0d] p-6 shadow-[0_24px_90px_rgba(0,0,0,.55)]">
                        <div class="absolute inset-x-0 top-0 h-36 bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.16),transparent_13rem)]"></div>
                        <div class="relative grid size-12 place-items-center rounded-2xl border border-white/10 bg-white/[0.04] text-white">
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="<?php echo e($path); ?>"/></svg>
                        </div>
                        <h3 class="relative mt-24 text-lg font-semibold"><?php echo e($title); ?></h3>
                        <p class="mt-2 text-sm leading-6 text-[#A3A3A3]"><?php echo e($copy); ?></p>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="grid items-center gap-10 lg:grid-cols-[1fr_560px]">
                <div class="wx-reveal">
                    <h2 class="max-w-xl text-4xl font-semibold tracking-[-0.04em] sm:text-5xl">Audio conversion engineered for maximum performance</h2>
                    <p class="mt-5 max-w-lg text-sm leading-6 text-[#A3A3A3]">Discover refined tools that streamline audio preparation, reduce manual work, and help your Roblox projects move faster.</p>
                    <a href="<?php echo e(route('signup')); ?>" class="mt-8 inline-flex rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-black transition hover:scale-[1.02]">Explore More ↗</a>
                </div>
                <div class="relative h-72 overflow-hidden rounded-[24px] border border-white/[0.07] bg-[#0b0b0d] p-6 shadow-[0_24px_100px_rgba(255,255,255,.06)]">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_40%_0%,rgba(255,255,255,.16),transparent_18rem)]"></div>
                    <div class="relative flex items-start justify-between">
                        <div><p class="text-3xl font-semibold">1,632</p><p class="text-xs text-[#A3A3A3]">Clicks</p></div>
                        <span class="h-1 w-12 rounded-full bg-white/70"></span>
                    </div>
                    <svg class="relative mt-12 h-28 w-full text-white/45" viewBox="0 0 420 140" fill="none">
                        <path d="M0 110 C65 110 58 30 126 44 C176 55 168 120 230 92 C286 66 274 38 330 52 C366 61 372 103 420 86" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span class="absolute left-[52%] top-[44%] rounded-full border border-white/10 bg-black/50 px-4 py-1 text-xs text-white/80">Chrome</span>
                </div>
            </div>
            <div class="mt-14 border-t border-white/[0.07] pt-7">
                <div class="grid gap-5 md:grid-cols-4">
                    <?php $__currentLoopData = [['AI Automation Systems','Streamline conversion processes with workflow automation.'], ['AI Development','Built around optimized processing for audio operations.'], ['Predictive Analytics','Forecast trends and success from your conversion history.'], ['Chatbots & Assistants','Enhance support with always-on help surfaces.']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$title, $copy]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <h3 class="text-sm font-semibold"><?php echo e($title); ?></h3>
                            <p class="mt-2 text-xs leading-5 text-[#A3A3A3]"><?php echo e($copy); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>

        <section id="pricing" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-semibold tracking-[-0.04em] sm:text-5xl">Find the Perfect Plan<br>for Your Business</h2>
                <p class="mx-auto mt-4 max-w-2xl text-sm text-[#A3A3A3]">Unlock your full potential with flexible pricing.</p>
            </div>
            <div class="mt-10 grid gap-4 md:grid-cols-4">
                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="wx-hover-lift relative overflow-hidden rounded-[22px] border border-white/[0.07] bg-[#0b0b0d] p-7 <?php echo e($plan->slug === 'standard' ? 'bg-[radial-gradient(circle_at_50%_0%,rgba(255,255,255,.28),rgba(11,11,13,1)_58%)]' : ''); ?>">
                        <?php $__currentLoopData = [$plan->slug === 'standard' ? 'Most Popular' : null, $plan->slug === 'premium' ? 'Best Value' : null]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(! $badge) continue; ?>
                            <span class="absolute right-6 top-6 rounded-full bg-white px-3 py-1 text-xs font-medium text-black"><?php echo e($badge); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <p class="text-lg text-white/80"><?php echo e($plan->name); ?></p>
                        <p class="mt-4 text-4xl font-semibold"><?php echo e($plan->formattedPrice()); ?></p>
                        <p class="mt-1 text-xs text-[#A3A3A3]">credit-based subscription</p>
                        <div class="mt-8 space-y-4 text-sm text-white/80">
                            <?php $__currentLoopData = ($plan->features ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p>- <?php echo e($item); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <a href="<?php echo e(route('signup')); ?>" class="mt-9 block rounded-full border border-white/10 bg-white <?php echo e($i === 1 ? 'text-black' : 'bg-white/[0.02] text-white'); ?> px-4 py-3 text-center text-sm font-semibold">Get Started</a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <section id="reviews" class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8" x-data="{ filter: 'all' }">
            <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(circle_at_18%_8%,rgba(168,85,247,.16),transparent_24rem),radial-gradient(circle_at_88%_36%,rgba(59,130,246,.12),transparent_22rem)]"></div>
            <div class="mb-10 grid gap-4 md:grid-cols-4">
                <?php $__currentLoopData = [
                    [\Illuminate\Support\Number::abbreviate($homepageStats['converted']), 'Audio Converted'],
                    [\Illuminate\Support\Number::abbreviate($homepageStats['users']), 'Registered Users'],
                    [number_format($homepageStats['rating'], 1), 'Average Rating'],
                    [number_format($homepageStats['success'], 1).'%', 'Success Rate'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$value, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-[22px] border border-white/[0.07] bg-[#0b0b0d] p-6 shadow-[0_18px_70px_rgba(0,0,0,.25)]">
                        <p class="text-3xl font-semibold text-white"><?php echo e($value); ?></p>
                        <p class="mt-2 text-sm text-[#A3A3A3]"><?php echo e($label); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="mb-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.24em] text-white/45">Social Proof</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-[-0.04em] text-white sm:text-5xl">Loved by Roblox Creators</h2>
                    <p class="mt-4 max-w-2xl text-sm leading-6 text-[#A3A3A3]">See what creators are saying about NPNHCREATIVE and share your own experience.</p>
                </div>
                <div class="w-full max-w-md rounded-[24px] border border-white/[0.08] bg-[#0b0b0d]/90 p-5 shadow-[0_22px_80px_rgba(0,0,0,.35)] backdrop-blur">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <?php if (isset($component)) { $__componentOriginale19f8fbbf09a1471bba83b18132c179c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f8fbbf09a1471bba83b18132c179c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.community.rating-stars','data' => ['rating' => $reviewSummary['average'],'size' => '20','showNumber' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('community.rating-stars'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['rating' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reviewSummary['average']),'size' => '20','showNumber' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f8fbbf09a1471bba83b18132c179c)): ?>
<?php $attributes = $__attributesOriginale19f8fbbf09a1471bba83b18132c179c; ?>
<?php unset($__attributesOriginale19f8fbbf09a1471bba83b18132c179c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f8fbbf09a1471bba83b18132c179c)): ?>
<?php $component = $__componentOriginale19f8fbbf09a1471bba83b18132c179c; ?>
<?php unset($__componentOriginale19f8fbbf09a1471bba83b18132c179c); ?>
<?php endif; ?>
                        </div>
                        <p class="pb-1 text-sm text-[#A3A3A3]">Based on <?php echo e(number_format($reviewSummary['count'])); ?> Reviews</p>
                    </div>
                    <div class="mt-5 space-y-2">
                        <?php $__currentLoopData = [5,4,3,2,1]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rating): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $percent = $reviewSummary['distribution'][$rating]['percent'];
                            ?>
                            <div class="grid grid-cols-[52px_1fr_34px] items-center gap-3 text-xs text-[#A3A3A3]">
                                <span><?php echo e($rating); ?> star</span>
                                <div class="h-2 overflow-hidden rounded-full bg-white/10">
                                    <div class="h-full rounded-full bg-white" style="width: <?php echo e($percent); ?>%"></div>
                                </div>
                                <span><?php echo e($percent); ?>%</span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <div class="mb-6 flex gap-2 overflow-x-auto pb-2 [scrollbar-width:none]">
                <?php $__currentLoopData = ['all' => 'All Reviews', '5' => '5 Stars', '4' => '4 Stars', '3' => '3 Stars', '2' => '2 Stars', '1' => '1 Star', 'premium' => 'Premium', 'standard' => 'Standard', 'enterprise' => 'Enterprise', 'verified' => 'Verified']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" @click="filter = '<?php echo e($key); ?>'" :class="filter === '<?php echo e($key); ?>' ? 'bg-white text-black shadow-[0_0_28px_rgba(255,255,255,.16)]' : 'bg-white/[0.04] text-[#A3A3A3] hover:border-white/20 hover:text-white'" class="shrink-0 rounded-full border border-white/10 px-4 py-2 text-xs font-semibold transition">
                        <?php echo e($label); ?> <span class="opacity-60">(<?php echo e($reviewFilterCounts[$key] ?? 0); ?>)</span>
                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="flex snap-x gap-5 overflow-x-auto pb-4 lg:grid lg:grid-cols-3 lg:overflow-visible">
                <?php $__empty_1 = true; $__currentLoopData = $homepageReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <article
                            x-show="filter === 'all' || filter === '<?php echo e($review->rating); ?>' || '<?php echo e($review->homepage_badge_slugs); ?>'.includes(filter)"
                            x-transition
                            class="min-w-[86%] snap-start rounded-[28px] border border-white/[0.08] bg-[#0b0b0d]/92 p-6 shadow-[0_24px_90px_rgba(0,0,0,.38)] backdrop-blur transition duration-300 hover:-translate-y-1 hover:border-white/25 hover:shadow-[0_30px_110px_rgba(168,85,247,.15)] sm:min-w-[420px] lg:min-w-0"
                        >
                            <div class="flex items-start gap-4">
                                <div class="group/avatar relative shrink-0">
                                    <div class="grid size-12 place-items-center overflow-hidden rounded-full border border-white/10 bg-gradient-to-br <?php echo e($review->homepage_gradient); ?> text-sm font-semibold text-white shadow-[0_12px_35px_rgba(0,0,0,.25)]">
                                        <?php if($review->homepage_avatar): ?>
                                            <img src="<?php echo e(str_starts_with($review->homepage_avatar, 'http') ? $review->homepage_avatar : asset('storage/'.$review->homepage_avatar)); ?>" alt="" class="h-full w-full object-cover">
                                        <?php else: ?>
                                            <?php echo e(strtoupper(substr($review->user?->name ?? 'U', 0, 1))); ?>

                                        <?php endif; ?>
                                    </div>
                                    <div class="pointer-events-none absolute left-0 top-14 z-30 w-72 origin-top-left scale-95 rounded-2xl border border-white/10 bg-[#0b0b0d]/95 p-4 opacity-0 shadow-[0_28px_90px_rgba(0,0,0,.45)] backdrop-blur-xl transition duration-200 group-hover/avatar:scale-100 group-hover/avatar:opacity-100">
                                        <div class="flex items-center gap-3">
                                            <div class="grid size-12 place-items-center overflow-hidden rounded-full border border-white/10 bg-gradient-to-br <?php echo e($review->homepage_gradient); ?> text-sm font-semibold text-white">
                                                <?php if($review->homepage_avatar): ?>
                                                    <img src="<?php echo e(str_starts_with($review->homepage_avatar, 'http') ? $review->homepage_avatar : asset('storage/'.$review->homepage_avatar)); ?>" alt="" class="h-full w-full object-cover">
                                                <?php else: ?>
                                                    <?php echo e(strtoupper(substr($review->user?->name ?? 'U', 0, 1))); ?>

                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-white"><?php echo e($review->user?->name ?? 'Creator'); ?></p>
                                                <div class="mt-1 flex flex-wrap gap-1.5">
                                                    <?php $__currentLoopData = collect([$review->homepage_membership_badge, $review->homepage_verified_badge])->filter(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if (isset($component)) { $__componentOriginal64fcfb64af9af28f0205d9af925eecb5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal64fcfb64af9af28f0205d9af925eecb5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.community.badge','data' => ['badge' => $badge]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('community.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['badge' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($badge)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal64fcfb64af9af28f0205d9af925eecb5)): ?>
<?php $attributes = $__attributesOriginal64fcfb64af9af28f0205d9af925eecb5; ?>
<?php unset($__attributesOriginal64fcfb64af9af28f0205d9af925eecb5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal64fcfb64af9af28f0205d9af925eecb5)): ?>
<?php $component = $__componentOriginal64fcfb64af9af28f0205d9af925eecb5; ?>
<?php unset($__componentOriginal64fcfb64af9af28f0205d9af925eecb5); ?>
<?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 grid grid-cols-2 gap-3 text-xs text-[#A3A3A3]">
                                            <p>Member Since <span class="mt-1 block text-white"><?php echo e($review->homepage_user_since); ?></span></p>
                                            <p>Current Plan <span class="mt-1 block text-white"><?php echo e($review->homepage_user_plan); ?></span></p>
                                            <p>Downloads <span class="mt-1 block text-white"><?php echo e($review->homepage_user_downloads); ?></span></p>
                                            <p>Conversions <span class="mt-1 block text-white"><?php echo e($review->homepage_user_conversions); ?></span></p>
                                            <p>Reviews <span class="mt-1 block text-white"><?php echo e($review->homepage_user_reviews); ?></span></p>
                                            <p>Helpful <span class="mt-1 block text-white"><?php echo e($review->homepage_user_helpful_received); ?></span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <p class="font-semibold text-white"><?php echo e($review->user?->name ?? 'Creator'); ?></p>
                                        <?php $__currentLoopData = collect([$review->homepage_membership_badge, $review->homepage_verified_badge])->filter(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if (isset($component)) { $__componentOriginal64fcfb64af9af28f0205d9af925eecb5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal64fcfb64af9af28f0205d9af925eecb5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.community.badge','data' => ['badge' => $badge]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('community.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['badge' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($badge)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal64fcfb64af9af28f0205d9af925eecb5)): ?>
<?php $attributes = $__attributesOriginal64fcfb64af9af28f0205d9af925eecb5; ?>
<?php unset($__attributesOriginal64fcfb64af9af28f0205d9af925eecb5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal64fcfb64af9af28f0205d9af925eecb5)): ?>
<?php $component = $__componentOriginal64fcfb64af9af28f0205d9af925eecb5; ?>
<?php unset($__componentOriginal64fcfb64af9af28f0205d9af925eecb5); ?>
<?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <?php if($review->is_featured): ?>
                                            <span class="rounded-full border border-white/10 bg-white px-3 py-1 text-xs font-semibold text-black">Editor's Choice</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php if (isset($component)) { $__componentOriginale19f8fbbf09a1471bba83b18132c179c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f8fbbf09a1471bba83b18132c179c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.community.rating-stars','data' => ['rating' => $review->rating,'size' => '18','showTooltip' => true,'class' => 'mt-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('community.rating-stars'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['rating' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($review->rating),'size' => '18','showTooltip' => true,'class' => 'mt-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f8fbbf09a1471bba83b18132c179c)): ?>
<?php $attributes = $__attributesOriginale19f8fbbf09a1471bba83b18132c179c; ?>
<?php unset($__attributesOriginale19f8fbbf09a1471bba83b18132c179c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f8fbbf09a1471bba83b18132c179c)): ?>
<?php $component = $__componentOriginale19f8fbbf09a1471bba83b18132c179c; ?>
<?php unset($__componentOriginale19f8fbbf09a1471bba83b18132c179c); ?>
<?php endif; ?>
                            <h3 class="mt-3 text-xl font-semibold text-white"><?php echo e($review->title); ?></h3>
                            <p class="mt-3 leading-7 text-[#D4D4D4]"><?php echo e(Str::limit($review->content, 170)); ?></p>
                            <div class="mt-5 flex flex-wrap gap-2 border-t border-white/[0.06] pt-4 text-sm text-[#A3A3A3]">
                                <a href="<?php echo e(route('signin')); ?>" class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/[0.03] px-3 py-2 transition hover:border-white/20 hover:text-white"><?php if (isset($component)) { $__componentOriginal619ce122d97a5e1b1586b601e82fa0cc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.community.icon','data' => ['name' => 'thumbs-up','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('community.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'thumbs-up','class' => 'size-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc)): ?>
<?php $attributes = $__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc; ?>
<?php unset($__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal619ce122d97a5e1b1586b601e82fa0cc)): ?>
<?php $component = $__componentOriginal619ce122d97a5e1b1586b601e82fa0cc; ?>
<?php unset($__componentOriginal619ce122d97a5e1b1586b601e82fa0cc); ?>
<?php endif; ?> <?php echo e($review->helpful_count); ?> Helpful</a>
                                <a href="<?php echo e(route('signin')); ?>" class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/[0.03] px-3 py-2 transition hover:border-white/20 hover:text-white"><?php if (isset($component)) { $__componentOriginal619ce122d97a5e1b1586b601e82fa0cc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.community.icon','data' => ['name' => 'reply','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('community.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'reply','class' => 'size-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc)): ?>
<?php $attributes = $__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc; ?>
<?php unset($__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal619ce122d97a5e1b1586b601e82fa0cc)): ?>
<?php $component = $__componentOriginal619ce122d97a5e1b1586b601e82fa0cc; ?>
<?php unset($__componentOriginal619ce122d97a5e1b1586b601e82fa0cc); ?>
<?php endif; ?> Reply</a>
                                <a href="<?php echo e(route('signin')); ?>" class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/[0.03] px-3 py-2 transition hover:border-white/20 hover:text-white"><?php if (isset($component)) { $__componentOriginal619ce122d97a5e1b1586b601e82fa0cc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.community.icon','data' => ['name' => 'flag','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('community.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'flag','class' => 'size-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc)): ?>
<?php $attributes = $__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc; ?>
<?php unset($__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal619ce122d97a5e1b1586b601e82fa0cc)): ?>
<?php $component = $__componentOriginal619ce122d97a5e1b1586b601e82fa0cc; ?>
<?php unset($__componentOriginal619ce122d97a5e1b1586b601e82fa0cc); ?>
<?php endif; ?> Report</a>
                                <a href="<?php echo e(route('signin')); ?>" class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/[0.03] px-3 py-2 transition hover:border-white/20 hover:text-white"><?php if (isset($component)) { $__componentOriginal619ce122d97a5e1b1586b601e82fa0cc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.community.icon','data' => ['name' => 'eye','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('community.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'eye','class' => 'size-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc)): ?>
<?php $attributes = $__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc; ?>
<?php unset($__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal619ce122d97a5e1b1586b601e82fa0cc)): ?>
<?php $component = $__componentOriginal619ce122d97a5e1b1586b601e82fa0cc; ?>
<?php unset($__componentOriginal619ce122d97a5e1b1586b601e82fa0cc); ?>
<?php endif; ?> <?php echo e($review->all_comments_count); ?> Replies</a>
                                <span class="inline-flex items-center gap-1.5 px-1 py-2"><?php if (isset($component)) { $__componentOriginal619ce122d97a5e1b1586b601e82fa0cc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.community.icon','data' => ['name' => 'clock','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('community.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clock','class' => 'size-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc)): ?>
<?php $attributes = $__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc; ?>
<?php unset($__attributesOriginal619ce122d97a5e1b1586b601e82fa0cc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal619ce122d97a5e1b1586b601e82fa0cc)): ?>
<?php $component = $__componentOriginal619ce122d97a5e1b1586b601e82fa0cc; ?>
<?php unset($__componentOriginal619ce122d97a5e1b1586b601e82fa0cc); ?>
<?php endif; ?> <?php echo e($review->created_at->diffForHumans()); ?></span>
                            </div>
                        </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="rounded-[28px] border border-white/[0.08] bg-[#0b0b0d] p-10 text-center lg:col-span-3">
                        <p class="text-2xl font-semibold text-white">No reviews yet.</p>
                        <p class="mt-2 text-[#A3A3A3]">Be the first to share your experience.</p>
                        <a href="<?php echo e(route('signin')); ?>" class="mt-6 inline-flex wx-btn-primary px-5 py-3">Write a Review</a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mt-8 text-center">
                <a href="<?php echo e(route('signin')); ?>" class="wx-btn-primary inline-flex px-6 py-3.5">Write a Review</a>
                </div>
        </section>

        <section id="faq" class="mx-auto max-w-5xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-semibold tracking-[-0.04em] text-white sm:text-5xl">FAQ</h2>
                <p class="mx-auto mt-4 max-w-2xl text-sm text-[#A3A3A3]">Quick answers before you start converting.</p>
            </div>
            <div class="mt-10 grid gap-3">
                <?php $__currentLoopData = [
                    ['Do credits run out when converting?', 'No. Credits are only used when downloading a result or uploading to Roblox.'],
                    ['Can I change presets freely?', 'Yes. You can try speed presets without spending credits.'],
                    ['Can I upload to Roblox?', 'Yes, when your Roblox Open Cloud setup is configured correctly.'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$question, $answer]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-[22px] border border-white/[0.07] bg-[#0b0b0d] p-6">
                        <p class="font-semibold text-white"><?php echo e($question); ?></p>
                        <p class="mt-2 text-sm leading-6 text-[#A3A3A3]"><?php echo e($answer); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="wx-card wx-glow bg-gradient-to-br from-[#FFFFFF]/14 to-white/[0.03] p-8 text-center sm:p-12">
                <h2 class="text-3xl font-semibold sm:text-5xl">Ready to convert your next Roblox audio?</h2>
                <p class="mt-4 text-[#A3A3A3]">Start converting for free in seconds.</p>
                <a href="<?php echo e(route('signup')); ?>" class="wx-btn-primary mt-8 inline-flex px-6 py-3.5">Get Started</a>
            </div>
        </section>
    </main>

    <footer class="border-t border-white/8 px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto grid max-w-7xl gap-8 md:grid-cols-5">
            <div class="md:col-span-2"><p class="font-semibold">NPNHCREATIVE</p><p class="mt-2 max-w-sm text-sm text-[#A3A3A3]">Fast Audio Converter for Roblox.</p></div>
            <?php $__currentLoopData = ['Product' => ['Features','Pricing','Documentation','Changelog'], 'Resources' => ['Help Center','API','Status'], 'Legal' => ['Privacy','Terms','License'], 'Social' => ['Discord','GitHub']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $title => $links): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <p class="font-medium"><?php echo e($title); ?></p>
                    <div class="mt-3 grid gap-2 text-sm text-[#A3A3A3]">
                        <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="#" class="hover:text-white"><?php echo e($link); ?></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <p class="mx-auto mt-10 max-w-7xl text-sm text-[#A3A3A3]">© 2025 NPNHCREATIVE. All rights reserved.</p>
    </footer>
</body>
</html>
