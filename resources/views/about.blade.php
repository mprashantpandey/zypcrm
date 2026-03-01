@extends('layouts.public')

@section('title', 'About Us | ' . config('app.name', 'LibrarySaaS'))

@section('content')
<div class="bg-white px-6 py-32 lg:px-8">
    <div class="mx-auto max-w-3xl text-base leading-7 text-gray-700">
        <p class="text-base font-semibold leading-7 text-indigo-600 tracking-wide uppercase">About Us</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
            style="font-family: 'Outfit', sans-serif;">Empowering local study spaces</h1>
        <p class="mt-6 text-xl leading-8">
            We believe that study libraries and reading rooms play a crucial role in shaping the futures of ambitious
            students. Our mission is to provide the operating system that makes running these spaces effortless.
        </p>
        <div class="mt-10 max-w-2xl">
            <p>
                Traditionally, library owners have relied on scattered tools: notebooks for attendance, WhatsApp for
                announcements, and manual tracking for fees. This fragmentation leads to lost revenue, miscommunication,
                and countless hours of administrative busywork.
            </p>
            <ul role="list" class="mt-8 max-w-xl space-y-8 text-gray-600">
                <li class="flex gap-x-3">
                    <svg class="mt-1 h-5 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                            clip-rule="evenodd" />
                    </svg>
                    <span><strong class="font-semibold text-gray-900">Built for Library Owners.</strong> We designed
                        every feature alongside real library owners to solve actual pain points.</span>
                </li>
                <li class="flex gap-x-3">
                    <svg class="mt-1 h-5 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                            clip-rule="evenodd" />
                    </svg>
                    <span><strong class="font-semibold text-gray-900">Modern Technology.</strong> Leveraging the latest
                        in web tech to provide real-time syncing and robust performance.</span>
                </li>
                <li class="flex gap-x-3">
                    <svg class="mt-1 h-5 w-5 flex-none text-indigo-600" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                            clip-rule="evenodd" />
                    </svg>
                    <span><strong class="font-semibold text-gray-900">Focus on the Students.</strong> By automating
                        administration, owners can focus on creating the best environment for their students.</span>
                </li>
            </ul>
            <p class="mt-8">
                Join hundreds of other reading rooms and study spaces that have modernized their operations with our
                platform. Elevate your brand, increase student retention, and regain control over your time.
            </p>
        </div>
    </div>
</div>
@endsection