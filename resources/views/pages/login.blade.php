<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Login') }} - Maharani Mobil</title>
    @include('components.ui-system-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="/assets/app.css" rel="stylesheet" />
</head>
<body class="min-h-screen bg-slate-100 dark:bg-slate-900 flex items-center justify-center p-4">
    <div class="fixed right-4 top-4 z-50 flex items-center gap-2">
        @include('components.nav-tools')
        <a href="/" class="rounded-lg border border-slate-300 bg-white px-3 py-1 text-xs font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            {{ __('Back to Home') }}
        </a>
    </div>

    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg dark:bg-slate-800">
        <h1 class="mb-2 text-2xl font-bold text-slate-900 dark:text-slate-100">{{ __('Login Account') }}</h1>
        <p class="mb-6 text-sm text-slate-600 dark:text-slate-300">{{ __('Sign in to continue using Maharani Mobil system.') }}</p>

        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('Password') }}</label>
                <input id="password" name="password" type="password" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full rounded-lg bg-slate-900 py-2.5 font-medium text-white transition hover:bg-slate-800 dark:bg-slate-200 dark:text-slate-900 dark:hover:bg-slate-300">
                {{ __('Login') }}
            </button>
        </form>

        <div class="my-5 flex items-center gap-3">
            <div class="h-px flex-1 bg-slate-200 dark:bg-slate-700"></div>
            <span class="text-xs uppercase tracking-wide text-slate-400">{{ __('or') }}</span>
            <div class="h-px flex-1 bg-slate-200 dark:bg-slate-700"></div>
        </div>

        <a href="{{ route('auth.google') }}"
           class="inline-flex w-full items-center justify-center rounded-lg border border-slate-300 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-700">
            {{ __('Login with Google') }}
        </a>

        <p class="mt-6 text-center text-sm text-slate-600 dark:text-slate-300">
            {{ __('Dont have an account?') }}
            <a href="{{ route('register') }}" class="font-semibold text-slate-900 hover:underline dark:text-slate-100">{{ __('Register now') }}</a>
        </p>
    </div>

    @include('components.ui-system-footer')
</body>
</html>
