<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>QR Code Manager — KN Consulting &amp; Innovation Ltd</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>
    <body class="min-h-screen bg-white text-neutral-900 antialiased dark:bg-neutral-950 dark:text-neutral-100" style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">

        {{-- Nav --}}
        <nav class="flex items-center justify-between px-6 py-4 lg:px-12 border-b border-neutral-200 dark:border-neutral-800">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-neutral-900 dark:bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" class="size-5 fill-current text-white dark:text-black">
                        <text x="50%" y="52%" dominant-baseline="central" text-anchor="middle" font-family="ui-sans-serif, system-ui, sans-serif" font-weight="700" font-size="18" fill="currentColor" letter-spacing="-1">KN</text>
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-semibold leading-tight">KN Consulting</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-tight">&amp; Innovation Ltd</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-neutral-900 dark:bg-white px-4 py-2 text-sm font-medium text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-100 transition-colors">
                        My QR Codes
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-neutral-600 dark:text-neutral-300 hover:text-neutral-900 dark:hover:text-white transition-colors">
                        Log in
                    </a>
                @endauth
            </div>
        </nav>

        {{-- Hero --}}
        <section class="flex flex-col items-center justify-center px-6 py-24 lg:py-36 text-center">
            <div class="inline-flex items-center gap-2 rounded-full border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900 px-4 py-1.5 text-xs font-medium text-neutral-600 dark:text-neutral-300 mb-8">
                <span class="size-1.5 rounded-full bg-green-500"></span>
                Dynamic QR Code Management
            </div>

            <h1 class="max-w-3xl text-4xl font-bold leading-tight tracking-tight lg:text-6xl">
                QR Codes that never<br>
                <span class="text-neutral-400 dark:text-neutral-500">go out of date</span>
            </h1>

            <p class="mt-6 max-w-xl text-lg text-neutral-500 dark:text-neutral-400 leading-relaxed">
                Create QR codes that point to <strong class="text-neutral-700 dark:text-neutral-200">your redirect URL</strong>. Change the destination anytime — without reprinting a single flyer, menu, or business card.
            </p>

            <div class="mt-10 flex flex-col sm:flex-row items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-lg bg-neutral-900 dark:bg-white px-6 py-3 text-sm font-semibold text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-100 transition-colors">
                        Go to Dashboard
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg bg-neutral-900 dark:bg-white px-6 py-3 text-sm font-semibold text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-100 transition-colors">
                        Sign in to your portal
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                @endauth
            </div>
        </section>

        {{-- Features --}}
        <section class="border-t border-neutral-200 dark:border-neutral-800 px-6 py-20 lg:px-12">
            <div class="mx-auto max-w-5xl">
                <div class="grid gap-8 md:grid-cols-3">

                    <div class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900 p-6">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-900 dark:bg-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-white dark:text-neutral-900">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-2">Dynamic Redirects</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Your QR code always points to our server. Change where it redirects to anytime from your dashboard — instantly.
                        </p>
                    </div>

                    <div class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900 p-6">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-900 dark:bg-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-white dark:text-neutral-900">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-2">Download &amp; Print</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Download your QR code as a crisp SVG file — perfect for print, signage, menus, or marketing materials.
                        </p>
                    </div>

                    <div class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900 p-6">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-900 dark:bg-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-white dark:text-neutral-900">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-2">Client Portals</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Each client gets their own secure login to manage, edit, and download their QR codes — no technical knowledge needed.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="border-t border-neutral-200 dark:border-neutral-800 px-6 py-8 lg:px-12">
            <div class="mx-auto max-w-5xl flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    &copy; {{ date('Y') }} KN Consulting &amp; Innovation Ltd. All rights reserved.
                </p>
                <a href="{{ route('login') }}" class="text-sm text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors">
                    Client Login →
                </a>
            </div>
        </footer>

    </body>
</html>
