<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>DENR BMS - Protected Area Sites | Biodiversity Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/protected-area-sites.css', 'resources/css/protected-area-sites-modal.css'])
    
    <!-- Scripts -->
    @vite(['resources/js/bootstrap.js', 'resources/js/sidebar.js', 'resources/js/protected-area-sites-modal.js'])
    
    <!-- Global JavaScript Variables -->
    <script>
        window.csrfToken = '{{ csrf_token() }}';
        // Pass protected areas data to JavaScript for modal dropdowns
        window.protectedAreas = @json(\App\Models\ProtectedArea::orderBy('name')->get(['id', 'name', 'code']));
        
        // Debug: Log the loaded data
        console.log('Protected Areas Data Loaded:', window.protectedAreas);
        
        // Global toggleSidebar function for compatibility
        function toggleSidebar() {
            if (window.sidebarManager) {
                window.sidebarManager.toggleSidebar();
            }
        }
    </script>
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
            <a href="{{ route('dashboard') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-green-50 hover:text-green-700 transition-all duration-200">
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
            <a href="{{ route('protected-area-sites.index') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 transition-all duration-200 ml-4">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
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

    <!-- Main Content -->
    <div class="lg:pl-64">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Page Title -->
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold text-gray-900">Protected Area Sites</h1>
                    </div>

                    <!-- Right Side Actions -->
                    <div class="flex items-center space-x-4">
                    </div>
                </div>
            </div>
        </header>

        <!-- Protected Area Sites Content -->
        <main class="p-6">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
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
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_areas']) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Sites -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Sites</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_sites']) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Observations -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Observations</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_observations']) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Species Diversity -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Species Diversity</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['species_diversity']) }}</p>
                            <p class="text-xs text-gray-600">unique species</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <form method="GET" action="{{ route('protected-area-sites.index') }}">
                    <div class="flex items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex-1">Filters</h2>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors whitespace-nowrap text-sm font-medium">
                                Apply
                            </button>
                            <button type="button" onclick="clearSiteFilters()" class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg transition-colors whitespace-nowrap text-sm font-medium border border-gray-300">
                                Clear
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Status Dropdown -->
                        <div class="w-full">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                                <option value="">All</option>
                                <option value="active" {{ (isset($statusFilter) && $statusFilter === 'active') ? 'selected' : '' }}>Active</option>
                                <option value="no_data" {{ (isset($statusFilter) && $statusFilter === 'no_data') ? 'selected' : '' }}>No Data</option>
                            </select>
                        </div>

                        <!-- Sort By Dropdown -->
                        <div class="w-full">
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                            <select name="sort" id="sort" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                                <option value="name" {{ (!isset($sort) || $sort === 'name') ? 'selected' : '' }}>Name (A–Z)</option>
                                <option value="protected_area" {{ (isset($sort) && $sort === 'protected_area') ? 'selected' : '' }}>Protected Area (A–Z)</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Protected Area Sites Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Search + Header -->
                <div class="px-6 py-4 border-b border-gray-200 relative">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Search Bar on Left -->
                        <div class="flex items-center space-x-2">
                            <!-- Search Input -->
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="protected-area-sites-search" 
                                    name="search"
                                    value="{{ request('search', '') }}"
                                    class="w-full sm:w-64 pl-8 pr-8 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                                    placeholder="Search protected area sites..."
                                    autocomplete="off"
                                    oninput="filterProtectedAreaSitesTable()"
                                />

                                <!-- Search Icon -->
                                <svg class="absolute left-2.5 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>

                                <!-- Clear Button -->
                                <button
                                    id="protected-area-sites-search-clear"
                                    type="button"
                                    class="protected-area-sites-search-clear text-gray-400 hover:text-gray-600 hidden bg-transparent"
                                    onclick="clearProtectedAreaSitesSearch()"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Title and Record Count on Right -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <h2 class="text-lg font-semibold text-gray-900">
                                Protected Area Sites ({{ $siteNames->total() }} records)
                            </h2>
                            <button onclick="openAddProtectedAreaSitesModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2 transition-colors flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Add Protected Area Site</span>
                            </button>
                        </div>
                    </div>
                </div>
            
            <div class="responsive-table-container">
                <table class="responsive-table">
                    <thead>
                        <tr>
                            <th>
                                Site Name
                            </th>
                            <th>
                                Protected Area
                            </th>
                            <th>
                                Station Code
                            </th>
                            <th>
                                Observations
                            </th>
                            <th>
                                Status
                            </th>
                            <th>
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="protected-area-sites-table-body">
                        @forelse ($siteNames as $site)
                            <tr class="hover:bg-gray-50 protected-area-sites-row" data-site-id="{{ $site->id }}">
                                <td>
                                    <div class="font-medium text-gray-900">
                                        {{ $site->name }}
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        @if($site->protectedArea)
                                            <div class="text-sm text-gray-900">
                                                {{ $site->protectedArea->name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $site->protectedArea->code }}
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">Not assigned</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        @if($site->station_code)
                                            <span class="station-code-badge">
                                                {{ $site->station_code }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-400">N/A</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="text-sm text-gray-900">
                                            {{ number_format($site->species_observations_count ?? 0) }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            observations
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($site->protectedArea)
                                        <span class="status-badge status-badge-active">
                                            Active
                                        </span>
                                    @else
                                        <span class="status-badge status-badge-unassigned">
                                            Unassigned
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-1 sm:gap-2 action-buttons-container">
                                        <!-- View Button -->
                                        <button type="button" onclick="openViewProtectedAreaSitesModal({{ $site->id }})" 
                                           class="protected-area-sites-action-btn view p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                                           title="View Site">
                                            <svg class="w-4 h-4 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        
                                        <!-- Edit Button -->
                                        <button type="button" onclick="openEditProtectedAreaSitesModal({{ $site->id }})" 
                                           class="protected-area-sites-action-btn edit p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                                           title="Edit Site">
                                            <svg class="w-4 h-4 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        
                                        <!-- Delete Button -->
                                        <button type="button" onclick="openDeleteProtectedAreaSitesModal({{ $site->id }})" 
                                           class="protected-area-sites-action-btn delete p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                                           title="Delete Site">
                                            <svg class="w-4 h-4 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">No Protected Area Sites</h3>
                                        <p class="text-gray-500">Protected area sites will appear here once they are added to the system.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <!-- Results Information -->
                        <div class="text-sm text-gray-700">
                            Showing 
                            {{ ($siteNames->currentPage() - 1) * $siteNames->perPage() + 1 }} 
                            to 
                            {{ min($siteNames->currentPage() * $siteNames->perPage(), $siteNames->total()) }} 
                            of {{ $siteNames->total() }} results
                        </div>
                        
                        <!-- Custom Previous/Next Navigation -->
                        <div class="flex items-center space-x-2">
                            <!-- Previous Button -->
                            @if($siteNames->onFirstPage())
                                <button class="px-3 py-1 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed" disabled>
                                    « Previous
                                </button>
                            @else
                                <a href="{{ $siteNames->previousPageUrl() }}" 
                                   class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                    « Previous
                                </a>
                            @endif
                            
                            <!-- Next Button -->
                            @if($siteNames->hasMorePages())
                                <a href="{{ $siteNames->nextPageUrl() }}" 
                                   class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                    Next »
                                </a>
                            @else
                                <button class="px-3 py-1 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed" disabled>
                                    Next »
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Laravel Pagination Links (hidden but kept for functionality) -->
                    <div class="hidden">
                        {{ $siteNames->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    // Initialize modal system when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Log that protected areas data is available
        console.log('Protected areas loaded:', window.protectedAreas?.length || 0, 'areas');
    });
    </script>
</body>
</html>
