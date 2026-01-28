<?php

namespace App\Http\Controllers;

use App\Models\ProtectedArea;
use App\Models\SiteName;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class ProtectedAreaController extends Controller
{
    public function index(Request $request)
    {
        // Handle filters
        $statusFilter = $request->input('status'); // active, no_data, or null
        $sort = $request->input('sort', 'name');   // name or code

        // Base query for all protected areas (we'll filter in collection so we can use
        // the computed species_observations_count attribute)
        $allProtectedAreas = ProtectedArea::withTotalObservationsCount()
            ->orderBy($sort === 'code' ? 'code' : 'name')
            ->get();

        // Apply status filter in PHP so we can rely on the computed observation count
        $filteredAreas = $allProtectedAreas->filter(function ($area) use ($statusFilter) {
            if ($statusFilter === 'active') {
                return $area->species_observations_count > 0;
            }
            if ($statusFilter === 'no_data') {
                return $area->species_observations_count == 0;
            }
            return true;
        });

        // Manual pagination for the filtered collection
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $filteredAreas->forPage($currentPage, $perPage)->values();

        $protectedAreas = new LengthAwarePaginator(
            $currentItems,
            $filteredAreas->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Calculate total observations across all tables
        $tables = ['batanes_tbl', 'baua_tbl', 'fuyot_tbl', 'magapit_tbl', 'palaui_tbl', 'quirino_tbl', 'mariano_tbl', 'madupapa_tbl', 'wangag_tbl', 'toyota_tbl', 'manga_tbl', 'quibal_tbl', 'madre_tbl', 'tumauini_tbl', 'bangan_tbl', 'salinas_tbl', 'dupax_tbl', 'casecnan_tbl', 'dipaniong_tbl', 'roque_tbl'];
        
        $totalObservations = 0;
        foreach ($tables as $table) {
            try {
                $totalObservations += DB::table($table)->count();
            } catch (\Exception $e) {
                // Skip tables that don't exist
                continue;
            }
        }

        // Calculate species diversity across all tables (true unique species)
        $allScientificNames = collect();
        foreach ($tables as $table) {
            try {
                $species = DB::table($table)->pluck('scientific_name')->filter();
                $allScientificNames = $allScientificNames->merge($species);
            } catch (\Exception $e) {
                // Skip tables that don't exist
                continue;
            }
        }
        $speciesDiversity = $allScientificNames->unique()->count();

        // Calculate active areas using all protected areas, not just paginated ones
        $allProtectedAreas = ProtectedArea::all();
        $activeAreasCount = 0;
        foreach ($allProtectedAreas as $area) {
            if ($area->getTotalObservationsCount() > 0) {
                $activeAreasCount++;
            }
        }

        $stats = [
            'total_areas' => ProtectedArea::count(),
            'active_areas' => $activeAreasCount,
            'total_observations' => $totalObservations,
            'species_diversity' => $speciesDiversity,
            'total_sites' => SiteName::count(),
        ];

        return view('protected-areas.index', compact('protectedAreas', 'stats', 'statusFilter', 'sort'));
    }

    /**
     * Store a newly created protected area.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:protected_areas,code',
            'name' => 'required|string|max:255',
        ]);

        try {
            ProtectedArea::create($validated);

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Protected area created successfully.',
                ]);
            }

            return redirect()
                ->route('protected-areas.index')
                ->with('success', 'Protected area created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error creating protected area: ' . $e->getMessage());

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to create protected area.',
                ], 500);
            }

            return back()
                ->with('error', 'Failed to create protected area.')
                ->withInput();
        }
    }

    public function sites()
    {
        $siteNames = SiteName::with('protectedArea')
            ->orderBy('name')
            ->paginate(10);

        // Add observation counts to each site
        $tables = ['batanes_tbl', 'baua_tbl', 'fuyot_tbl', 'magapit_tbl', 'palaui_tbl', 'quirino_tbl', 'mariano_tbl', 'madupapa_tbl', 'wangag_tbl', 'toyota_tbl', 'manga_tbl', 'quibal_tbl', 'madre_tbl', 'tumauini_tbl', 'bangan_tbl', 'salinas_tbl', 'dupax_tbl', 'casecnan_tbl', 'dipaniong_tbl', 'roque_tbl'];
        
        // Define all station code mappings for each site
        $stationCodeMappings = [
            'PPLS Site 1 – Toyota Project, Cabasan, Peñablanca, Cagayan' => ['TOYOTA-S1', 'R2-BWFR-BMS', 'R2-WWFR-BMS-S1'],
            'PPLS Site 2 – Sitio Spring, San Roque, Peñablanca, Cagayan' => ['SANROQUE-S1'],
            'PPLS Site 3 – Sitio Danna, Manga, Peñablanca, Cagayan' => ['MANGA-S1'],
            'PPLS Site 4 – Sitio Abukay, Quibal, Peñablanca, Cagayan' => ['QUIBAL-S1'],
            'MPL SITE 1 – San Mariano, Lal-lo, Cagayan' => ['R2-MPL-BMS-T - S1'],
            'MPL SITE 2 – Sitio Madupapa, Sta. Ana, Gattaran, Cagayan' => ['R2-MPL-BMS-T - S2'],
        ];
        
        foreach ($siteNames as $site) {
            $siteObservationCount = 0;
            $siteStationCodes = $stationCodeMappings[$site->name] ?? [];
            
            foreach ($siteStationCodes as $stationCode) {
                foreach ($tables as $table) {
                    try {
                        $siteObservationCount += DB::table($table)
                            ->where('station_code', $stationCode)
                            ->count();
                    } catch (\Exception $e) {
                        // Skip tables that don't exist
                        continue;
                    }
                }
            }
            $site->species_observations_count = $siteObservationCount;
        }

        // Calculate total observations across all tables
        $totalObservations = 0;
        foreach ($tables as $table) {
            try {
                $totalObservations += DB::table($table)->count();
            } catch (\Exception $e) {
                // Skip tables that don't exist
                continue;
            }
        }

        // Calculate species diversity across all tables (true unique species)
        $allScientificNames = collect();
        foreach ($tables as $table) {
            try {
                $species = DB::table($table)->pluck('scientific_name')->filter();
                $allScientificNames = $allScientificNames->merge($species);
            } catch (\Exception $e) {
                // Skip tables that don't exist
                continue;
            }
        }
        $speciesDiversity = $allScientificNames->unique()->count();

        $stats = [
            'total_areas' => ProtectedArea::count(),
            'total_sites' => SiteName::count(),
            'total_observations' => $totalObservations,
            'species_diversity' => $speciesDiversity,
        ];

        return view('protected-area-sites.index', compact('siteNames', 'stats'));
    }

    /**
     * Display the specified protected area.
     */
    public function show(ProtectedArea $protectedArea)
    {
        $protectedArea->loadCount('speciesObservations');
        return view('protected-areas.show', compact('protectedArea'));
    }

    /**
     * Get protected area data for View modal (AJAX)
     */
    public function getProtectedAreaData($id)
    {
        try {
            $protectedArea = ProtectedArea::findOrFail($id);
            
            // Get observation count
            $observationCount = $protectedArea->getTotalObservationsCount();
            
            return response()->json([
                'success' => true,
                'protectedArea' => [
                    'id' => $protectedArea->id,
                    'code' => $protectedArea->code,
                    'name' => $protectedArea->name,
                    'observation_count' => $observationCount,
                    'status' => $observationCount > 0 ? 'Active' : 'No Data',
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching protected area data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load protected area data.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified protected area.
     */
    public function edit(ProtectedArea $protectedArea)
    {
        return view('protected-areas.edit', compact('protectedArea'));
    }

    /**
     * Update the specified protected area in storage.
     */
    public function update(Request $request, ProtectedArea $protectedArea)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:protected_areas,code,' . $protectedArea->id,
            'name' => 'required|string|max:255',
        ]);

        try {
            $protectedArea->update($validated);
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Protected area updated successfully.'
                ]);
            }
            
            return redirect()->route('protected-areas.index')
                ->with('success', 'Protected area updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating protected area: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to update protected area.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to update protected area.')
                ->withInput();
        }
    }

    /**
     * Remove the specified protected area from storage.
     */
    public function destroy(ProtectedArea $protectedArea)
    {
        try {
            // Check if protected area has observations
            $observationCount = $protectedArea->getTotalObservationsCount();
            
            if ($observationCount > 0) {
                if (request()->expectsJson() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Cannot delete protected area with existing observations.'
                    ], 422);
                }
                
                return back()->with('error', 'Cannot delete protected area with existing observations.');
            }
            
            $protectedArea->delete();
            
            if (request()->expectsJson() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Protected area deleted successfully.'
                ]);
            }
            
            return redirect()->route('protected-areas.index')
                ->with('success', 'Protected area deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting protected area: ' . $e->getMessage());
            
            if (request()->expectsJson() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to delete protected area.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to delete protected area.');
        }
    }

    /**
     * Display the specified protected area site.
     */
    public function showSite(SiteName $siteName)
    {
        $siteName->load('protectedArea');
        return view('protected-area-sites.show', compact('siteName'));
    }

    /**
     * Get site data for View modal (AJAX)
     */
    public function getSiteData($id)
    {
        try {
            $siteName = SiteName::with('protectedArea')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'siteName' => [
                    'id' => $siteName->id,
                    'name' => $siteName->name,
                    'station_code' => $siteName->station_code,
                    'protected_area_id' => $siteName->protected_area_id,
                    'protected_area' => $siteName->protectedArea ? [
                        'id' => $siteName->protectedArea->id,
                        'code' => $siteName->protectedArea->code,
                        'name' => $siteName->protectedArea->name,
                    ] : null,
                    'status' => $siteName->protectedArea ? 'Active' : 'Unassigned',
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching site data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load site data.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified protected area site.
     */
    public function editSite(SiteName $siteName)
    {
        $protectedAreas = ProtectedArea::orderBy('name')->get();
        return view('protected-area-sites.edit', compact('siteName', 'protectedAreas'));
    }

    /**
     * Update the specified protected area site in storage.
     */
    public function updateSite(Request $request, SiteName $siteName)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'protected_area_id' => 'nullable|exists:protected_areas,id',
        ]);

        try {
            $siteName->update($validated);
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Site updated successfully.'
                ]);
            }
            
            return redirect()->route('protected-area-sites.index')
                ->with('success', 'Site updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating site: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to update site.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to update site.')
                ->withInput();
        }
    }

    /**
     * Remove the specified protected area site from storage.
     */
    public function destroySite(SiteName $siteName)
    {
        try {
            $siteName->delete();
            
            if (request()->expectsJson() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Site deleted successfully.'
                ]);
            }
            
            return redirect()->route('protected-area-sites.index')
                ->with('success', 'Site deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting site: ' . $e->getMessage());
            
            if (request()->expectsJson() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to delete site.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to delete site.');
        }
    }
}
