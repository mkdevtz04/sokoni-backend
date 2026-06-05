<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sokoni Admin — Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#FF6600' } } } }</script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary rounded-2xl mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Sokoni Admin</h1>
            <p class="text-gray-500 text-sm mt-1">Sign in to manage your marketplace</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-sm p-8">
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-5 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm
                               focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div class="flex items-center mb-6">
                    <input type="checkbox" name="remember" id="remember" class="accent-primary mr-2">
                    <label for="remember" class="text-sm text-gray-600">Remember me</label>
                </div>
                <button type="submit"
                    class="w-full bg-primary hover:bg-orange-700 text-white font-semibold py-2.5
                           rounded-xl transition-colors">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</body>
</html>
