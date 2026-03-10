@extends('layouts.public')

@section('title', $post->title . ' | ' . config('app.name', 'LibrarySaaS'))

@section('content')
<section class="section-space bg-white">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">Blog</p>
        <h1 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl font-display">
            {{ $post->title }}
        </h1>
        <p class="mt-2 text-xs text-slate-500">
            {{ $post->published_at?->format('M d, Y') }}
        </p>

        @if($post->image_path)
            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-slate-100">
                <img src="{{ Storage::url($post->image_path) }}" alt="{{ $post->title }}"
                    class="h-full w-full object-cover">
            </div>
        @endif

        @if($post->excerpt)
            <p class="mt-4 text-sm leading-6 text-slate-600 sm:text-base">
                {{ $post->excerpt }}
            </p>
        @endif

        <article class="prose prose-sm sm:prose base mt-6 max-w-none prose-headings:text-slate-900 prose-p:text-slate-700">
            {!! nl2br(e($post->body)) !!}
        </article>

        <div class="mt-8">
            <a href="{{ route('public.blog.index') }}"
                class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-indigo-600">
                <svg class="mr-1 h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 12H5m6-6-6 6 6 6" />
                </svg>
                Back to all articles
            </a>
        </div>
    </div>
</section>
@endsection

