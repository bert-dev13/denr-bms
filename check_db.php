<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Check protected areas
echo "=== Recent Protected Areas ===\n";
$areas = App\Models\ProtectedArea::orderBy('id', 'desc')->take(10)->get(['id', 'code', 'name']);

foreach ($areas as $area) {
    echo "ID: {$area->id}, Code: {$area->code}, Name: {$area->name}\n";
}

echo "\n=== Total Count ===\n";
echo "Total protected areas: " . App\Models\ProtectedArea::count() . "\n";

echo "\n=== Check for PA5138 ===\n";
$testArea = App\Models\ProtectedArea::where('code', 'PA5138')->first();
if ($testArea) {
    echo "Found PA5138: ID {$testArea->id}, Name: {$testArea->name}\n";
} else {
    echo "PA5138 not found\n";
}
