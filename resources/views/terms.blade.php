<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Terms &amp; Conditions - Sokoni</title>
    <link rel="icon" type="image/jpeg" href="/images/sokonifavicon.jpeg">
    <meta name="google-site-verification" content="K7bsxOdc-a5BQ5c0OfT0-hbDFvjlTMpbM6O56swmAOY" />
    <meta name="description" content="Read Sokoni's Terms and Conditions for customers, vendors and delivery partners using the Sokoni marketplace platform.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ route('terms') }}">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="Sokoni">
    <meta property="og:title" content="Terms &amp; Conditions - Sokoni">
    <meta property="og:description" content="Terms for customers, vendors and delivery partners using the Sokoni marketplace platform.">
    <meta property="og:url" content="{{ route('terms') }}">
    <meta property="og:image" content="{{ asset('images/sokoniicon.jpeg') }}">
    <meta name="twitter:card" content="summary">
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
        nav a{transition:color .2s}
        .safe-x{padding-left:max(1rem,env(safe-area-inset-left));padding-right:max(1rem,env(safe-area-inset-right))}
        .safe-b{padding-bottom:max(2rem,env(safe-area-inset-bottom))}
        button,a{-webkit-tap-highlight-color:transparent}
        #mobile-menu{transition:max-height .25s ease}
        .prose h2{font-size:1.2rem;font-weight:700;color:#111827;margin-top:2rem;margin-bottom:.75rem}
        .prose h3{font-size:1rem;font-weight:600;color:#374151;margin-top:1.25rem;margin-bottom:.5rem}
        .prose p{color:#6B7280;font-size:.9rem;line-height:1.75;margin-bottom:.9rem}
        .prose ul{color:#6B7280;font-size:.9rem;line-height:1.75;margin-bottom:.9rem;list-style:disc;padding-left:1.25rem}
        .prose ul li{margin-bottom:.25rem}
        .prose a{color:#FF6600;text-decoration:underline}
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">

{{-- NAVBAR --}}
<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100 transition-shadow duration-300">
    <div class="max-w-7xl mx-auto safe-x sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
            <img src="/images/sokoniicon.jpeg" alt="Sokoni" class="w-9 h-9 rounded-xl object-cover shadow-sm">
            <span class="text-xl font-bold text-gray-900">Sokoni</span>
        </a>
        <div class="hidden md:flex items-center gap-8">
            <a href="{{ route('home') }}#features"     class="text-sm font-medium text-gray-600 hover:text-primary">Features</a>
            <a href="{{ route('home') }}#how-it-works" class="text-sm font-medium text-gray-600 hover:text-primary">How it works</a>
            <a href="{{ route('home') }}#vendors"      class="text-sm font-medium text-gray-600 hover:text-primary">For Vendors</a>
            <a href="{{ route('become-driver') }}"     class="text-sm font-medium text-gray-600 hover:text-primary">For Drivers</a>
            <a href="{{ route('admin.login') }}"       class="text-sm font-medium text-gray-500 hover:text-primary">Admin</a>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}#download" class="hidden sm:inline-flex items-center gap-2 bg-primary text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm hover:bg-primary-dark transition-colors min-h-11">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Download App
            </a>
            <button id="menu-btn" class="md:hidden p-2.5 rounded-xl text-gray-600 hover:bg-gray-100 min-w-11 min-h-11 flex items-center justify-center">
                <svg id="menu-icon-open"  class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg id="menu-icon-close" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    <div id="mobile-menu" class="md:hidden hidden border-t border-gray-100 bg-white safe-x py-3 space-y-0.5">
        <a href="{{ route('home') }}#features"     class="flex items-center py-3 text-sm font-medium text-gray-700 hover:text-primary border-b border-gray-50">Features</a>
        <a href="{{ route('home') }}#how-it-works" class="flex items-center py-3 text-sm font-medium text-gray-700 hover:text-primary border-b border-gray-50">How it works</a>
        <a href="{{ route('home') }}#vendors"      class="flex items-center py-3 text-sm font-medium text-gray-700 hover:text-primary border-b border-gray-50">For Vendors</a>
        <a href="{{ route('become-driver') }}"     class="flex items-center py-3 text-sm font-medium text-gray-700 hover:text-primary border-b border-gray-50">For Drivers</a>
        <a href="{{ route('admin.login') }}"       class="flex items-center py-3 text-sm font-medium text-gray-500 hover:text-primary border-b border-gray-50">Admin Login</a>
        <a href="{{ route('home') }}#download" class="flex items-center justify-center mt-3 bg-primary text-white text-sm font-semibold py-3.5 rounded-xl min-h-12">Download App</a>
    </div>
</nav>

{{-- PAGE HEADER --}}
<section class="gradient-hero pt-28 pb-14">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <div class="inline-flex items-center gap-2 bg-white/20 text-white text-xs font-semibold px-4 py-2 rounded-full mb-5 uppercase tracking-widest">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Legal
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-4">Terms &amp; Conditions</h1>
        <p class="text-orange-100 text-sm sm:text-base">Last updated: {{ date('d F Y') }}</p>
    </div>
</section>

{{-- WAVE --}}
<div class="bg-gray-50 overflow-hidden -mt-1">
    <svg viewBox="0 0 1440 48" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" class="w-full">
        <path d="M0 48L60 40C120 32 240 16 360 12C480 8 600 16 720 20C840 24 960 24 1080 20C1200 16 1320 8 1380 4L1440 0V48H0Z" fill="#FF6600" opacity="0.08"/>
        <path d="M0 48L1440 48L1440 0C1200 20 960 32 720 28C480 24 240 8 0 48Z" fill="white"/>
    </svg>
</div>

{{-- CONTENT --}}
<section class="bg-white py-10 sm:py-14">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Quick nav --}}
        <div class="bg-orange-50 border border-orange-100 rounded-2xl p-5 mb-10">
            <p class="text-xs font-bold text-primary uppercase tracking-wider mb-3">Contents</p>
            <ul class="space-y-1.5 text-sm">
                @foreach ([
                    ['#acceptance',  '1. Acceptance of Terms'],
                    ['#account',     '2. Account Registration'],
                    ['#platform',    '3. Use of the Platform'],
                    ['#orders',      '4. Orders & Payments'],
                    ['#vendors',     '5. Vendor Terms'],
                    ['#drivers',     '6. Driver Terms'],
                    ['#prohibited',  '7. Prohibited Activities'],
                    ['#ip',          '8. Intellectual Property'],
                    ['#liability',   '9. Limitation of Liability'],
                    ['#termination', '10. Termination'],
                    ['#changes',     '11. Changes to Terms'],
                    ['#governing',   '12. Governing Law'],
                    ['#contact',     '13. Contact Us'],
                ] as [$href, $label])
                <li>
                    <a href="{{ $href }}" class="text-gray-600 hover:text-primary transition-colors">{{ $label }}</a>
                </li>
                @endforeach
            </ul>
        </div>

        <div class="prose">
            <p>Welcome to Sokoni. By accessing or using our mobile application or website (the "Platform"), you agree to be bound by these Terms & Conditions ("Terms"). Please read them carefully before using our services.</p>
            <p>If you do not agree with any part of these Terms, you must not use the Platform.</p>

            <h2 id="acceptance">1. Acceptance of Terms</h2>
            <p>By creating an account or using the Platform in any way, you confirm that you are at least 18 years of age (or the legal age of majority in your jurisdiction), and that you agree to these Terms and our <a href="{{ route('privacy') }}">Privacy Policy</a>.</p>

            <h2 id="account">2. Account Registration</h2>
            <ul>
                <li>You must provide accurate and complete information during registration.</li>
                <li>You are responsible for maintaining the security of your account credentials.</li>
                <li>You must notify us immediately of any unauthorised use of your account.</li>
                <li>Each person may hold only one customer account. Duplicate accounts may be suspended.</li>
                <li>You must verify your phone number via OTP before your account is activated.</li>
            </ul>

            <h2 id="platform">3. Use of the Platform</h2>
            <p>You agree to use the Platform only for lawful purposes and in accordance with these Terms. The Platform allows you to:</p>
            <ul>
                <li>Browse and purchase products from registered vendors</li>
                <li>Track orders in real-time</li>
                <li>Communicate with vendors via in-app chat</li>
                <li>Submit reviews and ratings for vendors and products</li>
                <li>Manage your delivery addresses and profile</li>
            </ul>

            <h2 id="orders">4. Orders & Payments</h2>
            <h3>Placing Orders</h3>
            <ul>
                <li>Orders are confirmed only after successful payment.</li>
                <li>Product availability and prices are set by individual vendors and may change without notice.</li>
                <li>You are responsible for providing a correct and accessible delivery address.</li>
            </ul>
            <h3>Payments</h3>
            <ul>
                <li>We accept Mobile Money payments (M-Pesa, Airtel Money, Tigo Pesa, HaloPesa) processed via PalmPesa.</li>
                <li>All prices are displayed in Tanzanian Shillings (TZS).</li>
                <li>A platform commission of 10% is deducted from each vendor's earnings per order.</li>
            </ul>
            <h3>Cancellations & Refunds</h3>
            <ul>
                <li>You may cancel an order before the vendor confirms it.</li>
                <li>Once confirmed, cancellations are at the vendor's discretion.</li>
                <li>Refunds for failed payments are processed within 3–5 business days via the original payment method.</li>
                <li>For disputes about received goods, contact us within 24 hours of delivery.</li>
            </ul>

            <h2 id="vendors">5. Vendor Terms</h2>
            <p>By registering as a vendor on Sokoni, you agree to the following additional terms:</p>
            <ul>
                <li>You must provide accurate business information and maintain it up to date.</li>
                <li>All products listed must be legal and accurately described, priced, and stocked.</li>
                <li>You are responsible for preparing and handing over orders in a timely manner.</li>
                <li>Sokoni charges a 10% commission on each completed order's subtotal.</li>
                <li>Earnings are credited to your wallet after order delivery is confirmed.</li>
                <li>Withdrawal requests are processed manually by the admin within 1–2 business days.</li>
                <li>Sokoni reserves the right to suspend vendor accounts that repeatedly receive poor ratings or violate these Terms.</li>
            </ul>

            <h2 id="drivers">6. Driver Terms</h2>
            <ul>
                <li>Drivers are registered by platform administrators only.</li>
                <li>Drivers must maintain a valid driving licence and comply with all traffic laws.</li>
                <li>Drivers are responsible for safe and timely delivery of orders.</li>
                <li>Any mishandling of goods or fraudulent delivery reports may result in account suspension.</li>
            </ul>

            <h2 id="prohibited">7. Prohibited Activities</h2>
            <p>You must not:</p>
            <ul>
                <li>Use the Platform for any illegal purpose or in violation of any law</li>
                <li>List or purchase prohibited, counterfeit, or stolen goods</li>
                <li>Post false reviews or manipulate ratings</li>
                <li>Attempt to bypass payment systems or defraud other users</li>
                <li>Impersonate another person or entity</li>
                <li>Use bots, scrapers, or automated tools to access the Platform</li>
                <li>Interfere with or disrupt the integrity or performance of the Platform</li>
                <li>Harass, abuse, or threaten other users, vendors, or drivers</li>
            </ul>
            <p>Violation of these rules may result in immediate account suspension and legal action.</p>

            <h2 id="ip">8. Intellectual Property</h2>
            <p>All content on the Platform — including the name "Sokoni", logos, design, text, and software — is owned by or licensed to us and is protected by intellectual property laws. You may not copy, reproduce, or distribute any content without our prior written consent.</p>
            <p>Vendors retain ownership of their own product images and descriptions but grant Sokoni a non-exclusive licence to display them on the Platform.</p>

            <h2 id="liability">9. Limitation of Liability</h2>
            <p>To the fullest extent permitted by law:</p>
            <ul>
                <li>Sokoni is a marketplace platform connecting buyers and vendors — we are not the seller of any products.</li>
                <li>We are not liable for the quality, safety, legality, or accuracy of products listed by vendors.</li>
                <li>We are not liable for any loss of profits, data, or business arising from use of or inability to use the Platform.</li>
                <li>Our total liability to you for any claim shall not exceed the amount you paid for the specific order in question.</li>
            </ul>

            <h2 id="termination">10. Termination</h2>
            <p>We reserve the right to suspend or terminate your account at any time, without notice, if you violate these Terms or engage in behaviour harmful to the Platform or its users. You may delete your account at any time by contacting our support team.</p>

            <h2 id="changes">11. Changes to Terms</h2>
            <p>We may update these Terms at any time. We will notify you of material changes via in-app notification or email. Continued use of the Platform after the effective date of changes constitutes your acceptance of the updated Terms.</p>

            <h2 id="governing">12. Governing Law</h2>
            <p>These Terms are governed by and construed in accordance with the laws of the United Republic of Tanzania. Any disputes arising from these Terms shall be subject to the exclusive jurisdiction of the courts of Tanzania.</p>

            <h2 id="contact">13. Contact Us</h2>
            <p>If you have any questions about these Terms, please contact us:</p>
            <ul>
                <li><strong>Email:</strong> <a href="mailto:support@sokoniapp.co.tz">support@sokoniapp.co.tz</a></li>
                <li><strong>Website:</strong> <a href="https://www.sokoniapp.co.tz">www.sokoniapp.co.tz</a></li>
                <li><strong>Location:</strong> Tanzania</li>
            </ul>
        </div>

        {{-- Back / nav links --}}
        <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-primary transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Home
            </a>
            <a href="{{ route('privacy') }}"
               class="inline-flex items-center gap-2 text-sm font-medium text-primary hover:text-primary-dark transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Privacy Policy
            </a>
        </div>
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
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Marketplace</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('home') }}#features"     class="hover:text-primary transition-colors block py-0.5">Features</a></li>
                    <li><a href="{{ route('home') }}#how-it-works" class="hover:text-primary transition-colors block py-0.5">How it Works</a></li>
                    <li><a href="{{ route('home') }}#vendors"      class="hover:text-primary transition-colors block py-0.5">Sell on Sokoni</a></li>
                    <li><a href="{{ route('home') }}#download"     class="hover:text-primary transition-colors block py-0.5">Download App</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-4">Legal</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('privacy') }}" class="hover:text-primary transition-colors block py-0.5">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}"   class="text-primary transition-colors block py-0.5">Terms of Service</a></li>
                    <li><a href="{{ route('admin.login') }}" class="hover:text-primary transition-colors block py-0.5">Admin Panel</a></li>
                </ul>
            </div>
        </div>
        <div class="py-6 safe-b flex flex-col sm:flex-row items-center justify-between gap-2 text-xs">
            <p>&copy; {{ date('Y') }} Sokoni. All rights reserved.</p>
            <p>Made with <span class="text-primary">&#9829;</span> in Tanzania</p>
        </div>
    </div>
</footer>

<script>
    var menuBtn    = document.getElementById('menu-btn');
    var mobileMenu = document.getElementById('mobile-menu');
    var iconOpen   = document.getElementById('menu-icon-open');
    var iconClose  = document.getElementById('menu-icon-close');
    menuBtn.addEventListener('click', function () {
        var isOpen = !mobileMenu.classList.contains('hidden');
        mobileMenu.classList.toggle('hidden', isOpen);
        iconOpen.classList.toggle('hidden', !isOpen);
        iconClose.classList.toggle('hidden', isOpen);
    });
    window.addEventListener('scroll', function () {
        document.getElementById('navbar').classList.toggle('shadow-md', window.scrollY > 20);
    });
</script>
</body>
</html>
