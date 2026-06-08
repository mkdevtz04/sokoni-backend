<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become a Driver — Sokoni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#FF6600', 'primary-dark': '#CC5200' } } }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-gray-800">Sokoni</p>
                <p class="text-xs text-gray-500">Delivery Partner Portal</p>
            </div>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-10">

        <!-- Hero -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Become a Sokoni Driver</h1>
            <p class="text-gray-500 text-sm max-w-md mx-auto">
                Join our delivery team and earn money on your schedule.
                Fill out the form below and our team will review your application.
            </p>
        </div>

        <!-- Success banner -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-4 text-sm flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Error summary -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-600 rounded-xl px-5 py-4 text-sm">
                <p class="font-semibold mb-1">Please fix the following errors:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Application form -->
        <form method="POST" action="{{ route('become-driver.store') }}"
              enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Personal info -->
            <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Personal Information</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}"
                        placeholder="e.g. John Michael Doe"
                        class="w-full px-4 py-2.5 border @error('full_name') border-red-400 @else border-gray-200 @enderror
                               rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('full_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                            placeholder="e.g. 0712345678"
                            class="w-full px-4 py-2.5 border @error('phone') border-red-400 @else border-gray-200 @enderror
                                   rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            placeholder="Optional"
                            class="w-full px-4 py-2.5 border @error('email') border-red-400 @else border-gray-200 @enderror
                                   rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Identity -->
            <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Identity Verification</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">National ID Number *</label>
                    <input type="text" name="national_id_number" value="{{ old('national_id_number') }}"
                        placeholder="e.g. 19920101-12345-00001-7"
                        class="w-full px-4 py-2.5 border @error('national_id_number') border-red-400 @else border-gray-200 @enderror
                               rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    @error('national_id_number')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">National ID Image *</label>
                    <p class="text-xs text-gray-400 mb-2">Upload a clear photo of your national ID card (front)</p>
                    <input type="file" name="national_id_image" accept=".jpg,.jpeg,.png,.pdf"
                        class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4
                               file:rounded-lg file:border-0 file:text-sm file:font-medium
                               file:bg-orange-50 file:text-primary hover:file:bg-orange-100
                               @error('national_id_image') border border-red-300 rounded-xl p-1 @enderror">
                    @error('national_id_image')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Driver Documents</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Driver's License *</label>
                    <p class="text-xs text-gray-400 mb-2">Upload a clear photo or scan of your valid driver's license</p>
                    <input type="file" name="driver_license" accept=".jpg,.jpeg,.png,.pdf"
                        class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4
                               file:rounded-lg file:border-0 file:text-sm file:font-medium
                               file:bg-orange-50 file:text-primary hover:file:bg-orange-100
                               @error('driver_license') border border-red-300 rounded-xl p-1 @enderror">
                    @error('driver_license')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Application Photo *</label>
                    <p class="text-xs text-gray-400 mb-2">A recent clear passport-style photo of yourself</p>
                    <input type="file" name="application_photo" accept=".jpg,.jpeg,.png"
                        class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4
                               file:rounded-lg file:border-0 file:text-sm file:font-medium
                               file:bg-orange-50 file:text-primary hover:file:bg-orange-100
                               @error('application_photo') border border-red-300 rounded-xl p-1 @enderror">
                    @error('application_photo')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Terms -->
            <div class="flex items-start gap-3">
                <input type="checkbox" id="terms" required
                    class="mt-0.5 w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                <label for="terms" class="text-sm text-gray-600">
                    I confirm that all information and documents provided are genuine and accurate.
                    I understand that providing false information will result in immediate disqualification.
                </label>
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full bg-primary hover:bg-primary-dark text-white font-semibold
                       py-3.5 rounded-xl transition-colors text-sm">
                Submit Application
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-6">
            Questions? Contact us at
            <a href="mailto:support@sokoni.co.tz" class="text-primary underline">support@sokoni.co.tz</a>
        </p>

    </main>
</body>
</html>
