<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->boot();

use App\Models\Task;
use App\Models\Project;

echo "Testing enum values...\n";

// Test valid project statuses
$validProjectStatuses = ['planning', 'active', 'on_hold', 'completed', 'cancelled'];
echo "Valid project statuses: " . implode(', ', $validProjectStatuses) . "\n";

// Test valid task statuses
$validTaskStatuses = ['pending', 'in_progress', 'review', 'completed', 'cancelled'];
echo "Valid task statuses: " . implode(', ', $validTaskStatuses) . "\n";

// Test TaskFactory status generation
echo "\nTesting TaskFactory random status generation:\n";
for ($i = 0; $i < 10; $i++) {
    $factory = new \Database\Factories\TaskFactory();
    $data = $factory->definition();
    echo "Generated task status: " . $data['status'] . "\n";
    
    if (!in_array($data['status'], $validTaskStatuses)) {
        echo "ERROR: Invalid task status generated: " . $data['status'] . "\n";
        exit(1);
    }
}

echo "\nAll enum values are valid! ✅\n";
