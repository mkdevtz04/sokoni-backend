<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Sokoni Admin — Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#FF6600' } } } }</script>
    <style>button, a { -webkit-tap-highlight-color: transparent; }</style>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center px-4 py-8"
      style="padding-bottom: max(2rem, env(safe-area-inset-bottom))">

    <div class="w-full max-w-md">

        {{-- Language switcher --}}
        <div class="flex justify-end gap-1 mb-4">
            <form method="POST" action="{{ route('lang.switch', 'en') }}">
                @csrf
                <button type="submit"
                    class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors
                           {{ app()->getLocale() === 'en' ? 'bg-primary text-white' : 'bg-white text-gray-500 hover:bg-gray-200' }}">
                    EN
                </button>
            </form>
            <form method="POST" action="{{ route('lang.switch', 'sw') }}">
                @csrf
                <button type="submit"
                    class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors
                           {{ app()->getLocale() === 'sw' ? 'bg-primary text-white' : 'bg-white text-gray-500 hover:bg-gray-200' }}">
                    SW
                </button>
            </form>
        </div>

        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary rounded-2xl mb-4 shadow-lg shadow-orange-200">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('admin.login.title') }}</h1>
            <p class="text-gray-500 text-sm mt-1">{{ __('admin.login.subtitle') }}</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8">
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('admin.login.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent
                               transition-colors">
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('admin.login.password') }}</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent
                               transition-colors">
                </div>
                <div class="flex items-center mb-6">
                    <input type="checkbox" name="remember" id="remember"
                           class="w-4 h-4 accent-primary mr-2.5 cursor-pointer">
                    <label for="remember" class="text-sm text-gray-600 cursor-pointer select-none">{{ __('admin.login.remember') }}</label>
                </div>
                <button type="submit"
                    class="w-full bg-primary hover:bg-orange-700 text-white font-semibold py-3 rounded-xl
                           transition-colors min-h-12 text-sm">
                    {{ __('admin.login.submit') }}
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">{{ __('admin.login.footer') }} &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
