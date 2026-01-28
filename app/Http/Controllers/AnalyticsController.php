<?php

namespace App\Http\Controllers;

use App\Models\ProtectedArea;
use App\Models\BanganObservation;
use App\Models\BauaObservation;
use App\Models\BmsSpeciesObservation;
use App\Models\CasecnanObservation;
use App\Models\DipaniongObservation;
use App\Models\DupaxObservation;
use App\Models\FuyotObservation;
use App\Models\MadreObservation;
use App\Models\MadupapaObservation;
use App\Models\MangaObservation;
use App\Models\MarianoObservation;
use App\Models\PalauiObservation;
use App\Models\QuibalObservation;
use App\Models\QuirinoObservation;
use App\Models\SalinasObservation;
use App\Models\SanRoqueObservation;
use App\Models\ToyotaObservation;
use App\Models\TumauiniObservation;
use App\Models\WangagObservation;
use App\Models\MagapitObservation;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $protectedAreas = ProtectedArea::orderBy('name')->get();
        return view('analytics', compact('protectedAreas'));
    }

    public function getObservationData(Request $request)
    {
        $request->validate([
            'protected_area_id' => 'required|exists:protected_areas,id'
        ]);

        $protectedArea = ProtectedArea::findOrFail($request->protected_area_id);
        
        // Map area codes to their respective observation models
        $observationModels = [
            'BPLS' => BmsSpeciesObservation::class,
            'BWFR' => BauaObservation::class,
            'FSNP' => FuyotObservation::class,
            'MPL' => [MagapitObservation::class, MarianoObservation::class, MadupapaObservation::class],
            'PIPLS' => PalauiObservation::class,
            'PPLS' => [ToyotaObservation::class, SanRoqueObservation::class, MangaObservation::class, QuibalObservation::class],
            'QPL' => QuirinoObservation::class,
            'SANMARIANO' => MarianoObservation::class,
            'MADUPAPA' => MadupapaObservation::class,
            'WWFR' => WangagObservation::class,
            'TOYOTA' => ToyotaObservation::class,
            'SANROQUE' => SanRoqueObservation::class,
            'MANGA' => MangaObservation::class,
            'QUIBAL' => QuibalObservation::class,
            'NSMNP' => MadreObservation::class,
            'TWNP' => TumauiniObservation::class,
            'BHNP' => BanganObservation::class,
            'SNM' => SalinasObservation::class,
            'DWFR' => DupaxObservation::class,
            'CPL' => CasecnanObservation::class,
            'DNP' => DipaniongObservation::class,
        ];

        $yearlyData = [];
        $modelClass = $observationModels[$protectedArea->code] ?? null;

        if ($modelClass) {
            try {
                $allObservations = collect();
                
                if (is_array($modelClass)) {
                    // Handle multiple models (like MPL)
                    foreach ($modelClass as $singleModel) {
                        try {
                            $observations = $singleModel::selectRaw('patrol_year as year, COUNT(*) as count, SUM(recorded_count) as total_count')
                                ->whereNotNull('patrol_year')
                                ->groupBy('patrol_year')
                                ->get();
                            $allObservations = $allObservations->merge($observations);
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                    
                    // Aggregate by year
                    $groupedObservations = $allObservations->groupBy('year');
                    $observations = $groupedObservations->map(function ($yearGroup) {
                        return (object)[
                            'year' => $yearGroup->first()->year,
                            'count' => $yearGroup->sum('count'),
                            'total_count' => $yearGroup->sum('total_count')
                        ];
                    })->values()->sortBy('year')->values();
                } else {
                    // Handle single model
                    $observations = $modelClass::selectRaw('patrol_year as year, COUNT(*) as count, SUM(recorded_count) as total_count')
                        ->whereNotNull('patrol_year')
                        ->groupBy('patrol_year')
                        ->orderBy('patrol_year', 'asc')
                        ->get();
                }

                // Convert to cumulative data for the chart
                $cumulativeCount = 0;
                $yearlyCounts = [];
                
                foreach ($observations as $obs) {
                    $yearlyCounts[$obs->year] = $obs->count;
                    $cumulativeCount += $obs->count;
                    $yearlyData[] = [
                        'year' => $obs->year,
                        'count' => $cumulativeCount, // Cumulative total
                        'yearly_count' => $obs->count, // Individual year count
                        'total_count' => $obs->total_count // Total recorded individuals
                    ];
                }

                // Fill in missing years with zero counts if needed
                if (!empty($yearlyData)) {
                    $minYear = min(array_column($yearlyData, 'year'));
                    $maxYear = max(array_column($yearlyData, 'year'));
                    
                    $completeData = [];
                    $cumulativeCount = 0;
                    
                    for ($year = $minYear; $year <= $maxYear; $year++) {
                        $yearData = collect($yearlyData)->firstWhere('year', $year);
                        
                        if ($yearData) {
                            $cumulativeCount += $yearData['yearly_count'];
                            $completeData[] = [
                                'year' => $year,
                                'count' => $cumulativeCount,
                                'yearly_count' => $yearData['yearly_count'],
                                'total_count' => $yearData['total_count']
                            ];
                        } else {
                            $completeData[] = [
                                'year' => $year,
                                'count' => $cumulativeCount,
                                'yearly_count' => 0,
                                'total_count' => 0
                            ];
                        }
                    }
                    
                    $yearlyData = $completeData;
                }

            } catch (\Exception $e) {
                // If there's an error querying the table, return empty data
                $yearlyData = [];
            }
        }

        return response()->json([
            'protected_area' => [
                'id' => $protectedArea->id,
                'name' => $protectedArea->name,
                'code' => $protectedArea->code
            ],
            'data' => $yearlyData,
            'total_years' => count($yearlyData),
            'total_observations' => !empty($yearlyData) ? end($yearlyData)['count'] : 0
        ]);
    }
}
