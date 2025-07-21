<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->boot();

echo "Tenants:\n";
App\Models\Tenant::all()->each(function($t) {
    echo "- {$t->name} ({$t->slug}) - ID: {$t->id}\n";
    echo "  Domains: " . $t->domains->pluck('domain')->join(', ') . "\n";
});

echo "\nTesting tenant access...\n";

// Test accessing tenant context
foreach (App\Models\Tenant::all() as $tenant) {
    if ($tenant->domains->count() > 0) {
        echo "Testing tenant: {$tenant->name}\n";
        tenancy()->initialize($tenant);
        echo "  Current tenant ID: " . tenant('id') . "\n";
        echo "  Current tenant name: " . tenant('name') . "\n";
        tenancy()->end();
    }
}
