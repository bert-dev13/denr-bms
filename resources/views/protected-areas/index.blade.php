<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>DENR BMS - Protected Areas | Biodiversity Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/css/protected-areas.css', 'resources/css/protected-area-modal.css'])
    
    <!-- Scripts -->
    @vite(['resources/js/bootstrap.js', 'resources/js/sidebar.js', 'resources/js/protected-area-modal.js'])
    
    <!-- Global JavaScript Variables -->
    <script>
        window.csrfToken = '{{ csrf_token() }}';
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
            <a href="{{ route('protected-areas.index') }}" class="nav-item group flex items-center space-x-3 px-4 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 transition-all duration-200">
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
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Page Title -->
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold text-gray-900">Protected Areas</h1>
                    </div>

                    <!-- Right Side Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Add Protected Area button moved to table section -->
                    </div>
                </div>
            </div>
        </header>

        <!-- Protected Areas Content -->
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
                            <p class="text-2xl font-bold text-gray-900" id="total-areas-count">{{ number_format($stats['total_areas']) }}</p>
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
                <form method="GET" action="{{ route('protected-areas.index') }}">
                    <div class="flex items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex-1">Filters</h2>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors whitespace-nowrap text-sm font-medium">
                                Apply
                            </button>
                            <button type="button" onclick="clearProtectedAreaFilters()" class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg transition-colors whitespace-nowrap text-sm font-medium border border-gray-300">
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
                                <option value="code" {{ (isset($sort) && $sort === 'code') ? 'selected' : '' }}>Code (A–Z)</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Protected Areas Table Card (with search + add button) -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Search + Header -->
                <div class="px-6 py-4 border-b border-gray-200 relative">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Search -->
                        <div class="flex-1 max-w-lg">
                            <div class="relative w-full">
                                <div class="relative flex items-center h-10">
                                    <span class="absolute left-3 flex items-center pointer-events-none z-10">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </span>

                                    <input
                                        type="text"
                                        id="protected-area-search"
                                        class="w-full h-10 pl-10 pr-8 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Search protected areas..."
                                        autocomplete="off"
                                        oninput="filterProtectedAreasTable()"
                                    />

                                    <button
                                        id="protected-area-search-clear"
                                        type="button"
                                        class="absolute right-0 top-0 bottom-0 flex items-center justify-center w-8
                                               text-gray-400 hover:text-gray-600 hidden bg-transparent"
                                        onclick="clearProtectedAreaSearch()"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Title and Add Button -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <h2 class="text-lg font-semibold text-gray-900">
                                Protected Areas ({{ $protectedAreas->total() }} records)
                            </h2>
                            <button onclick="openAddModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-flex items-center space-x-2 transition-colors flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Add Protected Area</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="responsive-table-container">
                    <table class="responsive-table">
                        <thead>
                            <tr>
                                <th>
                                    Area Code
                                </th>
                                <th>
                                    Name
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
                        <tbody id="protected-area-table-body">
                            @forelse ($protectedAreas as $area)
                                <tr class="hover:bg-gray-50 protected-area-row" data-area-id="{{ $area->id }}">
                                    <td>
                                        <div class="font-medium text-gray-900">
                                            {{ $area->code }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-medium text-gray-900">
                                            {{ $area->name }}
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="text-sm text-gray-900">
                                                {{ number_format($area->species_observations_count) }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                observations
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($area->species_observations_count > 0)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                No Data
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1 sm:gap-2 action-buttons-container">
                                            <!-- View Button -->
                                            <button type="button" onclick="openViewModal({{ $area->id }})" 
                                               class="action-btn view p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                                               title="View Protected Area">
                                                <svg class="w-4 h-4 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            
                                             <!-- Edit Button -->
                                            <button type="button" onclick="openEditModal({{ $area->id }})" 
                                               class="action-btn edit p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                                               title="Edit Protected Area">
                                                <svg class="w-4 h-4 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            <button type="button" onclick="openDeleteModal({{ $area->id }})" 
                                               class="action-btn delete p-1.5 sm:p-1 rounded transition-colors flex-shrink-0"
                                               title="Delete Protected Area">
                                                <svg class="w-4 h-4 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No protected areas</h3>
                                            <p class="text-gray-500">Protected areas will appear here once they are added to the system.</p>
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
                            {{ ($protectedAreas->currentPage() - 1) * $protectedAreas->perPage() + 1 }} 
                            to 
                            {{ min($protectedAreas->currentPage() * $protectedAreas->perPage(), $protectedAreas->total()) }} 
                            of {{ $protectedAreas->total() }} results
                        </div>
                        
                        <!-- Custom Previous/Next Navigation -->
                        <div class="flex items-center space-x-2">
                            <!-- Previous Button -->
                            @if($protectedAreas->onFirstPage())
                                <button class="px-3 py-1 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed" disabled>
                                    « Previous
                                </button>
                            @else
                                <a href="{{ $protectedAreas->previousPageUrl() }}" 
                                   class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                    « Previous
                                </a>
                            @endif
                            
                            <!-- Next Button -->
                            @if($protectedAreas->hasMorePages())
                                <a href="{{ $protectedAreas->nextPageUrl() }}" 
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
                        {{ $protectedAreas->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>

<script>
// Ensure modal system is initialized
function ensureModalSystem() {
    // Only initialize if the class exists (for existing modal system)
    if (typeof ProtectedAreaModalSystem !== 'undefined') {
        if (!window.protectedAreaModalSystem) {
            window.protectedAreaModalSystem = new ProtectedAreaModalSystem();
        }
    }
}

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
    
    if (window.innerWidth < 1024 && !sidebar.contains(event.target) && !toggle.contains(event.target)) {
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

// Clear filters (status + sort)
function clearProtectedAreaFilters() {
    const status = document.getElementById('status');
    const sort = document.getElementById('sort');
    if (status) status.value = '';
    if (sort) sort.value = 'name';

    // Submit the form to reset filters
    status.closest('form').submit();
}

// Client-side search over protected areas table
function filterProtectedAreasTable() {
    const input = document.getElementById('protected-area-search');
    const clearBtn = document.getElementById('protected-area-search-clear');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#protected-area-table-body .protected-area-row');

    if (clearBtn) {
        clearBtn.classList.toggle('hidden', filter.length === 0);
    }

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}

function clearProtectedAreaSearch() {
    const input = document.getElementById('protected-area-search');
    input.value = '';
    filterProtectedAreasTable();
}

// Initialize modal system when page loads
document.addEventListener('DOMContentLoaded', function() {
    ensureModalSystem();
});

</script>

</body>
</html>
