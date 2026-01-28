<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>DENR BMS - Dashboard | Biodiversity Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/sidebar.css'])
    
    <!-- Scripts -->
    @vite(['resources/js/bootstrap.js', 'resources/js/sidebar.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="antialiased bg-gray-50">
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle lg:hidden fixed top-4 left-4 z-40 bg-blue-600 text-white p-3 rounded-lg shadow-lg" onclick="toggleSidebar()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white shadow-xl z-35 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
        <!-- Header -->
        <div class="bg-gradient-to-br from-green-600 to-green-700 p-6 text-white">
            <div class="flex items-center space-x-3">
                <div>
                    <h1 class="text-xl font-bold">DENR BMS</h1>
                    <p class="text-green-100 text-sm">Biodiversity Management System</p>
                </div>
            </div>
        </div>

        <!-- User Profile -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 rounded-full border-2 border-white"></div>
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-900">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-sm text-gray-500">{{ auth()->user()->email ?? 'user@example.com' }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="whitespace-nowrap">Dashboard</span>
            </a>

            <!-- Species Observations -->
            <a href="{{ route('species-observations.index') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-green-50 hover:text-green-700 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <span class="whitespace-nowrap">Species Observations</span>
            </a>

            <!-- Protected Areas -->
            <a href="{{ route('protected-areas.index') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-green-50 hover:text-green-700 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="whitespace-nowrap">Protected Areas</span>
            </a>

            <!-- Protected Area Sites -->
            <a href="{{ route('protected-area-sites.index') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-green-50 hover:text-green-700 transition-all duration-200 ml-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span class="whitespace-nowrap">Protected Area Sites</span>
            </a>

            <!-- Analytics -->
            <a href="{{ route('analytics.index') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-green-50 hover:text-green-700 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span class="whitespace-nowrap">Analytics</span>
            </a>

            <!-- Reports -->
            <a href="#" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-green-50 hover:text-green-700 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v7m3-2h6"></path>
                </svg>
                <span class="whitespace-nowrap">Reports</span>
            </a>

            <!-- Settings -->
            <a href="#" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-green-50 hover:text-green-700 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="whitespace-nowrap">Settings</span>
            </a>
        </nav>

        <!-- Logout -->
        <div class="p-4 border-t border-gray-200">
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="whitespace-nowrap">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="lg:pl-64">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-end h-16">
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="p-6">
            <!-- Welcome Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name ?? 'User' }}!</h1>
                <p class="text-gray-600 mt-2">Here's what's happening with your biodiversity management system today.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Species Observations -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Observations</p>
                            <p class="text-2xl font-bold text-gray-900" id="total-observations">{{ number_format($stats['total_observations']) }}</p>
                            <p class="text-xs {{ $stats['monthly_growth'] > 0 ? 'text-green-600' : 'text-gray-600' }}">
                                {{ $stats['monthly_growth'] > 0 ? '+' : '' }}{{ $stats['monthly_growth'] }}% from last month
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total Areas -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Areas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['protected_areas'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Species Count -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Species Tracked</p>
                            <p class="text-2xl font-bold text-gray-900" id="total-species">{{ number_format($stats['total_species']) }}</p>
                            <p class="text-xs {{ $stats['quarterly_growth'] > 0 ? 'text-green-600' : 'text-gray-600' }}">
                                {{ $stats['quarterly_growth'] > 0 ? '+' : '' }}{{ $stats['quarterly_growth'] }}% this quarter
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Active Users</p>
                            <p class="text-2xl font-bold text-gray-900" id="active-users">{{ $stats['active_users'] }}</p>
                            <p class="text-xs text-gray-600">This week</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Quick Actions</h2>
                        <p class="text-sm text-gray-600 mt-1">Common tasks and quick access to system features</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Add New Observation -->
                    <a href="{{ route('species-observations.create') }}" class="group flex flex-col items-center p-6 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-all duration-200">
                        <div class="p-3 bg-white rounded-lg mb-3 group-hover:bg-gray-50 transition-colors border border-gray-200">
                            <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">New Observation</h3>
                        <p class="text-sm text-gray-600 text-center">Record species observation data</p>
                    </a>

                    <!-- View Protected Areas -->
                    <a href="{{ route('protected-areas.index') }}" class="group flex flex-col items-center p-6 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-all duration-200">
                        <div class="p-3 bg-white rounded-lg mb-3 group-hover:bg-gray-50 transition-colors border border-gray-200">
                            <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">Protected Areas</h3>
                        <p class="text-sm text-gray-600 text-center">Browse protected area information</p>
                    </a>

                    <!-- Analytics Dashboard -->
                    <a href="{{ route('analytics.index') }}" class="group flex flex-col items-center p-6 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-all duration-200">
                        <div class="p-3 bg-white rounded-lg mb-3 group-hover:bg-gray-50 transition-colors border border-gray-200">
                            <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">Analytics</h3>
                        <p class="text-sm text-gray-600 text-center">View detailed analytics and reports</p>
                    </a>

                    <!-- Search Observations -->
                    <a href="{{ route('species-observations.index') }}" class="group flex flex-col items-center p-6 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-all duration-200">
                        <div class="p-3 bg-white rounded-lg mb-3 group-hover:bg-gray-50 transition-colors border border-gray-200">
                            <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">Search Data</h3>
                        <p class="text-sm text-gray-600 text-center">Search and filter observations</p>
                    </a>
                </div>
            </div>

            <!-- Monitoring Chart -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Monitoring Activity Over Time</h2>
                        <p class="text-sm text-gray-600 mt-1">Yearly monitoring activity showing patterns</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Increasing
                        </span>
                        <span class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-1 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Decreasing
                        </span>
                    </div>
                </div>
                
                <!-- Chart Container -->
                <div class="relative">
                    <canvas id="monitoringChart" width="400" height="150"></canvas>
                    <div id="chartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75">
                        <div class="flex items-center space-x-2">
                            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm text-gray-600">Loading monitoring data...</span>
                        </div>
                    </div>
                </div>
                
                <!-- Chart Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Total Years Tracked</p>
                        <p class="text-lg font-semibold text-gray-900" id="totalYears">-</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Average Annual Monitoring</p>
                        <p class="text-lg font-semibold text-gray-900" id="avgMonitoring">-</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Trend Direction</p>
                        <p class="text-lg font-semibold" id="trendDirection">-</p>
                    </div>
                </div>
            </div>
        </main>

    <!-- JavaScript -->
    <script>
        let monitoringChart = null;
        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth < 1024 && !sidebar.contains(event.target) && !toggle.contains(event.target) && !event.target.closest('a')) {
                sidebar.classList.add('-translate-x-full');
                document.querySelector('.sidebar-overlay').classList.add('hidden');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });


        // Load chart when page loads

        async function loadMonitoringChart() {
            console.log('Loading monitoring chart...');
            try {
                console.log('Fetching data from /api/dashboard/yearly-monitoring');
                const response = await fetch('/api/dashboard/yearly-monitoring');
                console.log('Response status:', response.status);
                const data = await response.json();
                console.log('Received data:', data);
                
                // Hide loading indicator
                document.getElementById('chartLoading').classList.add('hidden');
                
                // Update statistics
                document.getElementById('totalYears').textContent = data.total_years;
                
                const avgMonitoring = data.total_years > 0 ? 
                    Math.round(data.total_observations / data.total_years) : 0;
                document.getElementById('avgMonitoring').textContent = avgMonitoring.toLocaleString();
                
                // Calculate trend direction
                if (data.data.length >= 2) {
                    const recent = data.data.slice(-3);
                    const older = data.data.slice(-6, -3);
                    
                    if (recent.length > 0 && older.length > 0) {
                        const recentAvg = recent.reduce((sum, item) => sum + item.count, 0) / recent.length;
                        const olderAvg = older.reduce((sum, item) => sum + item.count, 0) / older.length;
                        
                        const trendElement = document.getElementById('trendDirection');
                        if (recentAvg > olderAvg) {
                            trendElement.innerHTML = '<span class="text-green-600">↑ Increasing</span>';
                        } else if (recentAvg < olderAvg) {
                            trendElement.innerHTML = '<span class="text-red-600">↓ Decreasing</span>';
                        } else {
                            trendElement.innerHTML = '<span class="text-gray-600">→ Stable</span>';
                        }
                    } else {
                        document.getElementById('trendDirection').innerHTML = '<span class="text-gray-600">→ Stable</span>';
                    }
                } else {
                    document.getElementById('trendDirection').innerHTML = '<span class="text-gray-600">→ Insufficient Data</span>';
                }
                
                // Prepare chart data
                const labels = data.data.map(item => item.year.toString());
                const counts = data.data.map(item => item.count); // Now cumulative totals
                const yearlyCounts = data.data.map(item => item.yearly_count); // Individual year counts
                
                // Create zigzag effect by alternating colors based on trend
                const backgroundColors = counts.map((count, index) => {
                    if (index === 0) return 'rgba(59, 130, 246, 0.1)';
                    const trend = count - counts[index - 1];
                    return trend >= 0 ? 'rgba(34, 197, 94, 0.1)' : 'rgba(239, 68, 68, 0.1)';
                });
                
                const borderColors = counts.map((count, index) => {
                    if (index === 0) return 'rgba(59, 130, 246, 1)';
                    const trend = count - counts[index - 1];
                    return trend >= 0 ? 'rgba(34, 197, 94, 1)' : 'rgba(239, 68, 68, 1)';
                });
                
                // Destroy existing chart if it exists
                if (monitoringChart) {
                    monitoringChart.destroy();
                }
                
                // Create new chart
                const ctx = document.getElementById('monitoringChart').getContext('2d');
                monitoringChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Monitoring Observations',
                            data: counts,
                            backgroundColor: backgroundColors,
                            borderColor: borderColors,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.1,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: borderColors,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            segment: {
                                borderColor: function(context) {
                                    const index = context.p0DataIndex;
                                    if (index === 0) return 'rgba(59, 130, 246, 1)';
                                    const trend = counts[index] - counts[index - 1];
                                    return trend >= 0 ? 'rgba(34, 197, 94, 1)' : 'rgba(239, 68, 68, 1)';
                                }
                            }
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                padding: 12,
                                displayColors: false,
                                callbacks: {
                                    title: function(context) {
                                        return `Year: ${context[0].label}`;
                                    },
                                    label: function(context) {
                                        const cumulativeTotal = context.parsed.y.toLocaleString();
                                        const yearlyCount = yearlyCounts[context.dataIndex].toLocaleString();
                                        let label = `Cumulative Total: ${cumulativeTotal}`;
                                        
                                        if (context.dataIndex > 0) {
                                            const prevCumulative = counts[context.dataIndex - 1];
                                            const currentCumulative = context.parsed.y;
                                            const change = currentCumulative - prevCumulative;
                                            const changePercent = prevCumulative > 0 ? ((change / prevCumulative) * 100).toFixed(1) : 0;
                                            const changeSymbol = change >= 0 ? '+' : '';
                                            label += ` (${changeSymbol}${yearlyCount} this year / ${changeSymbol}${changePercent}%)`;
                                        } else {
                                            label += ` (${yearlyCount} this year)`;
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    },
                                    color: '#6b7280'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    },
                                    color: '#6b7280',
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
                
            } catch (error) {
                console.error('Error loading monitoring chart:', error);
                document.getElementById('chartLoading').innerHTML = 
                    '<div class="text-center text-red-600">Error loading monitoring data</div>';
            }
        }

        // Load chart when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadMonitoringChart();
        });
    </script>
</body>
</html>
