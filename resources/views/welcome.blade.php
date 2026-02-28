<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARIFAH Gym Makassar - Power & Progress</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Poppins:wght@400;600;800&display=swap');
        
        /* Base Styles */
        body { 
            font-family: 'Inter', sans-serif; 
            overflow-x: hidden; 
            transition: all 0.3s ease;
            /* Hide scrollbar but keep scroll functionality */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* Internet Explorer 10+ */
        }
        
        /* Hide scrollbar for Webkit browsers (Chrome, Safari, Edge) */
        body::-webkit-scrollbar {
            display: none;
        }
        
        html { 
            scroll-behavior: smooth;
            /* Hide scrollbar but keep scroll functionality */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* Internet Explorer 10+ */
        }
        
        /* Hide scrollbar for Webkit browsers on html */
        html::-webkit-scrollbar {
            display: none;
        }
        
        /* Dark Mode Styles (Default) */
        body.dark {
            background-color: rgb(3 7 18);
            color: white;
        }
        
        /* Light Mode Styles */
        body.light {
            background-color: rgb(255 255 255);
            color: rgb(15 23 42);
        }
        
        /* Hero Background - Dark Mode */
        .hero-bg.dark { 
            background: linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(17,24,39,1)), 
                        url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1350&q=80'); 
            background-size: cover; 
            background-position: center; 
        }
        
        /* Hero Background - Light Mode */
        .hero-bg.light { 
            background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.4)), 
                        url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1350&q=80'); 
            background-size: cover; 
            background-position: center; 
        }
        
        /* Text Glow Effects */
        .orange-glow { text-shadow: 0 0 20px rgba(9, 146, 194, 0.5); }
        
        /* Glass Card Effects */
        .glass-card.dark { 
            background: rgba(255, 255, 255, 0.03); 
            backdrop-filter: blur(10px); 
            border: 1px solid rgba(255, 255, 255, 0.05); 
        }
        
        .glass-card.light { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px); 
            border: 1px solid rgba(0, 0, 0, 0.1); 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Override glass card border for highlighted cards */
        .glass-card.highlighted-card {
            border: 2px solid rgba(9, 146, 194, 0.5) !important;
        }
        
        body.light .glass-card.highlighted-card {
            border: 2px solid rgba(9, 146, 194, 0.7) !important;
        }
        
        /* Modern Button Effects */
        .modern-btn-primary {
            position: relative;
            overflow: hidden;
        }
        
        .modern-btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .modern-btn-primary:hover::before {
            left: 100%;
        }
        
        .modern-btn-secondary {
            position: relative;
            overflow: hidden;
        }
        
        .modern-btn-secondary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }
        
        .modern-btn-secondary:hover::before {
            left: 100%;
        }
        
        /* Custom Shadow Classes */
        .shadow-3xl {
            box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
        }
        
        /* Button Hover Effects - Legacy */
        .btn-hover { transition: all 0.3s ease; }
        .btn-hover:hover { transform: scale(1.05); box-shadow: 0 10px 20px -10px rgba(9, 146, 194, 1); }
        
        /* WhatsApp Animation */
        @keyframes wa-pulse { 
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); } 
            70% { box-shadow: 0 0 0 20px rgba(34, 197, 94, 0); } 
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); } 
        }
        .animate-wa { animation: wa-pulse 2s infinite; }
        
        /* Expired Stamp */
        .stamp-expired { 
            border: 0.2rem solid #ef4444; 
            color: #ef4444; 
            font-size: 1.5rem; 
            font-weight: 900; 
            text-transform: uppercase; 
            padding: 0.2rem 1rem; 
            transform: rotate(-15deg); 
            border-radius: 0.4rem; 
            background: rgba(255, 255, 255, 0.1); 
            backdrop-filter: blur(1px); 
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.3); 
            letter-spacing: 0.1em; 
            z-index: 50; 
            opacity: 0.9; 
        }
        
        /* Mobile Menu */
        #mobile-menu { 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
            transform: translateY(-20px); 
            opacity: 0; 
            pointer-events: none; 
            visibility: hidden; 
        }
        #mobile-menu.active { 
            transform: translateY(0); 
            opacity: 1; 
            pointer-events: auto; 
            visibility: visible; 
        }
        
        /* Promo Glow Animation */
        @keyframes pulse-orange { 
            0% { box-shadow: 0 0 0 0 rgba(9, 146, 194, 0.7); } 
            70% { box-shadow: 0 0 0 15px rgba(9, 146, 194, 0); } 
            100% { box-shadow: 0 0 0 0 rgba(9, 146, 194, 0); } 
        }
        .promo-glow { animation: pulse-orange 2s infinite; }
        
        /* Modern Theme Toggle Button - Icon Only */
        .navbar-theme-toggle {
            position: relative;
            width: 60px;
            height: 30px;
            border-radius: 15px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        
        .navbar-theme-toggle .theme-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 3;
        }
        
        .navbar-theme-toggle .sun-icon {
            left: 8px;
            color: #ffffff;
            opacity: 1;
        }
        
        .navbar-theme-toggle .moon-icon {
            right: 8px;
            color: #64748b;
            opacity: 0.3;
        }
        
        /* Light mode toggle */
        body.light .navbar-theme-toggle {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.05));
            border-color: rgba(0, 0, 0, 0.2);
        }
        
        body.light .navbar-theme-toggle .sun-icon {
            opacity: 0.3;
            color: #64748b;
        }
        
        body.light .navbar-theme-toggle .moon-icon {
            opacity: 1;
            color: #1e293b;
        }
        
        .navbar-theme-toggle:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(9, 146, 194, 0.2);
        }
        
        body.light .navbar-theme-toggle:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        /* Mobile theme toggle - smaller version */
        .mobile-theme-toggle {
            width: 50px;
            height: 25px;
            border-radius: 12.5px;
        }
        
        .mobile-theme-toggle .theme-icon {
            font-size: 11px;
        }
        
        .mobile-theme-toggle .sun-icon {
            left: 6px;
        }
        
        .mobile-theme-toggle .moon-icon {
            right: 6px;
        }
        
        /* Theme-specific text colors */
        .navbar-text {
            color: white;
            transition: color 0.3s ease;
        }
        
        .navbar-link {
            color: white;
            transition: color 0.3s ease;
        }
        
        .mobile-border {
            border-color: rgba(255, 255, 255, 0.05);
            transition: border-color 0.3s ease;
        }
        
        .hero-title {
            color: white;
            transition: color 0.3s ease;
        }
        
        .hero-subtitle {
            color: rgb(156 163 175);
            transition: color 0.3s ease;
        }
        
        .hero-secondary-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        
        /* Light mode overrides */
        body.light .navbar-text {
            color: rgb(15 23 42);
        }
        
        body.light .navbar-link {
            color: rgb(15 23 42);
        }
        
        body.light .mobile-border {
            border-color: rgba(0, 0, 0, 0.1);
        }
        
        body.light .hero-title {
            color: white;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.8), 0 4px 16px rgba(0, 0, 0, 0.6);
        }
        
        body.light .hero-subtitle {
            color: rgb(243 244 246);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
        }
        
        /* Pastikan orange-glow tetap biru di light mode */
        body.light .orange-glow {
            text-shadow: 0 0 20px rgba(9, 146, 194, 0.8) !important;
        }
        
        body.light .hero-secondary-btn {
            background: rgba(0, 0, 0, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            backdrop-filter: blur(10px);
        }
        
        body.light .hero-secondary-btn:hover {
            background: rgba(0, 0, 0, 0.8);
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        /* Hero badge styles */
        .hero-badge {
            background: rgba(9, 146, 194, 0.05);
            transition: all 0.3s ease;
        }
        
        body.light .hero-badge {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            border-color: rgba(9, 146, 194, 0.8);
            color: #0992C2;
        }
        
        /* Navbar light mode - DISABLED untuk floating navbar */
        /* body.light nav {
            background: rgba(255, 255, 255, 0.95) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        } */
        
        body.light #mobile-menu {
            background: rgba(255, 255, 255, 0.98) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        /* Dark mode mobile menu - solid background */
        body.dark #mobile-menu {
            background: rgba(0, 0, 0, 0.98) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Section backgrounds */
        .section-bg {
            background-color: rgb(3 7 18);
            transition: background-color 0.3s ease;
        }
        
        body.light .section-bg {
            background-color: rgb(255 255 255);
        }
        
        /* Section titles and subtitles */
        .section-title {
            color: white;
            transition: color 0.3s ease;
        }
        
        .section-subtitle {
            color: rgb(107 114 128);
            transition: color 0.3s ease;
        }
        
        body.light .section-title {
            color: rgb(15 23 42);
        }
        
        body.light .section-subtitle {
            color: rgb(71 85 105);
        }
        
        /* Pricing card elements */
        .pricing-card {
            background: rgba(3, 7, 18, 0.9);
            transition: background-color 0.3s ease;
        }
        
        .pricing-title {
            color: white;
            transition: color 0.3s ease;
        }
        
        .pricing-note {
            color: rgb(107 114 128);
            transition: color 0.3s ease;
        }
        
        body.light .pricing-card {
            background: rgba(255, 255, 255, 0.95);
        }
        
        body.light .pricing-title {
            color: rgb(15 23 42);
        }
        
        body.light .pricing-note {
            color: rgb(71 85 105);
        }
        
        /* Card elements */
        .card-title {
            color: white;
            transition: color 0.3s ease;
        }
        
        .card-subtitle {
            color: rgb(107 114 128);
            transition: color 0.3s ease;
        }
        
        .card-price-suffix {
            color: rgb(107 114 128);
            transition: color 0.3s ease;
        }
        
        .card-features {
            color: rgb(156 163 175);
            transition: color 0.3s ease;
        }
        
        body.light .card-title {
            color: rgb(15 23 42);
        }
        
        body.light .card-subtitle {
            color: rgb(71 85 105);
        }
        
        body.light .card-price-suffix {
            color: rgb(71 85 105);
        }
        
        body.light .card-features {
            color: rgb(100 116 139);
        }
        
        /* Glass card light mode borders */
        body.light .glass-card {
            border-color: rgba(0, 0, 0, 0.1) !important;
        }
        
        /* Footer styles */
        .footer-bg {
            background-color: rgb(0 0 0);
            transition: background-color 0.3s ease;
        }
        
        .footer-border {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            transition: border-color 0.3s ease;
        }
        
        .footer-title {
            color: white;
            transition: color 0.3s ease;
        }
        
        .footer-links {
            color: rgb(156 163 175);
            transition: color 0.3s ease;
        }
        
        .footer-subtitle {
            color: rgb(107 114 128);
            transition: color 0.3s ease;
        }
        
        .footer-copyright {
            color: rgb(75 85 99);
            transition: color 0.3s ease;
        }
        
        body.light .footer-bg {
            background-color: rgb(255 255 255);
        }
        
        body.light .footer-border {
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        body.light .footer-title {
            color: rgb(15 23 42);
        }
        
        body.light .footer-links {
            color: rgb(100 116 139);
        }
        
        body.light .footer-subtitle {
            color: rgb(71 85 105);
        }
        
        body.light .footer-copyright {
            color: rgb(100 116 139);
        }
        
        /* Input field styles */
        .input-field {
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }
        
        .input-border {
            border-color: rgba(255, 255, 255, 0.1);
            transition: border-color 0.3s ease;
        }
        
        .input-text {
            color: white;
            transition: color 0.3s ease;
        }
        
        .input-placeholder {
            color: rgb(107 114 128);
            transition: color 0.3s ease;
        }
        
        .input-icon {
            color: rgb(107 114 128);
            transition: color 0.3s ease;
        }
        
        body.light .input-field {
            background: rgba(0, 0, 0, 0.05);
        }
        
        body.light .input-field:focus {
            background: rgba(0, 0, 0, 0.1);
        }
        
        body.light .input-border {
            border-color: rgba(0, 0, 0, 0.1);
        }
        
        body.light .input-text {
            color: rgb(15 23 42);
        }
        
        body.light .input-placeholder {
            color: rgb(71 85 105);
        }
        
        body.light .input-icon {
            color: rgb(71 85 105);
        }
        
        /* Utility classes for easy theming */
        .theme-transition {
            transition: all 0.3s ease;
        }
        
        .theme-bg-primary {
            background-color: rgb(3 7 18);
        }
        
        body.light .theme-bg-primary {
            background-color: rgb(255 255 255);
        }
        
        .theme-text-primary {
            color: white;
        }
        
        body.light .theme-text-primary {
            color: rgb(15 23 42);
        }
        
        .theme-text-secondary {
            color: rgb(156 163 175);
        }
        
        body.light .theme-text-secondary {
            color: rgb(100 116 139);
        }
        
        /* Location button styles */
        .location-btn {
            background: white;
            color: black;
            transition: all 0.3s ease;
        }
        
        .location-btn:hover {
            background: #0992C2;
            color: white;
        }
        
        body.light .location-btn {
            background: black;
            color: white;
        }
        
        body.light .location-btn:hover {
            background: #0992C2;
            color: white;
        }
        
        /* Map iframe styles */
        .map-iframe {
            transition: all 0.3s ease;
        }
        
        body.light .map-iframe {
            filter: none !important;
            opacity: 1 !important;
        }
        
        /* Membership Plans specific styles */
        .pricing-badge {
            background: rgba(255, 255, 255, 0.05);
            transition: background-color 0.3s ease;
        }
        
        body.light .pricing-badge {
            background: rgba(0, 0, 0, 0.05);
        }
        
        .membership-card {
            transition: all 0.3s ease;
        }
        
        /* Mobile: Increased height with enhanced rounded corners */
        @media (max-width: 768px) {
            .membership-card {
                min-height: 520px;
                justify-content: space-between;
                padding: 2.5rem 1.5rem !important;
                text-align: center;
                border-radius: 3rem !important;
            }
            
            /* Better spacing for mobile card content */
            .membership-card .card-title {
                margin-bottom: 1.5rem !important;
                text-align: center;
                font-size: 4rem !important;
                line-height: 1.1 !important;
            }
            
            .membership-card .card-subtitle {
                text-align: center;
                margin-bottom: 1rem !important;
                font-size: 0.9rem !important;
                font-weight: 700 !important;
            }
            
            .membership-card .card-features {
                margin-bottom: 2rem !important;
                flex-grow: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
                text-align: left;
                padding: 0 1rem;
                font-size: 1rem;
                align-items: center;
            }
            
            .membership-card .card-features li {
                display: flex;
                align-items: center;
                justify-content: flex-start;
                margin-bottom: 0.5rem;
                font-weight: 500;
                font-size: 1rem;
                width: 100%;
                max-width: 200px;
            }
            
            .membership-card .card-features li i {
                margin-right: 0.5rem;
                width: 16px;
                flex-shrink: 0;
            }
        }
        
        /* Tablet: Moderate height increase */
        @media (min-width: 769px) and (max-width: 1024px) {
            .membership-card {
                min-height: 400px;
            }
        }
        
        .membership-card-border {
            border-color: rgba(255, 255, 255, 0.05);
            transition: border-color 0.3s ease;
        }
        
        body.light .membership-card-border {
            border-color: rgba(0, 0, 0, 0.1);
        }
        
        .feature-check-icon {
            color: rgb(107 114 128);
            transition: color 0.3s ease;
        }
        
        body.light .feature-check-icon {
            color: rgb(71 85 105);
        }
        
        /* Enhanced glass card borders for membership cards */
        body.light .glass-card.dark {
            border-color: rgba(0, 0, 0, 0.1) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        /* Preserve hover animations - Override any conflicting transitions */
        img {
            transition: transform 0.7s ease !important;
        }
        
        .group:hover img {
            transform: scale(1.1) !important;
        }
        
        .hover\\:scale-110:hover {
            transform: scale(1.1) !important;
        }
        
        .hover\\:scale-105:hover {
            transform: scale(1.05) !important;
        }
        
        .hover\\:-translate-y-2:hover {
            transform: translateY(-0.5rem) !important;
        }
        
        .hover\\:bg-white\\/20:hover {
            background-color: rgba(255, 255, 255, 0.2) !important;
        }
        
        .hover\\:bg-\\[\\#0992C2\\]:hover {
            background-color: #0992C2 !important;
        }
        
        .hover\\:text-\\[\\#0992C2\\]:hover {
            color: #0992C2 !important;
        }
        
        /* Gallery specific hover animations */
        .gallery-image {
            transition: transform 0.7s ease, filter 0.7s ease !important;
        }
        
        .gallery-overlay {
            transition: opacity 0.5s ease !important;
        }
        
        /* Facilities hover animations */
        .facility-icon {
            transition: all 0.5s ease !important;
        }
        
        .facility-card:hover .facility-icon {
            background-color: #0992C2 !important;
        }
        
        .facility-card:hover .facility-icon i {
            color: black !important;
        }
        
        /* Ensure all hover animations work properly */
        .transition-all {
            transition: all 0.3s ease !important;
        }
        
        .transition-transform {
            transition: transform 0.7s ease !important;
        }
        
        .transition-opacity {
            transition: opacity 0.5s ease !important;
        }
        
        .transition-colors {
            transition: color 0.3s ease !important;
        }
        
        .duration-500 {
            transition-duration: 0.5s !important;
        }
        
        .duration-700 {
            transition-duration: 0.7s !important;
        }
        
        /* Group hover effects */
        .group:hover .group-hover\\:scale-105 {
            transform: scale(1.05) !important;
        }
        
        .group:hover .group-hover\\:scale-110 {
            transform: scale(1.1) !important;
        }
        
        .group:hover .group-hover\\:grayscale-0 {
            filter: grayscale(0) !important;
        }
        
        .group:hover .group-hover\\:opacity-100 {
            opacity: 1 !important;
        }
        
        .group:hover .group-hover\\:opacity-0 {
            opacity: 0 !important;
        }
        
        .group:hover .group-hover\\:bg-\\[\\#0992C2\\] {
            background-color: #0992C2 !important;
        }
        
        .group:hover .group-hover\\:text-black {
            color: black !important;
        }
        
        /* Card hover animations */
        .hover\\:border-\\[\\#0992C2\\]\\/50:hover {
            border-color: rgba(9, 146, 194, 0.5) !important;
        }
        
        .hover\\:border-\\[\\#0992C2\\]\\/30:hover {
            border-color: rgba(9, 146, 194, 0.3) !important;
        }
        
        /* Pricing card highlight borders - Ensure they're always visible */
        .border-\\[\\#0992C2\\]\\/50 {
            border-color: rgba(9, 146, 194, 0.5) !important;
        }
        
        .shadow-\\[\\#0992C2\\]\\/20 {
            box-shadow: 0 25px 50px -12px rgba(9, 146, 194, 0.2) !important;
        }
        
        /* Membership card with badge styling */
        .membership-card.border-\\[\\#0992C2\\]\\/50 {
            border: 2px solid rgba(9, 146, 194, 0.5) !important;
            box-shadow: 0 25px 50px -12px rgba(9, 146, 194, 0.2), 
                        0 0 0 1px rgba(9, 146, 194, 0.1) !important;
        }
        
        /* Light mode specific overrides for highlighted cards */
        body.light .membership-card.border-\\[\\#0992C2\\]\\/50 {
            border: 2px solid rgba(9, 146, 194, 0.6) !important;
            box-shadow: 0 25px 50px -12px rgba(9, 146, 194, 0.25), 
                        0 10px 20px -5px rgba(9, 146, 194, 0.1),
                        0 0 0 1px rgba(9, 146, 194, 0.2) !important;
        }
        
        /* Promo glow animation for badges */
        .promo-glow {
            animation: pulse-orange 2s infinite !important;
        }
        
        /* Highlighted card styling - Direct CSS approach */
        .highlighted-card {
            border: 2px solid rgba(9, 146, 194, 0.5) !important;
            box-shadow: 0 25px 50px -12px rgba(9, 146, 194, 0.25) !important;
        }
        
        /* Light mode highlighted card */
        body.light .highlighted-card {
            border: 2px solid rgba(9, 146, 194, 0.7) !important;
            box-shadow: 0 25px 50px -12px rgba(9, 146, 194, 0.3), 
                        0 10px 20px -5px rgba(9, 146, 194, 0.15) !important;
        }
        
        /* Dark mode highlighted card */
        body.dark .highlighted-card {
            border: 2px solid rgba(9, 146, 194, 0.5) !important;
            box-shadow: 0 25px 50px -12px rgba(9, 146, 194, 0.25) !important;
        }
        
        /* Member Harian Styling - Dark Mode */
        .member-harian-card {
            background: linear-gradient(to right, rgba(31, 41, 55, 0.5), rgba(17, 24, 39, 0.5));
            transition: all 0.3s ease;
        }
        
        .member-harian-border {
            border-color: rgba(75, 85, 99, 0.5);
        }
        
        .member-harian-icon-bg {
            background: linear-gradient(to right, rgba(245, 158, 11, 0.2), rgba(249, 115, 22, 0.2));
        }
        
        .member-harian-icon-border {
            border-color: rgba(245, 158, 11, 0.3);
        }
        
        .member-harian-title {
            color: white;
        }
        
        .member-harian-subtitle {
            color: rgb(156, 163, 175);
        }
        
        .member-harian-type-card {
            background: linear-gradient(to right, rgba(245, 158, 11, 0.1), rgba(249, 115, 22, 0.1));
        }
        
        .member-harian-type-border {
            border-color: rgba(245, 158, 11, 0.3);
        }
        
        .member-harian-expired {
            background: linear-gradient(to right, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
            border-color: rgba(239, 68, 68, 0.3);
            color: rgb(248, 113, 113);
            transition: all 0.3s ease;
        }
        
        .member-harian-active {
            background: linear-gradient(to right, rgba(34, 197, 94, 0.1), rgba(22, 163, 74, 0.1));
        }
        
        .member-harian-info {
            background: linear-gradient(to right, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.1));
        }
        
        .member-harian-info-border {
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        /* Member Harian Styling - Light Mode */
        body.light .member-harian-card {
            background: linear-gradient(to right, rgba(255, 255, 255, 0.9), rgba(248, 250, 252, 0.9));
        }
        
        body.light .member-harian-border {
            border-color: rgba(0, 0, 0, 0.1);
        }
        
        body.light .member-harian-icon-bg {
            background: linear-gradient(to right, rgba(245, 158, 11, 0.15), rgba(249, 115, 22, 0.15));
        }
        
        body.light .member-harian-icon-border {
            border-color: rgba(245, 158, 11, 0.4);
        }
        
        body.light .member-harian-title {
            color: rgb(15, 23, 42);
        }
        
        body.light .member-harian-subtitle {
            color: rgb(71, 85, 105);
        }
        
        body.light .member-harian-type-card {
            background: linear-gradient(to right, rgba(245, 158, 11, 0.08), rgba(249, 115, 22, 0.08));
        }
        
        body.light .member-harian-type-border {
            border-color: rgba(245, 158, 11, 0.4);
        }
        
        body.light .member-harian-expired {
            background: linear-gradient(to right, rgba(239, 68, 68, 0.12), rgba(220, 38, 38, 0.12));
            border-color: rgba(239, 68, 68, 0.5);
            color: rgb(185, 28, 28);
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1), 0 2px 4px -1px rgba(239, 68, 68, 0.06);
        }
        
        body.light .member-harian-active {
            background: linear-gradient(to right, rgba(34, 197, 94, 0.08), rgba(22, 163, 74, 0.08));
        }
        
        body.light .member-harian-info {
            background: linear-gradient(to right, rgba(59, 130, 246, 0.08), rgba(37, 99, 235, 0.08));
        }
        
        body.light .member-harian-info-border {
            border-color: rgba(59, 130, 246, 0.4);
        }
        
        /* Search button text styling */
        .search-button-text {
            color: white;
            transition: color 0.3s ease;
        }
        
        /* Ensure search button text is always white in both modes */
        body.light .search-button-text {
            color: white;
        }
        
        body.dark .search-button-text {
            color: white;
        }
        
        /* Testimonial specific styling */
        .testimonial-text {
            color: rgb(156 163 175);
            transition: color 0.3s ease;
        }
        
        .testimonial-name {
            color: white;
            transition: color 0.3s ease;
        }
        
        .testimonial-role {
            color: rgb(107 114 128);
            transition: color 0.3s ease;
        }
        
        body.light .testimonial-text {
            color: rgb(51 65 85);
        }
        
        body.light .testimonial-name {
            color: rgb(15 23 42);
        }
        
        body.light .testimonial-role {
            color: rgb(71 85 105);
        }
        
        /* Expired Member Notification Styling */
        .expired-notification {
            background: linear-gradient(to right, rgba(245, 158, 11, 0.1), rgba(249, 115, 22, 0.1));
            border-color: rgba(245, 158, 11, 0.3);
            color: rgb(245, 158, 11);
            transition: all 0.3s ease;
        }
        
        body.light .expired-notification {
            background: linear-gradient(to right, rgba(245, 158, 11, 0.15), rgba(249, 115, 22, 0.15));
            border-color: rgba(245, 158, 11, 0.5);
            color: rgb(180, 83, 9);
            box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.1), 0 2px 4px -1px rgba(245, 158, 11, 0.06);
        }
        
        .expired-notification-icon {
            color: rgb(245, 158, 11);
            transition: color 0.3s ease;
        }
        
        body.light .expired-notification-icon {
            color: rgb(180, 83, 9);
        }
        
        .expired-notification-text {
            color: rgb(245, 158, 11);
            transition: color 0.3s ease;
        }
        
        body.light .expired-notification-text {
            color: rgb(146, 64, 14);
        }
        
    </style>
</head>
<body class="bg-gray-950 text-white selection:bg-[#0992C2] selection:text-white dark" id="body">



    @include('components.landing.navbar')

    <main>
        @include('components.landing.hero')
        @include('components.landing.stats')
        @include('components.landing.about')
        @include('components.landing.pricing')
        @include('components.landing.facilities')
        @include('components.landing.check-status')
        @include('components.landing.gallery')
        @include('components.landing.testimonials')
        @include('components.landing.faq')
        @include('components.landing.location')
    </main>

    @include('components.landing.footer')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Instagram Embed Script -->
    <script async src="//www.instagram.com/embed.js"></script>
    <script>
        // Initialize AOS first
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({ 
                once: true, 
                duration: 800,
                easing: 'ease-out-cubic',
                delay: 100
            });
        });

        // Theme Toggle Functionality
        const themeToggle = document.getElementById('theme-toggle');
        const mobileThemeToggle = document.getElementById('mobile-theme-toggle');
        const body = document.getElementById('body');
        
        // Check for saved theme preference or default to 'dark'
        const currentTheme = localStorage.getItem('theme') || 'dark';
        
        // Apply saved theme on page load
        function applyTheme(theme) {
            if (theme === 'light') {
                body.classList.remove('dark', 'bg-gray-950', 'text-white');
                body.classList.add('light', 'bg-white', 'text-slate-900');
                
                // Update all elements with theme-dependent classes
                updateElementsForLightMode();
            } else {
                body.classList.remove('light', 'bg-white', 'text-slate-900');
                body.classList.add('dark', 'bg-gray-950', 'text-white');
                
                // Update all elements with theme-dependent classes
                updateElementsForDarkMode();
            }
        }
        
        function updateElementsForLightMode() {
            // Update hero background
            const heroSection = document.querySelector('.hero-bg');
            if (heroSection) {
                heroSection.classList.remove('dark');
                heroSection.classList.add('light');
            }
            
            // Update glass cards
            const glassCards = document.querySelectorAll('.glass-card');
            glassCards.forEach(card => {
                card.classList.remove('dark');
                card.classList.add('light');
            });
        }
        
        function updateElementsForDarkMode() {
            // Update hero background
            const heroSection = document.querySelector('.hero-bg');
            if (heroSection) {
                heroSection.classList.remove('light');
                heroSection.classList.add('dark');
            }
            
            // Update glass cards
            const glassCards = document.querySelectorAll('.glass-card');
            glassCards.forEach(card => {
                card.classList.remove('light');
                card.classList.add('dark');
            });
        }
        
        // Apply theme on page load
        applyTheme(currentTheme);
        
        // Add transition classes after initial load to prevent flash
        setTimeout(() => {
            document.body.style.transition = 'all 0.3s ease';
            
            // Only add transitions to specific elements, not all elements
            const elementsToTransition = document.querySelectorAll(`
                .section-bg, .section-title, .section-subtitle, 
                .navbar-text, .navbar-link, .hero-title, .hero-subtitle, 
                .card-title, .card-subtitle, .card-features, .card-price-suffix,
                .pricing-title, .pricing-note, .pricing-card, .pricing-badge,
                .input-field, .input-text, .input-placeholder, .input-icon,
                .footer-bg, .footer-title, .footer-links, .footer-subtitle, .footer-copyright,
                .hero-badge, .hero-secondary-btn, .location-btn,
                .membership-card-border, .feature-check-icon
            `);
            
            elementsToTransition.forEach(el => {
                if (!el.hasAttribute('data-aos') && 
                    !el.classList.contains('aos-animate') && 
                    !el.style.transition) {
                    el.style.transition = 'color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease';
                }
            });
        }, 1000);
        
        // Theme toggle event listeners
        function handleThemeToggle() {
            const isCurrentlyDark = body.classList.contains('dark');
            const newTheme = isCurrentlyDark ? 'light' : 'dark';
            
            // Save theme preference
            localStorage.setItem('theme', newTheme);
            
            // Apply new theme
            applyTheme(newTheme);
            
            // Refresh AOS after theme change to ensure animations work
            setTimeout(() => {
                if (typeof AOS !== 'undefined') {
                    AOS.refresh();
                }
            }, 350);
        }
        
        // Add event listeners to both theme toggle buttons
        if (themeToggle) {
            themeToggle.addEventListener('click', handleThemeToggle);
        }
        if (mobileThemeToggle) {
            mobileThemeToggle.addEventListener('click', handleThemeToggle);
        }

        // WhatsApp Tooltip Animation (setiap 15 detik)
        const waTooltip = document.getElementById('wa-tooltip');
        if (waTooltip) {
            setInterval(() => {
                waTooltip.classList.add('tooltip-show');
                setTimeout(() => {
                    waTooltip.classList.remove('tooltip-show');
                }, 3000); // Tampil selama 3 detik
            }, 30000); // Setiap 30 detik
            
            // Tampilkan pertama kali setelah 5 detik
            setTimeout(() => {
                waTooltip.classList.add('tooltip-show');
                setTimeout(() => {
                    waTooltip.classList.remove('tooltip-show');
                }, 3000);
            }, 5000);
        }

        // Logic Mobile Menu
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const barsIcon = document.getElementById('bars-icon');
        const xIcon = document.getElementById('x-icon');
        const mobileLinks = document.querySelectorAll('.mobile-link');

        function toggleMenu() {
            const isActive = mobileMenu.classList.toggle('active');
            if(isActive) {
                barsIcon.classList.add('opacity-0', 'scale-50');
                xIcon.classList.remove('opacity-0', 'scale-50');
            } else {
                barsIcon.classList.remove('opacity-0', 'scale-50');
                xIcon.classList.add('opacity-0', 'scale-50');
            }
        }
        menuBtn?.addEventListener('click', toggleMenu);
        mobileLinks.forEach(link => link.addEventListener('click', toggleMenu));

        // Logic Download Kartu
        function downloadCard() {
            const card = document.getElementById('memberCard');
            const btn = document.getElementById('btnDownload');
            if(!card) return;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            btn.disabled = true;
            html2canvas(card, { scale: 3, useCORS: true, backgroundColor: null }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Kartu-Member-ArifahGym.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                btn.innerHTML = '<i class="fa-solid fa-download"></i> Simpan Kartu ke HP';
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>