@extends('layouts.public')

@section('title', 'Blog | ' . config('app.name', 'LibrarySaaS'))

@section('content')
<section class="section-space bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">Blog</p>
            <h1 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl font-display">Insights for study
                libraries</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600 sm:text-base">
                Articles on operations, attendance, pricing, and workflows for serious library owners.
            </p>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($posts as $post)
                <article class="card-lift reveal rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    @if($post->image_path)
                        <a href="{{ route('public.blog.show', $post->slug) }}" class="block">
                            <div class="aspect-[4/3] w-full bg-slate-100">
                                <img src="{{ Storage::url($post->image_path) }}" alt="{{ $post->title }}"
                                    class="h-full w-full object-cover">
                            </div>
                        </a>
                    @endif
                    <div class="p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                        {{ $post->published_at?->format('M d, Y') ?? 'Draft' }}
                    </p>
                    <h2 class="mt-2 text-base font-semibold text-slate-900">
                        <a href="{{ route('public.blog.show', $post->slug) }}" class="hover:text-indigo-600">
                            {{ $post->title }}
                        </a>
                    </h2>
                    @if($post->excerpt)
                        <p class="mt-2 text-sm text-slate-600 line-clamp-3">{{ $post->excerpt }}</p>
                    @endif
                    <a href="{{ route('public.blog.show', $post->slug) }}"
                        class="mt-3 inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                        Read article
                        <svg class="ml-1 h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14m-6-6 6 6-6 6" />
                        </svg>
                    </a>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-slate-200 bg-slate-50 p-10 text-center text-sm text-slate-500">
                    No posts published yet. Check back soon.
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </div>
</section>
@endsection

