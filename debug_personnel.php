<?php

use App\Models\Personnel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $count = Personnel::count();
    echo 'Personnel Count: '.$count."\n";

    if ($count > 0) {
        $personnel = Personnel::all();
        echo 'First Personnel: '.json_encode($personnel->first())."\n";
    } else {
        echo "No personnel found.\n";
    }
} catch (\Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
