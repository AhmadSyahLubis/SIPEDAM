<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the user (id=5)
$user = \App\Models\User::where('role', '!=', 'admin')->first();
if (!$user) {
    die("No user found");
}

$query = \App\Models\Report::with(['category', 'user', 'attachments']);
$query->where('user_id', $user->id);

/*
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $query->where('status', $_GET['status']);
}

if (isset($_GET['search']) && $_GET['search'] !== '') {
    $query->where(function($q) {
        $q->where('ticket_number', 'like', "%{$_GET['search']}%")
          ->orWhere('title', 'like', "%{$_GET['search']}%");
    });
}
*/

$reports = $query->latest()->paginate(10);
$json = json_encode([
    'success' => true,
    'data' => $reports
]);

echo "USER_ID: " . $user->id . "\n";
echo "COUNT: " . $reports->count() . "\n";
echo "JSON:\n" . $json . "\n";
