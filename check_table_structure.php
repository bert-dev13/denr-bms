<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Check structure of existing observation tables
$tables = ['baua_tbl', 'batanes_tbl', 'fuyot_tbl'];

foreach ($tables as $tableName) {
    if (\Schema::hasTable($tableName)) {
        echo "=== Structure of {$tableName} ===\n";
        $columns = \Schema::getColumnListing($tableName);
        foreach ($columns as $column) {
            $type = \Schema::getColumnType($tableName, $column);
            echo "- {$column}: {$type}\n";
        }
        echo "\n";
    } else {
        echo "Table {$tableName} does not exist\n\n";
    }
}

// Check if there are any species observation models
echo "=== Checking for Species Observation Models ===\n";
$modelPath = __DIR__ . '/app/Models';
if (is_dir($modelPath)) {
    $files = glob($modelPath . '/*Observation.php');
    foreach ($files as $file) {
        echo "- " . basename($file) . "\n";
    }
}
