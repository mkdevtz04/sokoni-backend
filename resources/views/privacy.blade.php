<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Privacy Policy — Sokoni</title>
    <link rel="icon" type="image/jpeg" href="/images/sokonifavicon.jpeg">
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
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/></svg>
            Legal
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-4">Privacy Policy</h1>
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
                    ['#info',      '1. Information We Collect'],
                    ['#use',       '2. How We Use Your Information'],
                    ['#share',     '3. How We Share Your Information'],
                    ['#security',  '4. Data Security'],
                    ['#retention', '5. Data Retention'],
                    ['#rights',    '6. Your Rights'],
                    ['#cookies',   '7. Cookies'],
                    ['#children',  '8. Children\'s Privacy'],
                    ['#changes',   '9. Changes to This Policy'],
                    ['#contact',   '10. Contact Us'],
                ] as [$href, $label])
                <li>
                    <a href="{{ $href }}" class="text-gray-600 hover:text-primary transition-colors">{{ $label }}</a>
                </li>
                @endforeach
            </ul>
        </div>

        <div class="prose">
            <p>Sokoni ("we", "our", or "us") operates the Sokoni mobile application and website (collectively, the "Platform"). This Privacy Policy explains how we collect, use, and protect your personal information when you use our Platform.</p>
            <p>By using Sokoni, you agree to the collection and use of information in accordance with this policy.</p>

            <h2 id="info">1. Information We Collect</h2>
            <h3>Information you provide directly</h3>
            <ul>
                <li>Name, phone number, and email address when you register</li>
                <li>Delivery address and location details</li>
                <li>Profile photo (optional)</li>
                <li>Business information (for vendors: business name, description, logo)</li>
                <li>Payment information (processed securely via PalmPesa — we do not store card or mobile money credentials)</li>
                <li>Messages sent through our in-app chat feature</li>
                <li>Reviews and ratings you submit</li>
            </ul>
            <h3>Information collected automatically</h3>
            <ul>
                <li>Device information (model, OS version, app version)</li>
                <li>IP address and general location</li>
                <li>App usage data (pages visited, features used, time spent)</li>
                <li>Push notification tokens (Firebase Cloud Messaging)</li>
                <li>Log data (errors, crashes, performance data)</li>
            </ul>

            <h2 id="use">2. How We Use Your Information</h2>
            <p>We use your information to:</p>
            <ul>
                <li>Create and manage your account</li>
                <li>Process and fulfill your orders</li>
                <li>Connect buyers with vendors and delivery drivers</li>
                <li>Send order confirmations, status updates, and notifications</li>
                <li>Verify OTP codes during registration and login</li>
                <li>Process vendor payments and withdrawals</li>
                <li>Improve the Platform through analytics</li>
                <li>Respond to your customer support requests</li>
                <li>Detect and prevent fraud or abuse</li>
                <li>Comply with legal obligations</li>
            </ul>

            <h2 id="share">3. How We Share Your Information</h2>
            <p>We do not sell your personal information. We share it only in the following circumstances:</p>
            <ul>
                <li><strong>Vendors:</strong> When you place an order, your name, delivery address, and phone number are shared with the vendor to fulfill your order.</li>
                <li><strong>Delivery Drivers:</strong> Your delivery address and first name are shared with the assigned driver to complete delivery.</li>
                <li><strong>Payment Processor:</strong> Payments are processed by PalmPesa. We share only the transaction amount and reference number.</li>
                <li><strong>SMS Provider:</strong> Your phone number is shared with our SMS provider (Kilakona) solely to deliver OTP verification codes.</li>
                <li><strong>Legal Compliance:</strong> We may disclose information if required by law, court order, or governmental authority.</li>
            </ul>

            <h2 id="security">4. Data Security</h2>
            <p>We implement industry-standard security measures to protect your data:</p>
            <ul>
                <li>All data transmission is encrypted using HTTPS/TLS</li>
                <li>Passwords are hashed using bcrypt — we never store plain-text passwords</li>
                <li>API access requires authenticated tokens (Laravel Sanctum)</li>
                <li>Database access is restricted to authorised personnel only</li>
            </ul>
            <p>Despite these measures, no system is 100% secure. Please keep your account password confidential and contact us immediately if you suspect unauthorised access.</p>

            <h2 id="retention">5. Data Retention</h2>
            <p>We retain your personal information for as long as your account is active or as needed to provide services. You may request deletion of your account and associated data at any time by contacting us at <a href="mailto:support@sokoniapp.co.tz">support@sokoniapp.co.tz</a>. Certain data may be retained for legal, tax, or fraud-prevention purposes.</p>

            <h2 id="rights">6. Your Rights</h2>
            <p>You have the right to:</p>
            <ul>
                <li><strong>Access</strong> the personal data we hold about you</li>
                <li><strong>Correct</strong> inaccurate data via your Profile settings</li>
                <li><strong>Delete</strong> your account and personal data</li>
                <li><strong>Withdraw consent</strong> for push notifications at any time in your device settings</li>
                <li><strong>Opt out</strong> of marketing communications</li>
            </ul>
            <p>To exercise any of these rights, contact us at <a href="mailto:support@sokoniapp.co.tz">support@sokoniapp.co.tz</a>.</p>

            <h2 id="cookies">7. Cookies</h2>
            <p>Our mobile app does not use browser cookies. Our website uses essential cookies only — for session management on the admin panel. No tracking or advertising cookies are used.</p>

            <h2 id="children">8. Children's Privacy</h2>
            <p>Our Platform is not intended for children under the age of 13. We do not knowingly collect personal information from children. If you believe a child has provided us with personal data, please contact us immediately.</p>

            <h2 id="changes">9. Changes to This Policy</h2>
            <p>We may update this Privacy Policy from time to time. We will notify you of significant changes via in-app notification or email. The "Last updated" date at the top of this page reflects the most recent revision. Continued use of the Platform after changes constitutes your acceptance of the updated policy.</p>

            <h2 id="contact">10. Contact Us</h2>
            <p>If you have any questions about this Privacy Policy, please contact us:</p>
            <ul>
                <li><strong>Email:</strong> <a href="mailto:support@sokoniapp.co.tz">support@sokoniapp.co.tz</a></li>
                <li><strong>Website:</strong> <a href="https://www.sokoniapp.co.tz">www.sokoniapp.co.tz</a></li>
                <li><strong>Location:</strong> Tanzania</li>
            </ul>
        </div>

        {{-- Back to home --}}
        <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-primary transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Home
            </a>
            <a href="{{ route('terms') }}"
               class="inline-flex items-center gap-2 text-sm font-medium text-primary hover:text-primary-dark transition-colors">
                Terms & Conditions
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
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
                    <li><a href="{{ route('privacy') }}" class="text-primary transition-colors block py-0.5">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}"   class="hover:text-primary transition-colors block py-0.5">Terms of Service</a></li>
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
