<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
if(!$user) {
    echo "No user";
    exit;
}
$sanctumRole = \Spatie\Permission\Models\Role::findByName('Employee', 'sanctum');
$webRole = \Spatie\Permission\Models\Role::findByName('Employee', 'web');
try {
    $user->assignRole([$sanctumRole, $webRole]);
    echo "Success!";
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage();
}
