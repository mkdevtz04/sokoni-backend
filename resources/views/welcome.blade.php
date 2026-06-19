{{-- SOKONI LANDING PAGE --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Sokoni — Tanzania's Multi-Vendor Marketplace</title>
    <link rel="icon" type="image/jpeg" href="/images/sokonifavicon.jpeg">
    <meta name="description" content="Shop fresh groceries, spices, vegetables and more from trusted local vendors across Tanzania.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary:'#FF6600','primary-dark':'#CC5200','primary-light':'#FF8533' }, fontFamily:{ sans:['Inter','system-ui','sans-serif'] } } }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        html{scroll-behavior:smooth}
        body{font-family:'Inter',sans-serif}
        .gradient-hero{background:linear-gradient(135deg,#FF6600 0%,#FF8533 40%,#FFB366 100%)}
        .gradient-card{background:linear-gradient(135deg,#fff8f3 0%,#fff2e8 100%)}
        .blob{border-radius:60% 40% 30% 70%/60% 30% 70% 40%}
        /* Step connector — only valid in the 4-column lg layout */
        .step-connector{position:relative}
        @media(min-width:1024px){
            .step-connector::after{content:'';position:absolute;top:24px;left:calc(50% + 28px);right:calc(-50% + 28px);height:2px;background:linear-gradient(to right,#FF6600,#FFB366)}
        }
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}
        .float-1{animation:float 3.2s ease-in-out infinite}
        .float-2{animation:float 3.2s ease-in-out infinite;animation-delay:.5s}
        .float-3{animation:float 3.2s ease-in-out infinite;animation-delay:1s}
        nav a{transition:color .2s}
        /* Safe-area support for notched & punch-hole phones */
        .safe-x{padding-left:max(1rem,env(safe-area-inset-left));padding-right:max(1rem,env(safe-area-inset-right))}
        .safe-b{padding-bottom:max(2rem,env(safe-area-inset-bottom))}
        /* Prevent blue flash on tap for custom buttons */
        button,a{-webkit-tap-highlight-color:transparent}
        /* Tiny phone (≤380px) hero font guard */
        @media(max-width:380px){
            .hero-h1{font-size:1.875rem!important;line-height:1.2!important}
            .hero-sub{font-size:.9rem!important}
        }
        /* Mobile menu slide feel */
        #mobile-menu{transition:max-height .25s ease}
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">

{{-- NAVBAR --}}
<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100 transition-shadow duration-300">
    <div class="max-w-7xl mx-auto safe-x sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <a href="#" class="flex items-center gap-2 shrink-0">
            <img src="/images/sokoniicon.jpeg" alt="Sokoni" class="w-9 h-9 rounded-xl object-cover shadow-sm">
            <span class="text-xl font-bold text-gray-900">Sokoni</span>
        </a>
        <div class="hidden md:flex items-center gap-8">
            <a href="#features"    class="text-sm font-medium text-gray-600 hover:text-primary">Features</a>
            <a href="#how-it-works" class="text-sm font-medium text-gray-600 hover:text-primary">How it works</a>
            <a href="#vendors"     class="text-sm font-medium text-gray-600 hover:text-primary">For Vendors</a>
            <a href="{{ route('become-driver') }}" class="text-sm font-medium text-gray-600 hover:text-primary">For Drivers</a>
            <a href="{{ route('admin.login') }}"   class="text-sm font-medium text-gray-500 hover:text-primary">Admin</a>
        </div>
        <div class="flex items-center gap-3">
            <a href="#download" class="hidden sm:inline-flex items-center gap-2 bg-primary text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm hover:bg-primary-dark transition-colors min-h-11">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Download App
            </a>
            <button id="menu-btn" class="md:hidden p-2.5 rounded-xl text-gray-600 hover:bg-gray-100 min-w-11 min-h-11 flex items-center justify-center" aria-label="Open menu">
                <svg id="menu-icon-open"  class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg id="menu-icon-close" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    {{-- Mobile menu --}}
    <div id="mobile-menu" class="md:hidden hidden border-t border-gray-100 bg-white safe-x py-3 space-y-0.5">
        <a href="#features"    class="flex items-center py-3 text-sm font-medium text-gray-700 hover:text-primary border-b border-gray-50">Features</a>
        <a href="#how-it-works" class="flex items-center py-3 text-sm font-medium text-gray-700 hover:text-primary border-b border-gray-50">How it works</a>
        <a href="#vendors"     class="flex items-center py-3 text-sm font-medium text-gray-700 hover:text-primary border-b border-gray-50">For Vendors</a>
        <a href="{{ route('become-driver') }}" class="flex items-center py-3 text-sm font-medium text-gray-700 hover:text-primary border-b border-gray-50">For Drivers</a>
        <a href="{{ route('admin.login') }}"   class="flex items-center py-3 text-sm font-medium text-gray-500 hover:text-primary border-b border-gray-50">Admin Login</a>
        <a href="#download" class="flex items-center justify-center mt-3 bg-primary text-white text-sm font-semibold py-3.5 rounded-xl min-h-12">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Download App
        </a>
    </div>
</nav>

{{-- HERO --}}
<section class="gradient-hero pt-24 sm:pt-28 lg:pt-32 pb-16 sm:pb-24 lg:pb-32 overflow-hidden relative">
    <div class="absolute top-16 right-0 w-96 h-96 bg-white/10 blob pointer-events-none"></div>
    <div class="absolute -bottom-10 left-10 w-64 h-64 bg-white/10 blob pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-12 items-center">
            {{-- Text + CTA --}}
            <div>
                
                <h1 class="hero-h1 text-3xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-5 sm:mb-6">
                    Fresh products,<br><span class="text-yellow-200">right to your</span><br>doorstep
                </h1>
                <p class="hero-sub text-base sm:text-lg text-orange-100 mb-7 sm:mb-8 max-w-md leading-relaxed">
                    Shop groceries, spices, fresh vegetables, and more from hundreds of trusted local vendors across Tanzania — all in one app.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <a href="#download" class="inline-flex items-center justify-center gap-2 bg-white text-primary font-bold text-sm sm:text-base px-6 sm:px-7 py-3.5 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 min-h-[52px]">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.7 9.05 7.4c1.39.07 2.36.74 3.16.75.96-.08 1.89-.77 3.14-.83 2.58.24 4.39 1.64 4.39 5.15-2.22.95-3.32 3.02-2.69 5.81zM12 7.3c-.13-2.5 1.96-4.6 4.3-4.8.24 2.5-2.22 4.8-4.3 4.8z"/></svg>
                        Download for Android
                    </a>
                    <a href="#vendors" class="inline-flex items-center justify-center gap-2 border-2 border-white/50 text-white font-bold text-sm sm:text-base px-6 sm:px-7 py-3.5 rounded-xl hover:bg-white/10 transition-colors min-h-[52px]">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Become a Vendor
                    </a>
                </div>

                {{-- Mobile floating preview cards (lg screens use the right column instead) --}}
                <div class="lg:hidden mt-10 relative h-40 sm:h-44">
                    <div class="float-2 absolute top-0 left-0 bg-white rounded-2xl shadow-xl px-3.5 py-2.5 flex items-center gap-2.5">
                        <div class="w-9 h-9 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div><p class="text-xs font-bold text-gray-900">Order Placed!</p><p class="text-xs text-gray-500">Arriving in 30 min</p></div>
                    </div>
                    <div class="float-1 absolute top-4 left-1/2 -translate-x-1/2 bg-white rounded-2xl shadow-2xl p-3.5 w-36 sm:w-40">
                        <div class="bg-orange-50 rounded-xl p-2.5 mb-2 flex items-center justify-center h-16 sm:h-20">
                            <svg class="w-9 h-9 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div class="h-7 bg-primary rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs font-bold">Order Now</span>
                        </div>
                    </div>
                    <div class="float-3 absolute bottom-0 right-0 bg-white rounded-2xl shadow-xl px-3.5 py-2.5 flex items-center gap-2.5">
                        <div class="w-9 h-9 bg-orange-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                        </div>
                        <div><p class="text-xs font-bold text-gray-900">500+ Vendors</p><p class="text-xs text-gray-500">Across Tanzania</p></div>
                    </div>
                </div>
            </div>

            {{-- Desktop floating cards --}}
            <div class="hidden lg:block relative h-96">
                <div class="float-1 absolute top-0 left-1/2 -translate-x-1/2 bg-white rounded-3xl shadow-2xl p-5 w-56">
                    <div class="bg-orange-50 rounded-2xl p-4 mb-3 flex items-center justify-center h-28">
                        <svg class="w-14 h-14 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="space-y-2">
                        <div class="h-2.5 bg-gray-100 rounded-full w-3/4"></div>
                        <div class="h-2 bg-gray-50 rounded-full w-1/2"></div>
                        <div class="h-8 bg-primary rounded-xl w-full mt-2 flex items-center justify-center"><span class="text-white text-xs font-bold">Order Now</span></div>
                    </div>
                </div>
                <div class="float-2 absolute top-6 -left-6 bg-white rounded-2xl shadow-xl px-4 py-3 flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div><p class="text-sm font-bold text-gray-900">Order Placed!</p><p class="text-xs text-gray-500">Arriving in 30 min</p></div>
                </div>
                <div class="float-3 absolute bottom-6 right-0 bg-white rounded-2xl shadow-xl px-4 py-3 flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    </div>
                    <div><p class="text-sm font-bold text-gray-900">500+ Vendors</p><p class="text-xs text-gray-500">Across Tanzania</p></div>
                </div>
            </div>
        </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0 pointer-events-none">
        <svg viewBox="0 0 1440 56" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" class="w-full h-10 sm:h-14">
            <path d="M0 56L60 46C120 36 240 16 360 12.7C480 9.3 600 22.7 720 26C840 29.3 960 22.7 1080 19.3C1200 16 1320 16 1380 16L1440 16V56H0Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- STATS --}}
<section class="py-12 sm:py-14 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-3 divide-x divide-gray-100 text-center">
            <div class="px-2 sm:px-6">
                <p class="text-2xl sm:text-3xl lg:text-4xl font-black text-primary">500+</p>
                <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium leading-tight">Local Vendors</p>
            </div>
            <div class="px-2 sm:px-6">
                <p class="text-2xl sm:text-3xl lg:text-4xl font-black text-primary">10K+</p>
                <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium leading-tight">Products Listed</p>
            </div>
            <div class="px-2 sm:px-6">
                <p class="text-2xl sm:text-3xl lg:text-4xl font-black text-primary">50K+</p>
                <p class="text-xs sm:text-sm text-gray-500 mt-1 font-medium leading-tight">Happy Customers</p>
            </div>
        </div>
    </div>
</section>

{{-- CATEGORIES --}}
<section id="categories" class="py-16 sm:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 sm:mb-12">
            <span class="text-primary font-semibold text-xs sm:text-sm uppercase tracking-widest">Browse by Category</span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mt-2">Shop what you need</h2>
        </div>
        @php
        $categories = [
            ['img'=>'/images/vehetables.png', 'name'=>'Vegetables & Greens', 'desc'=>'Fresh daily picks'],
            ['img'=>'/images/fruits.png',     'name'=>'Fresh Fruits',        'desc'=>'Tropical & local'],
            ['img'=>'/images/eggs.png',       'name'=>'Eggs & Dairy',        'desc'=>'Farm fresh'],
            ['img'=>'/images/rice.png',       'name'=>'Grains & Cereals',    'desc'=>'Rice, maize, flour'],
            ['img'=>'/images/spices.png',     'name'=>'Spices & Herbs',      'desc'=>'Authentic flavours'],
            ['img'=>'/images/cookingoil.png', 'name'=>'Cooking Oil',         'desc'=>'All varieties'],
        ];
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach ($categories as $cat)
            <div class="group relative rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-shadow duration-300 aspect-square cursor-pointer">
                <img src="{{ $cat['img'] }}" alt="{{ $cat['name'] }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                     onerror="this.parentElement.classList.add('bg-gradient-to-br','from-primary','to-primary-dark')">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-3 sm:p-4">
                    <p class="text-white font-bold text-xs sm:text-sm leading-tight">{{ $cat['name'] }}</p>
                    <p class="text-white/70 text-xs mt-0.5 hidden sm:block">{{ $cat['desc'] }}</p>
                </div>
            </div>
            @endforeach
            {{-- CTA tile --}}
            <div class="group relative rounded-2xl overflow-hidden bg-gradient-to-br from-primary to-primary-dark shadow-sm hover:shadow-xl transition-shadow duration-300 aspect-square cursor-pointer flex items-center justify-center">
                <div class="text-center p-3 sm:p-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-2 sm:mb-3">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </div>
                    <p class="text-white font-bold text-xs sm:text-sm">More Categories</p>
                    <p class="text-white/70 text-xs mt-1 hidden sm:block">In the app</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FEATURES --}}
<section id="features" class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 sm:mb-14">
            <span class="text-primary font-semibold text-xs sm:text-sm uppercase tracking-widest">Why Sokoni?</span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mt-2 mb-3 sm:mb-4">
                Everything you need,<br class="hidden sm:block"> in one marketplace
            </h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm sm:text-base">Built for Tanzania — connecting local vendors with customers across the country.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @php
            $features = [
                ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','color'=>'bg-green-100 text-green-600','title'=>'Trusted Vendors','desc'=>'Every vendor is verified and approved before going live. Shop with confidence knowing you\'re buying from legitimate local businesses.'],
                ['icon'=>'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z','color'=>'bg-orange-100 text-primary','title'=>'Easy Shopping','desc'=>'Browse categories, add to cart, and checkout in minutes. Pay cash on delivery or via mobile money.'],
                ['icon'=>'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z','color'=>'bg-blue-100 text-blue-600','title'=>'Secure Payments','desc'=>'Pay safely via M-Pesa, Airtel Money, or cash on delivery. All transactions are encrypted end-to-end.'],
                ['icon'=>'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z','color'=>'bg-purple-100 text-purple-600','title'=>'Real-time Tracking','desc'=>'Follow your order live from the vendor\'s preparation through to delivery at your doorstep.'],
                ['icon'=>'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z','color'=>'bg-yellow-100 text-yellow-600','title'=>'Wide Categories','desc'=>'Fresh vegetables, fruits, meat, spices, grains, cooking oil — all the daily essentials, all in one place.'],
                ['icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','color'=>'bg-red-100 text-red-500','title'=>'Vendor Earnings','desc'=>'Vendors get real-time earnings tracking and can withdraw to mobile money anytime.'],
            ];
            @endphp
            @foreach ($features as $f)
            <div class="gradient-card border border-orange-100 rounded-2xl p-5 sm:p-6 hover:shadow-md transition-shadow duration-200">
                <div class="w-11 h-11 sm:w-12 sm:h-12 {{ $f['color'] }} rounded-xl flex items-center justify-center mb-3 sm:mb-4">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/></svg>
                </div>
                <h3 class="text-sm sm:text-base font-bold text-gray-900 mb-1.5 sm:mb-2">{{ $f['title'] }}</h3>
                <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- HOW IT WORKS --}}
<section id="how-it-works" class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 sm:mb-12">
            <span class="text-primary font-semibold text-xs sm:text-sm uppercase tracking-widest">Simple Process</span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mt-2 mb-3 sm:mb-4">How Sokoni works</h2>
        </div>
        <div class="flex justify-center mb-10 sm:mb-12">
            <div class="inline-flex bg-gray-100 rounded-xl p-1">
                <button id="tab-buyer"  onclick="switchTab('buyer')"  class="tab-btn px-4 sm:px-6 py-2.5 rounded-lg text-sm font-semibold transition-all bg-primary text-white shadow-sm min-h-11">For Buyers</button>
                <button id="tab-vendor" onclick="switchTab('vendor')" class="tab-btn px-4 sm:px-6 py-2.5 rounded-lg text-sm font-semibold transition-all text-gray-500 min-h-11">For Vendors</button>
            </div>
        </div>
        @php
        $buyerSteps = [
            ['num'=>'01','icon'=>'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4','title'=>'Download the App','desc'=>'Get Sokoni from the Play Store and create your free account in under 2 minutes.'],
            ['num'=>'02','icon'=>'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z','title'=>'Browse Products','desc'=>'Explore hundreds of products from local vendors. Filter by category, vendor, or price.'],
            ['num'=>'03','icon'=>'M3 3h2l.4 2M7 13h10l4-8H5.4','title'=>'Add to Cart & Pay','desc'=>'Add your favourites to cart, choose your delivery address, and pay by mobile money or cash.'],
            ['num'=>'04','icon'=>'M5 13l4 4L19 7','title'=>'Get Delivered','desc'=>'Track your order live and receive it fresh at your doorstep. Then rate your experience!'],
        ];
        $vendorSteps = [
            ['num'=>'01','icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z','title'=>'Register & Apply','desc'=>'Create an account and submit your vendor application with your business details.'],
            ['num'=>'02','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','title'=>'Get Approved','desc'=>'Our team reviews your application within 24 hours and activates your vendor dashboard.'],
            ['num'=>'03','icon'=>'M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z','title'=>'List Products','desc'=>'Upload your products with images, prices, and stock levels. Go live instantly.'],
            ['num'=>'04','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2','title'=>'Earn & Grow','desc'=>'Receive orders, manage deliveries, and withdraw your earnings directly to mobile money.'],
        ];
        @endphp
        <div id="steps-buyer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6">
            @foreach ($buyerSteps as $i => $step)
            <div class="text-center {{ $i < 3 ? 'step-connector' : '' }}">
                <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center mx-auto mb-4 shadow-md shadow-orange-200 relative z-10">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/></svg>
                </div>
                <span class="block text-xs font-black text-primary tracking-widest mb-1.5">{{ $step['num'] }}</span>
                <h3 class="font-bold text-gray-900 mb-2 text-sm">{{ $step['title'] }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed max-w-xs mx-auto">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
        <div id="steps-vendor" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6">
            @foreach ($vendorSteps as $i => $step)
            <div class="text-center {{ $i < 3 ? 'step-connector' : '' }}">
                <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center mx-auto mb-4 shadow-md shadow-orange-200 relative z-10">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/></svg>
                </div>
                <span class="block text-xs font-black text-primary tracking-widest mb-1.5">{{ $step['num'] }}</span>
                <h3 class="font-bold text-gray-900 mb-2 text-sm">{{ $step['title'] }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed max-w-xs mx-auto">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- VENDOR CTA --}}
<section id="vendors" class="py-16 sm:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-primary to-primary-dark rounded-3xl overflow-hidden shadow-xl shadow-orange-200">
            <div class="grid lg:grid-cols-2 items-center">
                {{-- Left: copy --}}
                <div class="p-6 sm:p-10 lg:p-14">
                    <span class="inline-block bg-white/20 text-white text-xs font-bold px-3 py-1.5 rounded-full mb-4 sm:mb-5 uppercase tracking-wider">Sell on Sokoni</span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white mb-3 sm:mb-4 leading-tight">Turn your business<br>into a digital store</h2>
                    <p class="text-orange-100 mb-6 sm:mb-8 leading-relaxed text-sm sm:text-base max-w-md">Join hundreds of vendors already selling on Sokoni. Reach thousands of customers across Tanzania, manage your orders, and grow your earnings — all from your phone.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6 sm:mb-8">
                        @php
                        $perks=[
                            ['icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2','text'=>'Earn & withdraw instantly'],
                            ['icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10','text'=>'Real-time sales dashboard'],
                            ['icon'=>'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z','text'=>'Easy product management'],
                            ['icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857','text'=>'Reach 50,000+ customers'],
                        ];
                        @endphp
                        @foreach ($perks as $perk)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $perk['icon'] }}"/></svg>
                            </div>
                            <span class="text-xs sm:text-sm text-orange-100 font-medium">{{ $perk['text'] }}</span>
                        </div>
                        @endforeach
                    </div>
                    <a href="#download" class="inline-flex items-center gap-2 bg-white text-primary font-bold px-6 sm:px-7 py-3.5 rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-sm sm:text-base min-h-[52px]">
                        Apply to Sell Now
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
                {{-- Right: vendor dashboard preview (shown on all screens) --}}
                <div class="flex items-center justify-center p-6 sm:p-10 lg:p-14 pt-0 sm:pt-0 lg:pt-14">
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-5 sm:p-7 w-full max-w-sm border border-white/20">
                        <div class="flex items-center gap-3 mb-5 sm:mb-6">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-white text-sm">Vendor Dashboard</p>
                                <p class="text-orange-200 text-xs">Manage your store</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="bg-white/10 rounded-xl p-3 sm:p-3.5">
                                <p class="text-orange-200 text-xs mb-1">This Month's Earnings</p>
                                <p class="text-white text-xl sm:text-2xl font-black">TZS 450,000</p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-white/10 rounded-xl p-3 text-center">
                                    <p class="text-white text-lg font-black">24</p>
                                    <p class="text-orange-200 text-xs">Orders</p>
                                </div>
                                <div class="bg-white/10 rounded-xl p-3 text-center">
                                    <p class="text-white text-lg font-black">4.8&#9733;</p>
                                    <p class="text-orange-200 text-xs">Rating</p>
                                </div>
                            </div>
                            <div class="bg-white/20 rounded-xl p-3 flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full shrink-0"></span>
                                <p class="text-white text-xs font-medium">Store is Open</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- DOWNLOAD --}}
<section id="download" class="py-20 sm:py-24 bg-white">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 text-center">
        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-primary rounded-2xl sm:rounded-3xl flex items-center justify-center mx-auto mb-5 sm:mb-6 shadow-lg shadow-orange-200">
            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        </div>
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mb-3 sm:mb-4">Get the Sokoni app</h2>
        <p class="text-gray-500 mb-8 sm:mb-10 max-w-md mx-auto leading-relaxed text-sm sm:text-base">Start shopping from trusted local vendors today. Free to download, free to use — always.</p>
        <a href="#" class="inline-flex items-center gap-3 sm:gap-3.5 bg-gray-900 text-white font-semibold px-6 sm:px-8 py-3.5 sm:py-4 rounded-2xl hover:bg-gray-800 transition-colors shadow-lg min-h-[60px]">
            <svg class="w-6 h-6 sm:w-7 sm:h-7 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.7 9.05 7.4c1.39.07 2.36.74 3.16.75.96-.08 1.89-.77 3.14-.83 2.58.24 4.39 1.64 4.39 5.15-2.22.95-3.32 3.02-2.69 5.81zM12 7.3c-.13-2.5 1.96-4.6 4.3-4.8.24 2.5-2.22 4.8-4.3 4.8z"/></svg>
            <div class="text-left">
                <p class="text-xs text-gray-400 leading-none mb-0.5">Download on</p>
                <p class="text-base sm:text-lg font-bold leading-tight">Google Play</p>
            </div>
        </a>
        <p class="text-xs text-gray-400 mt-4">Requires Android 6.0 or higher</p>
    </div>
</section>

{{-- FOOTER --}}
<footer class="bg-gray-900 text-gray-400 pt-12 sm:pt-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8 pb-10 border-b border-gray-800">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2 mb-4">
                    <img src="/images/sokoniicon.jpeg" alt="Sokoni" class="w-9 h-9 rounded-xl object-cover">
                    <span class="text-xl font-bold text-white">Sokoni</span>
                </div>
                <p class="text-sm leading-relaxed max-w-xs mb-5">Tanzania's multi-vendor marketplace connecting local vendors with customers across the country.</p>
                <div class="flex gap-3">
                    <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-primary rounded-lg flex items-center justify-center transition-colors group" aria-label="Facebook">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-primary rounded-lg flex items-center justify-center transition-colors group" aria-label="Instagram">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-primary rounded-lg flex items-center justify-center transition-colors group" aria-label="Twitter / X">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Marketplace</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="#features"     class="hover:text-primary transition-colors block py-0.5">Features</a></li>
                    <li><a href="#how-it-works" class="hover:text-primary transition-colors block py-0.5">How it Works</a></li>
                    <li><a href="#vendors"      class="hover:text-primary transition-colors block py-0.5">Sell on Sokoni</a></li>
                    <li><a href="#download"     class="hover:text-primary transition-colors block py-0.5">Download App</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Business</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('admin.login') }}" class="hover:text-primary transition-colors block py-0.5">Admin Panel</a></li>
                    <li><a href="#vendors" class="hover:text-primary transition-colors block py-0.5">Vendor Portal</a></li>
                    <li><a href="#"        class="hover:text-primary transition-colors block py-0.5">Privacy Policy</a></li>
                    <li><a href="#"        class="hover:text-primary transition-colors block py-0.5">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        <div class="py-6 safe-b flex flex-col sm:flex-row items-center justify-between gap-2 text-xs">
            <p>&copy; {{ date('Y') }} Sokoni. All rights reserved.</p>
            <p>Made with <span class="text-primary">&#9829;</span> in Tanzania @mktech </p>
        </div>
    </div>
</footer>

<script>
    // Mobile menu toggle
    var menuBtn   = document.getElementById('menu-btn');
    var mobileMenu = document.getElementById('mobile-menu');
    var iconOpen  = document.getElementById('menu-icon-open');
    var iconClose = document.getElementById('menu-icon-close');
    menuBtn.addEventListener('click', function () {
        var isOpen = !mobileMenu.classList.contains('hidden');
        mobileMenu.classList.toggle('hidden', isOpen);
        iconOpen.classList.toggle('hidden', !isOpen);
        iconClose.classList.toggle('hidden', isOpen);
    });
    // Close menu on any link click inside it
    document.querySelectorAll('#mobile-menu a').forEach(function (a) {
        a.addEventListener('click', function () {
            mobileMenu.classList.add('hidden');
            iconOpen.classList.remove('hidden');
            iconClose.classList.add('hidden');
        });
    });
    // Navbar scroll shadow
    window.addEventListener('scroll', function () {
        document.getElementById('navbar').classList.toggle('shadow-md', window.scrollY > 20);
    });
    // How it works tab switcher
    function switchTab(tab) {
        var isBuyer = tab === 'buyer';
        document.getElementById('steps-buyer').classList.toggle('hidden', !isBuyer);
        document.getElementById('steps-vendor').classList.toggle('hidden', isBuyer);
        var on  = 'tab-btn px-4 sm:px-6 py-2.5 rounded-lg text-sm font-semibold transition-all bg-primary text-white shadow-sm min-h-11';
        var off = 'tab-btn px-4 sm:px-6 py-2.5 rounded-lg text-sm font-semibold transition-all text-gray-500 min-h-11';
        document.getElementById('tab-buyer').className  = isBuyer  ? on : off;
        document.getElementById('tab-vendor').className = !isBuyer ? on : off;
    }
</script>

</body>
</html>
