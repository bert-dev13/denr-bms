<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Species Observations | DENR BMS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/species-observation-modal.css'])
    
    <!-- Scripts -->
    @vite(['resources/js/bootstrap.js', 'resources/js/sidebar.js', 'resources/js/species-observation-modal.js'])
    
    <!-- Global JavaScript Variables -->
    <script>
        window.csrfToken = '{{ csrf_token() }}';
        window.routes = {
            speciesObservationsStore: '{{ route('species-observations.store') }}',
            speciesObservationsShow: '{{ route('species-observations.show', ':id') }}',
            speciesObservationsUpdate: '{{ route('species-observations.update', ':id') }}',
            speciesObservationsDestroy: '{{ route('species-observations.destroy', ':id') }}'
        };
        
        // Debug: Log the routes
        // console.log('Routes defined:', window.routes);
        
        // Debug: Check if speciesObservationsModal is available
        // setTimeout(() => {
        //     console.log('Checking speciesObservationsModal availability:', typeof window.speciesObservationsModal);
        //     console.log('SpeciesObservationsModal object:', window.speciesObservationsModal);
        //     if (window.speciesObservationsModal) {
        //         console.log('speciesObservationsModal methods:', Object.getOwnPropertyNames(window.speciesObservationsModal));
        //     }
        // }, 2000);
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
            <a href="{{ route('species-observations.index') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 transition-all duration-200">
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
        <div class="w-full">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Page Title -->
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold text-gray-900">Species Observations</h1>
                    </div>
                </div>
            </div>
        </header>

        <!-- Species Observations Content -->
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

            <!-- Error Message -->
            @if (session('error'))
                <div id="error-message" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            {{ session('error') }}
                        </div>
                        <button type="button" onclick="dismissErrorMessage()" class="text-red-600 hover:text-red-800">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Filters Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <form method="GET" action="{{ route('species-observations.index') }}">
                    <div class="flex items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex-1">Filters</h2>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors whitespace-nowrap text-sm font-medium">
                                Apply
                            </button>
                            <button type="button" onclick="clearFilters()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors whitespace-nowrap text-sm font-medium">
                                Clear
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                    <!-- Protected Area Filter -->
                    <div class="flex-1 min-w-40">
                        <label for="protected_area_id" class="block text-sm font-medium text-gray-700 mb-1">Protected Area</label>
                        <select name="protected_area_id" id="protected_area_id" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm" onchange="toggleSiteNameFilter()">
                            <option value="">All Areas</option>
                            @foreach($filterOptions['protectedAreas'] as $area)
                                <option value="{{ $area->id }}" {{ request('protected_area_id') == $area->id ? 'selected' : '' }} data-code="{{ $area->code }}">
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bio Group Filter -->
                    <div class="flex-1 min-w-24">
                        <label for="bio_group" class="block text-sm font-medium text-gray-700 mb-1">Bio Group</label>
                        <select name="bio_group" id="bio_group" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                            <option value="">All Groups</option>
                            @foreach($filterOptions['bioGroups'] as $key => $group)
                                <option value="{{ $key }}" {{ request('bio_group') == $key ? 'selected' : '' }}>
                                    {{ $group }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Year Filter -->
                    <div class="flex-1 min-w-20">
                        <label for="patrol_year" class="block text-sm font-medium text-gray-700 mb-1">Patrol Year</label>
                        <select name="patrol_year" id="patrol_year" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                            <option value="">All Years</option>
                            @foreach($filterOptions['years'] as $year)
                                <option value="{{ $year }}" {{ request('patrol_year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Semester Filter -->
                    <div class="flex-1 min-w-20">
                        <label for="patrol_semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                        <select name="patrol_semester" id="patrol_semester" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
                            <option value="">All Semesters</option>
                            @foreach($filterOptions['semesters'] as $value => $label)
                                <option value="{{ $value }}" {{ request('patrol_semester') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Site Name Filter -->
                    <div class="flex-1 min-w-32">
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                        <select name="site_name" id="site_name" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm" disabled>
                            <option value="no_specific_site" {{ request('site_name') == 'no_specific_site' || !request()->filled('site_name') ? 'selected' : '' }}>No Specific Site</option>
                            <option value="" {{ request('site_name') == '' ? 'selected' : '' }}>All Sites</option>
                            @foreach($filterOptions['siteNames'] as $siteName)
                                <option value="{{ $siteName->id }}" {{ request('site_name') == $siteName->id ? 'selected' : '' }}>
                                    {{ $siteName->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    </div>
                </form>
            </div>

            <!-- Observations Table Search -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 relative">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex-1 max-w-lg">
                <div class="relative w-full">
                    <div class="relative flex items-center h-10">

                        <!-- 🔍 Search Icon (LEFT) -->
                        <span class="absolute left-3 flex items-center pointer-events-none z-10">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>

                        <!-- INPUT -->
                        <input
                            type="text"
                            id="table-search"
                            class="w-full h-10 pl-10 pr-8 text-sm border border-gray-300 rounded-lg
                                   focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Search observations..."
                            autocomplete="off"
                        />

                        <!-- ❌ Clear Button (FAR RIGHT) -->
                        <button
                            id="search-clear"
                            type="button"
                            class="absolute right-0 top-0 bottom-0 flex items-center justify-center w-8
                                   text-gray-400 hover:text-gray-600 hidden bg-transparent"
                            onclick="clearSearch()"
                            style="right: 2px;"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Left: Table Title -->
                <div class="flex-shrink-0 order-1 sm:order-1">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Observations ({{ $observations->total() }} records)
                    </h2>
                </div>
                
                <!-- Center: Export Dropdown -->
                <div class="flex-shrink-0 order-3 sm:order-2">
                    <div class="relative">
                        <button type="button" id="export-dropdown-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2 transition-colors w-full sm:w-auto justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Export</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="export-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50 sm:right-0 right-auto left-0 sm:left-auto">
                            <div class="py-1">
                                <button type="button" onclick="exportTable('print')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    <span>Print</span>
                                </button>
                                <button type="button" onclick="exportTable('excel')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v7m3-2h6"></path>
                                    </svg>
                                    <span>Excel</span>
                                </button>
                                <button type="button" onclick="exportTable('pdf')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>PDF</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Add Observation Button -->
                <div class="flex-shrink-0 order-2 sm:order-3">
                    <button onclick="window.openAddModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2 transition-colors w-full sm:w-auto justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Add Observation</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

                <div class="responsive-table-container">
                    <table class="responsive-table">
                        <thead>
                            <tr>
                                <th>
                                    Protected Area
                                </th>
                                <th>
                                    Station Code
                                </th>
                                <th>
                                    Patrol Period
                                </th>
                                <th>
                                    Bio Group
                                </th>
                                <th>
                                    Common Name
                                </th>
                                <th>
                                    Scientific Name
                                </th>
                                <th>
                                    Count
                                </th>
                                <th>
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($observations as $observation)
                                <tr class="hover:bg-gray-50 observation-row">
                                    <td>
                                        <span class="font-medium text-gray-900">{{ $observation->protectedArea->name ?? 'N/A' }}</span>
                                        <span class="text-xs text-gray-500">{{ $observation->transaction_code }}</span>
                                    </td>
                                    <td>
                                        {{ $observation->station_code }}
                                    </td>
                                    <td>
                                        <span class="text-sm text-gray-900">{{ $observation->patrol_year }}</span>
                                        <span class="text-xs text-gray-500">{{ $observation->patrol_semester_text }} Semester</span>
                                    </td>
                                    <td>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $observation->bio_group == 'fauna' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($observation->bio_group) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $observation->common_name }}
                                    </td>
                                    <td>
                                        <em>{{ $observation->scientific_name }}</em>
                                    </td>
                                    <td>
                                        <span class="inline-flex items-center px-2 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded-full">
                                            {{ $observation->recorded_count }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1 sm:gap-2 action-buttons-container">
                                            <!-- View Button -->
                                            <button type="button" onclick="window.openViewModal({{ $observation->id }}, '{{ $observation->table_name ?? '' }}')" 
                                               class="text-blue-600 hover:text-blue-800 p-1.5 sm:p-1 rounded hover:bg-blue-50 transition-colors flex-shrink-0"
                                               title="View Observation">
                                                <svg class="w-4 h-4 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            
                                             <!-- Edit Button -->
                                            <button type="button" onclick="window.openEditModal({{ $observation->id }}, '{{ $observation->table_name ?? '' }}')" 
                                               class="text-green-600 hover:text-green-800 p-1.5 sm:p-1 rounded hover:bg-green-50 transition-colors flex-shrink-0"
                                               title="Edit Observation">
                                                <svg class="w-4 h-4 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            <button type="button" onclick="window.openDeleteModal({{ $observation->id }}, '{{ $observation->table_name ?? '' }}')" 
                                               class="text-red-600 hover:text-red-800 p-1.5 sm:p-1 rounded hover:bg-red-50 transition-colors flex-shrink-0"
                                               title="Delete Observation">
                                                <svg class="w-4 h-4 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No observations found</h3>
                                            <p class="text-gray-500 mb-4">Get started by creating a new species observation.</p>
                                            <a href="{{ route('species-observations.create') }}" 
                                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                <span>New Observation</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            <tr id="species-observations-no-results" class="hidden">
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($observations->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ $observations->firstItem() }} to {{ $observations->lastItem() }} of {{ $observations->total() }} results
                            </div>
                            <div id="pagination-container">
                                {{ $observations->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Simple notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 1rem;
                right: 1rem;
                z-index: 10000;
                padding: 1rem 1.5rem;
                border-radius: 0.5rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                font-weight: 500;
                max-width: 400px;
                color: white;
                transition: opacity 0.3s ease-in-out;
            `;
            
            if (type === 'success') {
                notification.style.backgroundColor = '#10b981';
            } else if (type === 'error') {
                notification.style.backgroundColor = '#ef4444';
            }
            
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
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

        // Toggle site name filter based on protected area selection
        function toggleSiteNameFilter() {
            const protectedAreaSelect = document.getElementById('protected_area_id');
            const siteNameSelect = document.getElementById('site_name');
            const selectedOption = protectedAreaSelect.options[protectedAreaSelect.selectedIndex];
            const areaCode = selectedOption.getAttribute('data-code');
            const selectedAreaId = protectedAreaSelect.value;
            
            console.log('toggleSiteNameFilter called:', {
                areaCode,
                selectedAreaId,
                currentValue: protectedAreaSelect.value,
                siteNameDisabled: siteNameSelect.disabled,
                siteNameLoading: siteNameSelect._loading,
                lastLoadedArea: siteNameSelect._lastLoadedArea
            });
            
            if ((areaCode === 'PPLS' || areaCode === 'MPL') && selectedAreaId) {
                // Enable site name filter for Peñablanca and Magapit Protected Landscape
                if (siteNameSelect._loading) {
                    // Currently loading, don't do anything
                    console.log('Site names currently loading, waiting...');
                    return;
                }
                
                if (siteNameSelect._lastLoadedArea !== selectedAreaId) {
                    // Need to load site names for this area
                    console.log('Loading site names for', areaCode, 'area:', selectedAreaId);
                    loadSiteNames(selectedAreaId);
                } else {
                    // Already loaded for this area, just enable and default to No Specific Site
                    siteNameSelect.disabled = false;
                    siteNameSelect.value = 'no_specific_site'; // Always default to No Specific Site
                    console.log('Site name dropdown already loaded for this area, enabling and defaulting to No Specific Site');
                }
            } else {
                // Disable site name filter for other areas
                siteNameSelect.disabled = true;
                siteNameSelect.value = ''; // Clear selection
                
                // Clear any loading state and last loaded area
                siteNameSelect._loading = false;
                siteNameSelect._lastLoadedArea = null;
                
                console.log('Site name dropdown disabled for non-PPLS/MPL area');
            }
        }

        // Load site names via AJAX
        function loadSiteNames(protectedAreaId) {
            const siteNameSelect = document.getElementById('site_name');
            // Get current selection from the dropdown (which should reflect URL parameters on page load)
            const currentSelection = siteNameSelect.value;
            
            console.log('loadSiteNames called:', {
                protectedAreaId,
                currentSelection,
                disabled: siteNameSelect.disabled,
                loading: siteNameSelect._loading,
                lastLoadedArea: siteNameSelect._lastLoadedArea
            });
            
            // Prevent multiple simultaneous requests for the same area
            if (siteNameSelect._loading) {
                console.log('Already loading, skipping request');
                return;
            }
            
            // Skip if already loaded for this area and has options
            if (siteNameSelect._lastLoadedArea === protectedAreaId && siteNameSelect.options.length > 3) {
                console.log('Site names already loaded for this area, skipping request');
                siteNameSelect.disabled = false;
                return;
            }
            
            // Set loading state
            siteNameSelect._loading = true;
            siteNameSelect.disabled = true;
            
            console.log('Starting AJAX request');
            
            // Use the proper Laravel route
            const url = `{{ route('species-observations.site-names', ':id') }}`.replace(':id', protectedAreaId);
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(siteNames => {
                    console.log('Site names loaded:', siteNames);
                    
                    // Create a document fragment for efficient DOM manipulation
                    const fragment = document.createDocumentFragment();
                    
                    // Add "No Specific Site" option first
                    const noSpecificSiteOption = document.createElement('option');
                    noSpecificSiteOption.value = 'no_specific_site';
                    noSpecificSiteOption.textContent = 'No Specific Site';
                    fragment.appendChild(noSpecificSiteOption);
                    
                    // Add "All Sites" option second
                    const allSitesOption = document.createElement('option');
                    allSitesOption.value = '';
                    allSitesOption.textContent = 'All Sites';
                    fragment.appendChild(allSitesOption);
                    
                    // Add site name options
                    siteNames.forEach(siteName => {
                        const option = document.createElement('option');
                        option.value = siteName.id;
                        option.textContent = siteName.name;
                        if (siteName.id == currentSelection) {
                            option.selected = true;
                        }
                        fragment.appendChild(option);
                    });
                    
                    // Clear and append new options
                    siteNameSelect.innerHTML = '';
                    siteNameSelect.appendChild(fragment);
                    
                    // Restore the previous selection if it exists, otherwise default to 'No Specific Site'
                    if (currentSelection) {
                        siteNameSelect.value = currentSelection;
                    } else {
                        siteNameSelect.value = 'no_specific_site'; // Default to No Specific Site
                    }
                    
                    // Mark this area as loaded
                    siteNameSelect._lastLoadedArea = protectedAreaId;
                    
                    console.log('DOM updated, enabling dropdown');
                })
                .catch(error => {
                    console.error('Error loading site names:', error);
                    // Keep consistent structure even on error
                    siteNameSelect.innerHTML = '<option value="no_specific_site">No Specific Site</option><option value="">All Sites</option>';
                    // Clear the last loaded area on error
                    siteNameSelect._lastLoadedArea = null;
                })
                .finally(() => {
                    // Always clear loading state and enable dropdown when done
                    siteNameSelect._loading = false;
                    siteNameSelect.disabled = false;
                    console.log('Request completed, dropdown enabled');
                });
        }

        // Debug function to check dropdown state
        function debugDropdown() {
            const siteNameSelect = document.getElementById('site_name');
            const protectedAreaSelect = document.getElementById('protected_area_id');
            
            console.log('=== DROPDOWN DEBUG INFO ===');
            console.log('Site Name Select:', {
                id: siteNameSelect.id,
                disabled: siteNameSelect.disabled,
                loading: siteNameSelect._loading,
                lastLoadedArea: siteNameSelect._lastLoadedArea,
                value: siteNameSelect.value,
                optionsLength: siteNameSelect.options.length,
                style: siteNameSelect.style.cssText,
                className: siteNameSelect.className
            });
            
            console.log('Protected Area Select:', {
                value: protectedAreaSelect.value,
                selectedIndex: protectedAreaSelect.selectedIndex,
                selectedOption: protectedAreaSelect.options[protectedAreaSelect.selectedIndex]?.text,
                selectedCode: protectedAreaSelect.options[protectedAreaSelect.selectedIndex]?.getAttribute('data-code')
            });
            
            // Test if dropdown is actually interactive
            siteNameSelect.focus();
            console.log('Focused on dropdown - try to interact with it now');
        }

        // Initialize dropdown state from URL parameters on page load
        function initializeDropdownState() {
            const protectedAreaSelect = document.getElementById('protected_area_id');
            const siteNameSelect = document.getElementById('site_name');
            
            // Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const protectedAreaId = urlParams.get('protected_area_id');
            const siteName = urlParams.get('site_name');
            
            console.log('Initializing dropdown state:', {
                protectedAreaId,
                siteName,
                currentSiteValue: siteNameSelect.value
            });
            
            // If we have a protected area selected, trigger the site name loading
            if (protectedAreaId) {
                // The static HTML should already have the correct selection from Laravel
                // Just need to trigger the JavaScript to enable the dropdown
                setTimeout(() => {
                    toggleSiteNameFilter();
                }, 100);
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeDropdownState();
            initializeSearch();
            
            // Auto-dismiss error message after 5 seconds
            const errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                setTimeout(() => {
                    dismissErrorMessage();
                }, 5000);
            }
            
            // Populate search input from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const searchQuery = urlParams.get('search');
            if (searchQuery) {
                document.getElementById('table-search').value = searchQuery;
                document.getElementById('search-clear').classList.remove('hidden');
            }
        });

        // Dismiss error message function
        function dismissErrorMessage() {
            const errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.style.transition = 'opacity 0.3s ease-in-out';
                errorMessage.style.opacity = '0';
                setTimeout(() => {
                    errorMessage.remove();
                }, 300);
            }
        }

        // Search functionality
        let searchTimeout;
        
        function initializeSearch() {
            const searchInput = document.getElementById('table-search');
            const searchClear = document.getElementById('search-clear');
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                // Show/hide clear button
                if (query) {
                    searchClear.classList.remove('hidden');
                } else {
                    searchClear.classList.add('hidden');
                }
                
                // Debounce search
                searchTimeout = setTimeout(() => {
                    performServerSearch(query);
                }, 500);
            });
            
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    clearSearch();
                }
            });
        }
        
        function performServerSearch(query) {
            console.log('=== PERFORMING SEARCH ===');
            console.log('Search query:', query);
            
            // Get current form parameters
            const form = document.querySelector('form[method="GET"]');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            console.log('Current form params:', Object.fromEntries(params));
            
            // Add or update search parameter
            if (query) {
                params.set('search', query);
                params.delete('page');
            } else {
                params.delete('search');
            }
            
            console.log('Updated params:', Object.fromEntries(params));
            
            // Navigate to new URL
            const newUrl = window.location.pathname + '?' + params.toString();
            console.log('Navigating to:', newUrl);
            window.location.href = newUrl;
        }
        
        function clearSearch() {
            const searchInput = document.getElementById('table-search');
            const searchClear = document.getElementById('search-clear');
            
            searchInput.value = '';
            searchClear.classList.add('hidden');
            performServerSearch('');
        }

        // Clear all filters
        function clearFilters() {
            // Reset all form fields
            document.getElementById('protected_area_id').value = '';
            document.getElementById('bio_group').value = '';
            document.getElementById('patrol_year').value = '';
            document.getElementById('patrol_semester').value = '';
            document.getElementById('site_name').value = ''; // This will default to No Specific Site on page reload
            
            // Submit the form to reload page with cleared filters
            const form = document.querySelector('form[method="GET"]');
            form.submit();
        }

        // Initialize pagination when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize pagination enhancement
            initializePagination();

            const protectedAreaSelect = document.getElementById('protected_area_id');
            const siteNameSelect = document.getElementById('site_name');
            
            // Initialize loading state and tracking
            siteNameSelect._loading = false;
            siteNameSelect._lastLoadedArea = null;
            
            // Add click handler to prevent interaction during loading
            siteNameSelect.addEventListener('click', function(e) {
                console.log('Site name dropdown clicked:', {
                    loading: this._loading,
                    disabled: this.disabled,
                    value: this.value,
                    optionsLength: this.options.length
                });
                
                if (this._loading) {
                    e.preventDefault();
                    console.log('Dropdown is loading, please wait...');
                    return false;
                }
                
                if (this.disabled) {
                    e.preventDefault();
                    console.log('Dropdown is disabled, cannot interact');
                    return false;
                }
            });
            
            // Add focus handler for additional debugging
            siteNameSelect.addEventListener('focus', function(e) {
                console.log('Site name dropdown focused:', {
                    loading: this._loading,
                    disabled: this.disabled,
                    value: this.value
                });
            });
            
            // Add change handler to ensure proper state management
            siteNameSelect.addEventListener('change', function(e) {
                console.log('Site name selection changed:', this.value);
            });
            
            if (protectedAreaSelect.value) {
                const selectedOption = protectedAreaSelect.options[protectedAreaSelect.selectedIndex];
                const areaCode = selectedOption.getAttribute('data-code');
                
                if (areaCode === 'PPLS' || areaCode === 'MPL') {
                    // Enable site name filter if PPLS or MPL is selected
                    siteNameSelect.disabled = false;
                    
                    // Load site names to restore the options
                    loadSiteNames(protectedAreaSelect.value);
                } else {
                    // Keep disabled for other areas
                    siteNameSelect.disabled = true;
                }
            } else {
                // Keep disabled when no area is selected
                siteNameSelect.disabled = true;
            }
        });

        // Export dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const exportDropdownBtn = document.getElementById('export-dropdown-btn');
            const exportDropdown = document.getElementById('export-dropdown');
            
            // Toggle dropdown
            exportDropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                exportDropdown.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!exportDropdown.contains(e.target) && e.target !== exportDropdownBtn) {
                    exportDropdown.classList.add('hidden');
                }
            });
        });

        // Export functionality
        function exportTable(format) {
            // Get current filter parameters
            const form = document.querySelector('form[method="GET"]');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            // Add export format parameter
            params.set('export', format);
            
            // Get current search query if exists
            const searchInput = document.getElementById('table-search');
            if (searchInput.value.trim()) {
                params.set('search', searchInput.value.trim());
            }
            
            // Construct export URL
            const exportUrl = window.location.pathname + '?' + params.toString();
            
            switch(format) {
                case 'print':
                    // Open print-friendly version in new window
                    const printWindow = window.open(exportUrl + '&print=1', '_blank');
                    if (printWindow) {
                        printWindow.onload = function() {
                            printWindow.print();
                        };
                    }
                    break;
                    
                case 'excel':
                    // Download Excel file
                    window.location.href = exportUrl + '&excel=1';
                    showNotification('Excel export started. Download will begin shortly.', 'success');
                    break;
                    
                case 'pdf':
                    // Download PDF file
                    window.location.href = exportUrl + '&pdf=1';
                    showNotification('PDF export started. Download will begin shortly.', 'success');
                    break;
                    
                default:
                    showNotification('Invalid export format', 'error');
            }
            
            // Close dropdown
            document.getElementById('export-dropdown').classList.add('hidden');
        }
    </script>
</body>
</html>
