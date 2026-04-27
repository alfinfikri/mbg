<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo 'status_layanan=' . (Schema::hasColumn('sekolahs', 'status_layanan') ? 'yes' : 'no') . PHP_EOL;
echo 'status=' . (Schema::hasColumn('sekolahs', 'status') ? 'yes' : 'no') . PHP_EOL;
