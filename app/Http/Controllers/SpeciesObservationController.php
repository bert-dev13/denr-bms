<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BmsSpeciesObservation;
use App\Models\FuyotObservation;
use App\Models\QuirinoObservation;
use App\Models\PalauiObservation;
use App\Models\BauaObservation;
use App\Models\WangagObservation;
use App\Models\MagapitObservation;
use App\Models\MadupapaObservation;
use App\Models\MarianoObservation;
use App\Models\ToyotaObservation;
use App\Models\SanRoqueObservation;
use App\Models\MangaObservation;
use App\Models\QuibalObservation;
use App\Models\BaseObservation;
use App\Models\MadreObservation;
use App\Models\TumauiniObservation;
use App\Models\BanganObservation;
use App\Models\SalinasObservation;
use App\Models\DupaxObservation;
use App\Models\CasecnanObservation;
use App\Models\DipaniongObservation;
use App\Models\ProtectedArea;
use App\Models\SiteName;

class SpeciesObservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Handle export requests
        if ($request->has('export') || $request->has('print')) {
            return $this->handleExport($request);
        }
        
        // Log request parameters for debugging
        \Log::info('Species Observation Index Request:', [
            'protected_area_id' => $request->protected_area_id,
            'site_name' => $request->site_name,
            'bio_group' => $request->bio_group,
            'patrol_year' => $request->patrol_year,
            'patrol_semester' => $request->patrol_semester,
            'search' => $request->search,
            'filled_site_name' => $request->filled('site_name'),
        ]);
        
        // Get observations from all protected areas
        $query = null; // Initialize query variable
        $batanesQuery = $this->buildFilteredQuery(BmsSpeciesObservation::class, $request);
        $fuyotQuery = $this->buildFilteredQuery(FuyotObservation::class, $request);
        $quirinoQuery = $this->buildFilteredQuery(QuirinoObservation::class, $request);
        $palauiQuery = $this->buildFilteredQuery(PalauiObservation::class, $request);
        $buaaQuery = $this->buildFilteredQuery(BauaObservation::class, $request);
        $wangagQuery = $this->buildFilteredQuery(WangagObservation::class, $request);
        
        // For MPL, include all sub-tables
        $magapitQuery = $this->buildFilteredQuery(MagapitObservation::class, $request);
        $marianoQuery = $this->buildFilteredQuery(MarianoObservation::class, $request);
        $madupapaQuery = $this->buildFilteredQuery(MadupapaObservation::class, $request);
        
        // Check if we need to filter Mariano and Madupapa based on site selection
        if ($request->filled('protected_area_id') && $request->filled('site_name') && $request->site_name !== 'no_specific_site') {
            $siteName = SiteName::find($request->site_name);
            if ($siteName) {
                // Filter Mariano and Madupapa when their specific sites are selected
                if (strpos($siteName->name, 'San Mariano') !== false) {
                    // When San Mariano site is selected, only show MarianoObservation records
                    $marianoQuery = $this->buildFilteredQuery(MarianoObservation::class, $request);
                    $madupapaQuery = null; // Exclude Madupapa
                } elseif (strpos($siteName->name, 'Madupapa') !== false) {
                    // When Madupapa site is selected, only show MadupapaObservation records
                    $madupapaQuery = $this->buildFilteredQuery(MadupapaObservation::class, $request);
                    $marianoQuery = null; // Exclude Mariano
                }
            }
        }
        
        // Handle "no_specific_site" selection for MPL - show only Magapit records
        if ($request->filled('protected_area_id') && $request->site_name === 'no_specific_site') {
            $protectedArea = ProtectedArea::find($request->protected_area_id);
            if ($protectedArea && $protectedArea->code === 'MPL') {
                // For MPL with "no_specific_site", exclude Mariano and Madupapa tables
                $marianoQuery = null;
                $madupapaQuery = null;
            }
        }
        
        // Special handling for San Mariano and Madupapa sites
        if ($request->filled('site_name') && $request->site_name !== 'no_specific_site') {
            $siteName = SiteName::find($request->site_name);
            if ($siteName) {
                $stationCode = $siteName->getStationCodeAttribute();
                if ($stationCode) {
                    // Special handling for San Mariano site - show ONLY MarianoObservation records
                    if (strpos($siteName->name, 'San Mariano') !== false) {
                        // When San Mariano site is selected, only show MarianoObservation records
                        $query = $this->buildFilteredQuery(MarianoObservation::class, $request);
                    }
                    // Special handling for Madupapa site - show ONLY MadupapaObservation records
                    elseif (strpos($siteName->name, 'Madupapa') !== false) {
                        // When Madupapa site is selected, only show MadupapaObservation records
                        $query = $this->buildFilteredQuery(MadupapaObservation::class, $request);
                    }
                    // For other MPL sites, use the main union logic (which now only includes MagapitQuery for MPL)
                    else {
                        $query = null; // Will use union below
                    }
                }
            }
        } else {
            $query = null; // Will use union below
        }
        
        $madreQuery = $this->buildFilteredQuery(MadreObservation::class, $request);
        $tumauiniQuery = $this->buildFilteredQuery(TumauiniObservation::class, $request);
        $banganQuery = $this->buildFilteredQuery(BanganObservation::class, $request);
        $salinasQuery = $this->buildFilteredQuery(SalinasObservation::class, $request);
        $dupaxQuery = $this->buildFilteredQuery(DupaxObservation::class, $request);
        $casecnanQuery = $this->buildFilteredQuery(CasecnanObservation::class, $request);
        $dipaniongQuery = $this->buildFilteredQuery(DipaniongObservation::class, $request);
        
        // Add PPLS site-specific tables
        $toyotaQuery = $this->buildTableQuery('toyota_tbl', $request);
        $sanRoqueQuery = $this->buildTableQuery('roque_tbl', $request);
        $mangaQuery = $this->buildTableQuery('manga_tbl', $request);
        $quibalQuery = $this->buildTableQuery('quibal_tbl', $request);
        
        // Combine all queries with union
        if ($query) {
            // Use specific query for San Mariano or Madupapa sites
            $observations = $query->orderBy('patrol_year', 'desc')
                ->orderBy('patrol_semester', 'desc')
                ->orderBy('station_code')
                ->paginate(20);
        } else {
            // Use union of all queries for other cases
            $allQueries = [
                $batanesQuery, $fuyotQuery, $quirinoQuery, $palauiQuery, 
                $buaaQuery, $wangagQuery, $magapitQuery, $marianoQuery, 
                $madupapaQuery, $madreQuery, $tumauiniQuery, $banganQuery, 
                $salinasQuery, $dupaxQuery, $casecnanQuery, $dipaniongQuery, 
                $toyotaQuery, $sanRoqueQuery, $mangaQuery, $quibalQuery
            ];
            
            // Filter out null queries (when specific sites are selected)
            $allQueries = array_filter($allQueries, function($query) {
                return $query !== null;
            });
            
            // Re-index array to avoid issues with union operations
            $allQueries = array_values($allQueries);
            
            $observations = $allQueries[0];
            for ($i = 1; $i < count($allQueries); $i++) {
                $observations = $observations->union($allQueries[$i]);
            }
            
            $observations = $observations->orderBy('patrol_year', 'desc')
                ->orderBy('patrol_semester', 'desc')
                ->orderBy('station_code')
                ->paginate(20);
        }

        // Load protected area relationships for all observations
        $observations->getCollection()->each(function ($observation) {
            $observation->load('protectedArea');
        });

        // Get filter options
        $filterOptions = $this->getFilterOptions();

        return view('species-observations.enhanced-index', compact(
            'observations',
            'filterOptions'
        ));
    }

    /**
     * Build a filtered query for database tables
     */
    private function buildTableQuery($tableName, Request $request)
    {
        $query = \DB::table($tableName)->select(
            'id',
            'protected_area_id',
            'transaction_code',
            'station_code',
            'patrol_year',
            'patrol_semester',
            'bio_group',
            'common_name',
            'scientific_name',
            'recorded_count',
            'created_at',
            'updated_at',
            \DB::raw("'" . $tableName . "' as table_name")
        );
        
        // Apply search filter if provided
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm, $tableName) {
                $q->where('common_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('scientific_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('station_code', 'like', '%' . $searchTerm . '%')
                  ->orWhere('transaction_code', 'like', '%' . $searchTerm . '%')
                  ->orWhereExists(function($subQuery) use ($searchTerm, $tableName) {
                      $subQuery->select(\DB::raw(1))
                          ->from('protected_areas')
                          ->whereRaw('protected_areas.id = ' . $tableName . '.protected_area_id')
                          ->where('protected_areas.name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }
        
        // Apply other filters if they exist
        $filters = [
            'protected_area_id' => 'protected_area_id',
            'bio_group' => 'bio_group', 
            'patrol_year' => 'patrol_year',
            'patrol_semester' => 'patrol_semester'
        ];

        foreach ($filters as $requestKey => $dbField) {
            if ($request->filled($requestKey)) {
                $query->where($dbField, $request->$requestKey);
            }
        }

        // Handle site name filter separately since it needs to be converted to station_code
        if ($request->filled('site_name')) {
            // Skip site filtering if "no_specific_site" is selected
            if ($request->site_name === 'no_specific_site') {
                // Don't apply any site filtering - show all records
                return $query;
            }
            
            $siteName = SiteName::find($request->site_name);
            if ($siteName) {
                $stationCode = $siteName->getStationCodeAttribute();
                if ($stationCode) {
                    // Only apply exact station code filtering for PPLS tables
                    $query->where('station_code', $stationCode);
                }
            }
        }

        return $query;
    }

    /**
     * Build a filtered query for observation models
     */
    private function buildFilteredQuery($modelClass, Request $request)
    {
        // Get table name from model
        $model = new $modelClass;
        $tableName = $model->getTable();
        
        $query = $modelClass::select(
            'id',
            'protected_area_id',
            'transaction_code',
            'station_code',
            'patrol_year',
            'patrol_semester',
            'bio_group',
            'common_name',
            'scientific_name',
            'recorded_count',
            'created_at',
            'updated_at',
            \DB::raw("'" . $tableName . "' as table_name")
        );
        
        // Apply search filter if provided
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('common_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('scientific_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('station_code', 'like', '%' . $searchTerm . '%')
                  ->orWhere('transaction_code', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('protectedArea', function($subQuery) use ($searchTerm) {
                      $subQuery->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }
        
        // Apply other filters if they exist
        $filters = [
            'protected_area_id' => 'protected_area_id',
            'bio_group' => 'bio_group', 
            'patrol_year' => 'patrol_year',
            'patrol_semester' => 'patrol_semester'
        ];

        foreach ($filters as $requestKey => $dbField) {
            if ($request->filled($requestKey)) {
                $query->where($dbField, $request->$requestKey);
            }
        }

        return $query;
    }

    /**
     * Get filter options for the view
     */
    private function getFilterOptions()
    {
        return [
            'protectedAreas' => ProtectedArea::orderBy('name')->get(),
            'bioGroups' => ['fauna' => 'Fauna', 'flora' => 'Flora'],
            'years' => $this->getAllYears(),
            'semesters' => [1 => '1st', 2 => '2nd'],
            'siteNames' => SiteName::orderBy('name')->get()
        ];
    }

    /**
     * Get site names for a specific protected area (AJAX)
     */
    public function getSiteNames($protectedAreaId)
    {
        $protectedArea = ProtectedArea::find($protectedAreaId);
        
        if ($protectedArea) {
            // Use the proper relationship to get site names for this protected area
            $siteNames = SiteName::where('protected_area_id', $protectedAreaId)
                ->orderBy('name')
                ->get();
            
            // Fallback to name-based filtering if no results found (for backward compatibility)
            if ($siteNames->isEmpty()) {
                $siteNames = SiteName::where(function($query) use ($protectedArea) {
                    if ($protectedArea->code === 'PPLS') {
                        $query->where('name', 'like', 'PPLS Site%');
                    } elseif ($protectedArea->code === 'MPL') {
                        $query->where('name', 'like', 'MPL SITE%')
                              ->orWhere('name', 'like', 'MPL Site%'); // Handle both cases
                    }
                })->orderBy('name')->get();
            }
            
            return response()->json($siteNames);
        }
        
        return response()->json([]);
    }

    /**
     * Get all unique years from all observation tables
     */
    private function getAllYears()
    {
        return BmsSpeciesObservation::distinct()->pluck('patrol_year')->sort()->reverse()
                    ->merge(FuyotObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(QuirinoObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(PalauiObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(BauaObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(WangagObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(MagapitObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(MadupapaObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(MarianoObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(ToyotaObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(SanRoqueObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(MangaObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(QuibalObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(MadreObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(TumauiniObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(BanganObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(SalinasObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(DupaxObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(CasecnanObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->merge(DipaniongObservation::distinct()->pluck('patrol_year')->sort()->reverse())
                    ->unique()
                    ->sort()
                    ->reverse();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $protectedAreas = ProtectedArea::orderBy('name')->get();
        $bioGroups = ['fauna' => 'Fauna', 'flora' => 'Flora'];
        $semesters = [1 => '1st', 2 => '2nd'];

        return view('species-observations.create', compact(
            'protectedAreas',
            'bioGroups',
            'semesters'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'protected_area_id' => 'required|exists:protected_areas,id',
                'transaction_code' => 'required|string|max:50',
                'station_code' => 'required|string|max:60',
                'patrol_year' => 'required|integer|min:2000|max:2100',
                'patrol_semester' => 'required|integer|in:1,2',
                'bio_group' => 'required|in:fauna,flora',
                'common_name' => 'required|string|max:150',
                'scientific_name' => 'nullable|string|max:200',
                'recorded_count' => 'required|integer|min:0',
                'site_name' => 'nullable|exists:site_names,id',
            ], [], [
                'protected_area_id' => 'protected area',
                'transaction_code' => 'transaction code',
                'station_code' => 'station code',
                'patrol_year' => 'patrol year',
                'patrol_semester' => 'patrol semester',
                'bio_group' => 'bio group',
                'common_name' => 'common name',
                'scientific_name' => 'scientific name',
                'recorded_count' => 'recorded count',
                'site_name' => 'site name',
            ]);

        // Get the protected area to determine which table to use
        $protectedArea = ProtectedArea::find($validated['protected_area_id']);
        
        // Save to the appropriate table based on protected area
        if ($protectedArea && $protectedArea->code === 'PPLS') {
            // For PPLS, determine which site table based on selected site name
            if (!empty($validated['site_name'])) {
                $siteName = SiteName::find($validated['site_name']);
                if ($siteName) {
                    // Set station code based on selected site
                    $validated['station_code'] = $siteName->getStationCodeAttribute();
                    
                    // Determine which table to use based on site name
                    if (strpos($siteName->name, 'Toyota') !== false) {
                        ToyotaObservation::create($validated);
                    } elseif (strpos($siteName->name, 'San Roque') !== false) {
                        SanRoqueObservation::create($validated);
                    } elseif (strpos($siteName->name, 'Manga') !== false) {
                        MangaObservation::create($validated);
                    } elseif (strpos($siteName->name, 'Quibal') !== false) {
                        QuibalObservation::create($validated);
                    } else {
                        // Default to Toyota if no specific site identified
                        ToyotaObservation::create($validated);
                    }
                } else {
                    // Fallback to station code logic if site name not found
                    if (strpos($validated['station_code'], 'Toyota') !== false) {
                        ToyotaObservation::create($validated);
                    } elseif (strpos($validated['station_code'], 'San Roque') !== false) {
                        SanRoqueObservation::create($validated);
                    } elseif (strpos($validated['station_code'], 'Manga') !== false) {
                        MangaObservation::create($validated);
                    } elseif (strpos($validated['station_code'], 'Quibal') !== false) {
                        QuibalObservation::create($validated);
                    } else {
                        // Default to Toyota if no specific site identified
                        ToyotaObservation::create($validated);
                    }
                }
            } else {
                // No site name selected, use station code logic
                if (strpos($validated['station_code'], 'Toyota') !== false) {
                    ToyotaObservation::create($validated);
                } elseif (strpos($validated['station_code'], 'San Roque') !== false) {
                    SanRoqueObservation::create($validated);
                } elseif (strpos($validated['station_code'], 'Manga') !== false) {
                    MangaObservation::create($validated);
                } elseif (strpos($validated['station_code'], 'Quibal') !== false) {
                    QuibalObservation::create($validated);
                } else {
                    // Default to Toyota if no specific site identified
                    ToyotaObservation::create($validated);
                }
            }
        } else {
            // For other protected areas, use their specific tables
            switch ($protectedArea->code) {
                case 'BPLS':
                    BmsSpeciesObservation::create($validated);
                    break;
                case 'FSNP':
                    FuyotObservation::create($validated);
                    break;
                case 'QPL':
                    QuirinoObservation::create($validated);
                    break;
                case 'PIPLS':
                    PalauiObservation::create($validated);
                    break;
                case 'BWFR':
                    BauaObservation::create($validated);
                    break;
                case 'WWFR':
                    WangagObservation::create($validated);
                    break;
                case 'MPL':
                    MagapitObservation::create($validated);
                    break;
                case 'MADUPAPA':
                    MadupapaObservation::create($validated);
                    break;
                case 'SANMARIANO':
                    MarianoObservation::create($validated);
                    break;
                case 'NSMNP':
                    MadreObservation::create($validated);
                    break;
                case 'TWNP':
                    TumauiniObservation::create($validated);
                    break;
                case 'BHNP':
                    BanganObservation::create($validated);
                    break;
                case 'SNM':
                    SalinasObservation::create($validated);
                    break;
                case 'DWFR':
                    DupaxObservation::create($validated);
                    break;
                case 'CPL':
                    CasecnanObservation::create($validated);
                    break;
                case 'DNP':
                    DipaniongObservation::create($validated);
                    break;
                default:
                    // Default to BmsSpeciesObservation if no specific table found
                    BmsSpeciesObservation::create($validated);
                    break;
            }
        }

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true
            ]);
        }

        return redirect()->route('species-observations.index');
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            
            // For non-AJAX requests, redirect back with errors
            throw $e;
        } catch (\Exception $e) {
            // Handle other exceptions
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while adding the observation.'
                ], 500);
            }
            
            // For non-AJAX requests, redirect back with error message
            return redirect()->route('species-observations.index')
                           ->with('error', 'An error occurred while adding the observation.');
        }
    }

    /**
     * Get observation data for Edit modal
     */
    public function getObservationForEdit($id)
    {
        $tableName = request()->query('table_name');
        \Log::info('getObservationForEdit called with ID: ' . $id . ', tableName: ' . $tableName);
        
        try {
            // Find the observation across all tables
            $observation = $this->findObservationById($id, $tableName);
            
            if (!$observation) {
                \Log::error('Observation not found for editing with ID: ' . $id);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Observation not found.'
                ], 404);
            }

            // Check if observation still exists in database before trying to load relationships
            if (!$observation->exists) {
                \Log::error('Observation no longer exists for editing with ID: ' . $id);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Observation no longer exists.'
                ], 410);
            }

            // Load the protected area relationship
            try {
                $observation->load('protectedArea');
            } catch (\Exception $e) {
                \Log::error('Failed to load protected area relationship for editing: ' . $e->getMessage());
            }

            // Get table name
            $tableName = $observation->getTable();
            
            // Prepare response data
            $observationData = [
                'id' => $observation->id,
                'table_name' => $tableName,
                'protected_area_id' => $observation->protected_area_id,
                'transaction_code' => $observation->transaction_code,
                'station_code' => $observation->station_code,
                'patrol_year' => $observation->patrol_year,
                'patrol_semester' => $observation->patrol_semester,
                'bio_group' => $observation->bio_group,
                'common_name' => $observation->common_name,
                'scientific_name' => $observation->scientific_name,
                'recorded_count' => $observation->recorded_count,
                'site_name_id' => null,
            ];

            // Try to get site name ID if applicable
            if (in_array($tableName, ['toyota_tbl', 'roque_tbl', 'manga_tbl', 'quibal_tbl'])) {
                // For PPLS site tables, try to find corresponding site name
                $siteName = SiteName::where('name', 'like', '%PPLS Site%')
                    ->where(function($query) use ($observation) {
                        $query->where('name', 'like', '%Toyota%')
                              ->orWhere('name', 'like', '%San Roque%')
                              ->orWhere('name', 'like', '%Manga%')
                              ->orWhere('name', 'like', '%Quibal%');
                    })
                    ->first();
                
                if ($siteName) {
                    $observationData['site_name_id'] = $siteName->id;
                }
            } elseif (in_array($tableName, ['magapit_tbl', 'mariano_tbl', 'madupapa_tbl'])) {
                // For MPL site tables, try to find corresponding site name
                $siteName = SiteName::where('name', 'like', '%MPL SITE%')
                    ->where(function($query) use ($observation) {
                        $query->where('name', 'like', '%Magapit%')
                              ->orWhere('name', 'like', '%San Mariano%')
                              ->orWhere('name', 'like', '%Madupapa%');
                    })
                    ->first();
                
                if ($siteName) {
                    $observationData['site_name_id'] = $siteName->id;
                }
            }
            
            return response()->json([
                'success' => true,
                'observation' => $observationData
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching observation data for editing: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' at line ' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load observation data for editing.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'protected_area_id' => 'required|exists:protected_areas,id',
                'transaction_code' => 'required|string|max:50',
                'station_code' => 'required|string|max:60',
                'patrol_year' => 'required|integer|min:2000|max:2100',
                'patrol_semester' => 'required|integer|in:1,2',
                'bio_group' => 'required|in:fauna,flora',
                'common_name' => 'required|string|max:150',
                'scientific_name' => 'nullable|string|max:200',
                'recorded_count' => 'required|integer|min:0',
                'site_name' => 'nullable|exists:site_names,id',
                'table_name' => 'required|string',
            ], [], [
                'protected_area_id' => 'protected area',
                'transaction_code' => 'transaction code',
                'station_code' => 'station code',
                'patrol_year' => 'patrol year',
                'patrol_semester' => 'patrol semester',
                'bio_group' => 'bio group',
                'common_name' => 'common name',
                'scientific_name' => 'scientific name',
                'recorded_count' => 'recorded count',
                'site_name' => 'site name',
                'table_name' => 'table name',
            ]);

            // Find the observation to update
            $observation = $this->findObservationById($id, $validated['table_name']);
            
            if (!$observation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Observation not found.'
                ], 404);
            }

            // Get the protected area to determine which table to use
            $protectedArea = ProtectedArea::find($validated['protected_area_id']);
            
            // Handle table changes if protected area is different
            $currentTableName = $observation->getTable();
            $targetTableName = null;
            
            if ($protectedArea && $protectedArea->code === 'PPLS') {
                // For PPLS, determine which site table based on selected site name
                if (!empty($validated['site_name'])) {
                    $siteName = SiteName::find($validated['site_name']);
                    if ($siteName) {
                        // Set station code based on selected site
                        $validated['station_code'] = $siteName->getStationCodeAttribute();
                        
                        // Determine which table to use based on site name
                        if (strpos($siteName->name, 'Toyota') !== false) {
                            $targetTableName = 'toyota_tbl';
                        } elseif (strpos($siteName->name, 'San Roque') !== false) {
                            $targetTableName = 'roque_tbl';
                        } elseif (strpos($siteName->name, 'Manga') !== false) {
                            $targetTableName = 'manga_tbl';
                        } elseif (strpos($siteName->name, 'Quibal') !== false) {
                            $targetTableName = 'quibal_tbl';
                        } else {
                            $targetTableName = 'toyota_tbl';
                        }
                    }
                }
            } elseif ($protectedArea) {
                // For other protected areas, use their specific tables
                $tableMap = [
                    'BPLS' => 'batanes_tbl',
                    'FSNP' => 'fuyot_tbl',
                    'QPL' => 'quirino_tbl',
                    'PIPLS' => 'palaui_tbl',
                    'BWFR' => 'buaa_tbl',
                    'WWFR' => 'wangag_tbl',
                    'MPL' => 'magapit_tbl',
                    'MADUPAPA' => 'madupapa_tbl',
                    'SANMARIANO' => 'mariano_tbl',
                    'NSMNP' => 'madre_tbl',
                    'TWNP' => 'tumauini_tbl',
                    'BHNP' => 'bangan_tbl',
                    'SNM' => 'salinas_tbl',
                    'DWFR' => 'dupax_tbl',
                    'CPL' => 'casecnan_tbl',
                    'DNP' => 'dipaniong_tbl',
                ];
                
                $targetTableName = $tableMap[$protectedArea->code] ?? null;
            }

            // If table needs to change, delete from old table and create in new table
            if ($targetTableName && $targetTableName !== $currentTableName) {
                // Delete from current table
                $observation->delete();
                
                // Create in new table
                $targetModel = $this->getModelByTableName($targetTableName);
                if ($targetModel) {
                    $targetModel::create($validated);
                }
            } else {
                // Update in same table
                $observation->update($validated);
            }

            return response()->json([
                'success' => true,
                'message' => 'Observation updated successfully!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating observation: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' at line ' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the observation.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BaseObservation $speciesObservation)
    {
        $speciesObservation->load('protectedArea');
        return view('species-observations.show', compact('speciesObservation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BaseObservation $speciesObservation)
    {
        $protectedAreas = ProtectedArea::orderBy('name')->get();
        $bioGroups = ['fauna' => 'Fauna', 'flora' => 'Flora'];
        $semesters = [1 => '1st', 2 => '2nd'];

        return view('species-observations.edit', compact(
            'speciesObservation',
            'protectedAreas',
            'bioGroups',
            'semesters'
        ));
    }

    /**
     * Get observation data for View modal
     */
    public function getObservationData($id)
    {
        try {
            $tableName = request()->query('table_name');
            \Log::info('getObservationData called with ID: ' . $id . ', tableName: ' . $tableName);
            
            // Find the observation across all tables
            $observation = $this->findObservationById($id, $tableName);
            
            \Log::info('getObservationData - findObservationById returned:', [
                'found' => $observation ? true : false,
                'class' => $observation ? get_class($observation) : null,
                'table' => $observation ? $observation->getTable() : null,
                'protected_area_id' => $observation ? $observation->protected_area_id : null,
                'common_name' => $observation ? $observation->common_name : null,
                'station_code' => $observation ? $observation->station_code : null
            ]);
            
            if (!$observation) {
                \Log::error('Observation not found with ID: ' . $id);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Observation not found.'
                ], 404);
            }

            // Check if observation still exists in database before trying to load relationships
            if (!$observation->exists) {
                \Log::error('Observation no longer exists with ID: ' . $id);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Observation no longer exists.'
                ], 410);
            }

            // Load the protected area relationship (this might fail if observation was deleted)
            try {
                $observation->load('protectedArea');
            } catch (\Exception $e) {
                \Log::error('Failed to load protected area relationship: ' . $e->getMessage());
                
                // Continue without the relationship if it fails
                \Log::warning('Continuing without protected area data due to error: ' . $e->getMessage());
            }
            
            \Log::info('getObservationData - Final observation data:', [
                'id' => $observation->id,
                'table_name' => $observation->getTable(),
                'protected_area_id' => $observation->protected_area_id,
                'protected_area_name' => $observation->protectedArea->name ?? 'Not loaded',
                'common_name' => $observation->common_name,
                'station_code' => $observation->station_code
            ]);
            
            return response()->json([
                'success' => true,
                'observation' => $observation
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching observation data: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' at line ' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load observation data.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        // Debug: Log the ID being deleted
        \Log::info('Attempting to delete observation with ID: ' . $id);
        
        // Get the table name from the request if provided
        $tableName = $request->input('table_name');
        \Log::info('Table name from request: ' . $tableName);
        
        try {
            // Find the observation across all tables
            $observation = $this->findObservationById($id, $tableName);
            
            if (!$observation) {
                \Log::error('Observation not found with ID: ' . $id);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Species observation not found.'
                    ], 404);
                }
                
                return redirect()->route('species-observations.index')
                               ->with('error', 'Species observation not found.');
            }

            \Log::info('Found observation in table: ' . get_class($observation));

            // Check if the observation can be deleted (verify it exists)
            if (!$observation->exists) {
                \Log::error('Observation no longer exists with ID: ' . $id);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Observation no longer exists.'
                    ], 410);
                }
                
                return redirect()->route('species-observations.index')
                               ->with('error', 'Observation no longer exists.');
            }

            // Perform the deletion
            $deleted = $observation->delete();
            
            if (!$deleted) {
                \Log::error('Failed to delete observation with ID: ' . $id);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Failed to delete species observation.'
                    ], 500);
                }
                
                return redirect()->route('species-observations.index')
                               ->with('error', 'Failed to delete species observation.');
            }
            
            \Log::info('Successfully deleted observation with ID: ' . $id);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Species observation deleted successfully.'
                ]);
            }
            
            return redirect()->route('species-observations.index')
                           ->with('success', 'Species observation deleted successfully.');
                           
        } catch (\Exception $e) {
            \Log::error('Exception during delete: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' at line ' . $e->getLine());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'An unexpected error occurred while deleting the observation.'
                ], 500);
            }
            
            return redirect()->route('species-observations.index')
                           ->with('error', 'An unexpected error occurred while deleting the observation.');
        }
    }

    /**
     * Find observation by ID across all tables
     */
    private function findObservationById($id, $tableName = null)
    {
        \Log::info('findObservationById called with ID: ' . $id . ', tableName: ' . $tableName);
        
        // If table name is provided, try to find the specific model for that table FIRST
        if ($tableName) {
            $model = $this->getModelByTableName($tableName);
            if ($model) {
                $observation = $model::find($id);
                if ($observation) {
                    \Log::info('Found observation in specified table model: ' . $model . ' with ID: ' . $id);
                    return $observation;
                }
            }
        }
        
        // Only check all models if no specific table was provided or not found in specified table
        \Log::info('Table name not provided or not found in specified table, checking all models');
        
        $models = [
            BmsSpeciesObservation::class,
            FuyotObservation::class,
            QuirinoObservation::class,
            PalauiObservation::class,
            BauaObservation::class,
            WangagObservation::class,
            MagapitObservation::class,
            MadupapaObservation::class,
            MarianoObservation::class,
            MadreObservation::class,
            TumauiniObservation::class,
            BanganObservation::class,
            SalinasObservation::class,
            DupaxObservation::class,
            CasecnanObservation::class,
            DipaniongObservation::class,
            ToyotaObservation::class,
            SanRoqueObservation::class,
            MangaObservation::class,
            QuibalObservation::class,
        ];

        foreach ($models as $model) {
            $observation = $model::find($id);
            if ($observation) {
                \Log::info('Found observation in model: ' . $model . ' with ID: ' . $id . ' (table: ' . $observation->getTable() . ')');
                return $observation;
            }
        }

        \Log::error('Observation not found in any model with ID: ' . $id);
        return null;
    }

    /**
     * Get model class by table name
     */
    private function getModelByTableName($tableName)
    {
        $tableModelMap = [
            'batanes_tbl' => BmsSpeciesObservation::class,
            'fuyot_tbl' => FuyotObservation::class,
            'quirino_tbl' => QuirinoObservation::class,
            'palaui_tbl' => PalauiObservation::class,
            'buaa_tbl' => BauaObservation::class,
            'wangag_tbl' => WangagObservation::class,
            'magapit_tbl' => MagapitObservation::class,
            'madupapa_tbl' => MadupapaObservation::class,
            'mariano_tbl' => MarianoObservation::class,
            'madre_tbl' => MadreObservation::class,
            'tumauini_tbl' => TumauiniObservation::class,
            'bangan_tbl' => BanganObservation::class,
            'salinas_tbl' => SalinasObservation::class,
            'dupax_tbl' => DupaxObservation::class,
            'casecnan_tbl' => CasecnanObservation::class,
            'dipaniong_tbl' => DipaniongObservation::class,
            'toyota_tbl' => ToyotaObservation::class,
            'roque_tbl' => SanRoqueObservation::class,
            'manga_tbl' => MangaObservation::class,
            'quibal_tbl' => QuibalObservation::class,
        ];

        return $tableModelMap[$tableName] ?? null;
    }

    /**
     * Handle export requests for different formats
     */
    private function handleExport(Request $request)
    {
        // Get the same filtered data as the index method but without pagination
        $query = null;
        $batanesQuery = $this->buildFilteredQuery(BmsSpeciesObservation::class, $request);
        $fuyotQuery = $this->buildFilteredQuery(FuyotObservation::class, $request);
        $quirinoQuery = $this->buildFilteredQuery(QuirinoObservation::class, $request);
        $palauiQuery = $this->buildFilteredQuery(PalauiObservation::class, $request);
        $buaaQuery = $this->buildFilteredQuery(BauaObservation::class, $request);
        $wangagQuery = $this->buildFilteredQuery(WangagObservation::class, $request);
        
        // For MPL, include all sub-tables
        $magapitQuery = $this->buildFilteredQuery(MagapitObservation::class, $request);
        $marianoQuery = $this->buildFilteredQuery(MarianoObservation::class, $request);
        $madupapaQuery = $this->buildFilteredQuery(MadupapaObservation::class, $request);
        
        // Check if we need to filter Mariano and Madupapa based on site selection
        if ($request->filled('protected_area_id') && $request->filled('site_name') && $request->site_name !== 'no_specific_site') {
            $siteName = SiteName::find($request->site_name);
            if ($siteName) {
                if (strpos($siteName->name, 'San Mariano') !== false) {
                    $marianoQuery = $this->buildFilteredQuery(MarianoObservation::class, $request);
                    $madupapaQuery = null;
                } elseif (strpos($siteName->name, 'Madupapa') !== false) {
                    $madupapaQuery = $this->buildFilteredQuery(MadupapaObservation::class, $request);
                    $marianoQuery = null;
                }
            }
        }
        
        // Handle "no_specific_site" selection for MPL
        if ($request->filled('protected_area_id') && $request->site_name === 'no_specific_site') {
            $protectedArea = ProtectedArea::find($request->protected_area_id);
            if ($protectedArea && $protectedArea->code === 'MPL') {
                $marianoQuery = null;
                $madupapaQuery = null;
            }
        }
        
        // Special handling for San Mariano and Madupapa sites
        if ($request->filled('site_name') && $request->site_name !== 'no_specific_site') {
            $siteName = SiteName::find($request->site_name);
            if ($siteName) {
                $stationCode = $siteName->getStationCodeAttribute();
                if ($stationCode) {
                    if (strpos($siteName->name, 'San Mariano') !== false) {
                        $query = $this->buildFilteredQuery(MarianoObservation::class, $request);
                    } elseif (strpos($siteName->name, 'Madupapa') !== false) {
                        $query = $this->buildFilteredQuery(MadupapaObservation::class, $request);
                    } else {
                        $query = null;
                    }
                }
            }
        } else {
            $query = null;
        }
        
        $madreQuery = $this->buildFilteredQuery(MadreObservation::class, $request);
        $tumauiniQuery = $this->buildFilteredQuery(TumauiniObservation::class, $request);
        $banganQuery = $this->buildFilteredQuery(BanganObservation::class, $request);
        $salinasQuery = $this->buildFilteredQuery(SalinasObservation::class, $request);
        $dupaxQuery = $this->buildFilteredQuery(DupaxObservation::class, $request);
        $casecnanQuery = $this->buildFilteredQuery(CasecnanObservation::class, $request);
        $dipaniongQuery = $this->buildFilteredQuery(DipaniongObservation::class, $request);
        
        // Add PPLS site-specific tables
        $toyotaQuery = $this->buildTableQuery('toyota_tbl', $request);
        $sanRoqueQuery = $this->buildTableQuery('roque_tbl', $request);
        $mangaQuery = $this->buildTableQuery('manga_tbl', $request);
        $quibalQuery = $this->buildTableQuery('quibal_tbl', $request);
        
        // Combine all queries with union
        if ($query) {
            $observations = $query->orderBy('patrol_year', 'desc')
                ->orderBy('patrol_semester', 'desc')
                ->orderBy('station_code')
                ->get();
        } else {
            $allQueries = [
                $batanesQuery, $fuyotQuery, $quirinoQuery, $palauiQuery, 
                $buaaQuery, $wangagQuery, $magapitQuery, $marianoQuery, 
                $madupapaQuery, $madreQuery, $tumauiniQuery, $banganQuery, 
                $salinasQuery, $dupaxQuery, $casecnanQuery, $dipaniongQuery, 
                $toyotaQuery, $sanRoqueQuery, $mangaQuery, $quibalQuery
            ];
            
            $allQueries = array_filter($allQueries, function($query) {
                return $query !== null;
            });
            
            $allQueries = array_values($allQueries);
            
            if (empty($allQueries)) {
                $observations = collect();
            } else {
                $observations = $allQueries[0];
                for ($i = 1; $i < count($allQueries); $i++) {
                    $observations = $observations->union($allQueries[$i]);
                }
                $observations = $observations->orderBy('patrol_year', 'desc')
                    ->orderBy('patrol_semester', 'desc')
                    ->orderBy('station_code')
                    ->get();
            }
        }

        // Load protected area relationships
        $observations->each(function ($observation) {
            $observation->load('protectedArea');
        });

        // Handle different export formats
        if ($request->has('print')) {
            return $this->exportPrint($observations, $request);
        } elseif ($request->has('excel')) {
            return $this->exportExcel($observations, $request);
        } elseif ($request->has('pdf')) {
            return $this->exportPdf($observations, $request);
        }
        
        return back()->with('error', 'Invalid export format');
    }

    /**
     * Export to print-friendly view
     */
    private function exportPrint($observations, Request $request)
    {
        // Get filter information for title
        $filterInfo = $this->getFilterInfo($request);
        
        return view('species-observations.print', compact('observations', 'filterInfo'));
    }

    /**
     * Export to Excel
     */
    private function exportExcel($observations, Request $request)
    {
        $filename = 'species-observations-' . date('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($observations) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // CSV header
            fputcsv($file, [
                'Protected Area',
                'Station Code', 
                'Transaction Code',
                'Patrol Year',
                'Patrol Semester',
                'Bio Group',
                'Common Name',
                'Scientific Name',
                'Count'
            ]);
            
            // Data rows
            foreach ($observations as $observation) {
                fputcsv($file, [
                    $observation->protectedArea->name ?? 'N/A',
                    $observation->station_code ?? 'N/A',
                    $observation->transaction_code ?? 'N/A',
                    $observation->patrol_year ?? 'N/A',
                    $observation->patrol_semester_text ?? 'N/A',
                    ucfirst($observation->bio_group ?? 'N/A'),
                    $observation->common_name ?? 'N/A',
                    $observation->scientific_name ?? 'N/A',
                    $observation->recorded_count ?? 0
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF
     */
    private function exportPdf($observations, Request $request)
    {
        // Limit the number of records for PDF to prevent memory issues
        $maxRecords = 100;
        $totalRecords = $observations->count();
        
        if ($totalRecords > $maxRecords) {
            return back()->with('error', "PDF export is limited to {$maxRecords} records. Your dataset has {$totalRecords} records. Please use Excel export for larger datasets.");
        }
        
        $filename = 'species-observations-' . date('Y-m-d-H-i-s') . '.pdf';
        
        // Get filter information for title
        $filterInfo = $this->getFilterInfo($request);
        
        // Configure DomPDF for memory efficiency
        $options = [
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false,
            'debugPng' => false,
            'debugKeepTemp' => false,
            'debugCss' => false,
            'debugLayout' => false,
            'debugLayoutLines' => false,
            'debugLayoutBlocks' => false,
            'debugLayoutInline' => false,
            'debugLayoutPaddingBox' => false,
        ];
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions($options)
            ->loadView('species-observations.pdf', compact('observations', 'filterInfo'));
        
        return $pdf->download($filename);
    }

    /**
     * Get filter information for export titles
     */
    private function getFilterInfo(Request $request)
    {
        $filterInfo = [];
        
        // Protected Area filter
        if ($request->filled('protected_area_id')) {
            $protectedArea = ProtectedArea::find($request->protected_area_id);
            if ($protectedArea) {
                $filterInfo['protected_area'] = $protectedArea->name;
            }
        }
        
        // Site Name filter
        if ($request->filled('site_name') && $request->site_name !== 'no_specific_site') {
            $siteName = SiteName::find($request->site_name);
            if ($siteName) {
                $filterInfo['site_name'] = $siteName->name;
            }
        } elseif ($request->site_name === 'no_specific_site') {
            $filterInfo['site_name'] = 'No Specific Site';
        }
        
        // Bio Group filter
        if ($request->filled('bio_group')) {
            $filterInfo['bio_group'] = ucfirst($request->bio_group);
        }
        
        // Year filter
        if ($request->filled('patrol_year')) {
            $filterInfo['patrol_year'] = $request->patrol_year;
        }
        
        // Semester filter
        if ($request->filled('patrol_semester')) {
            $semesters = [1 => '1st', 2 => '2nd'];
            $filterInfo['patrol_semester'] = $semesters[$request->patrol_semester] ?? $request->patrol_semester;
        }
        
        return $filterInfo;
    }
}
