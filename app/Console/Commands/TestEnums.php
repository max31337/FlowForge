<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Factories\TaskFactory;
use Database\Factories\ProjectFactory;

class TestEnums extends Command
{
    protected $signature = 'test:enums';
    protected $description = 'Test that enum values are working correctly';

    public function handle()
    {
        $this->info('Testing enum values...');

        // Test valid project statuses
        $validProjectStatuses = ['planning', 'active', 'on_hold', 'completed', 'cancelled'];
        $this->info('Valid project statuses: ' . implode(', ', $validProjectStatuses));

        // Test valid task statuses
        $validTaskStatuses = ['pending', 'in_progress', 'review', 'completed', 'cancelled'];
        $this->info('Valid task statuses: ' . implode(', ', $validTaskStatuses));

        // Test TaskFactory status generation
        $this->info('Testing TaskFactory random status generation:');
        for ($i = 0; $i < 10; $i++) {
            $factory = new TaskFactory();
            $data = $factory->definition();
            $this->line("Generated task status: " . $data['status']);
            
            if (!in_array($data['status'], $validTaskStatuses)) {
                $this->error("ERROR: Invalid task status generated: " . $data['status']);
                return 1;
            }
        }

        // Test ProjectFactory status generation
        $this->info('Testing ProjectFactory random status generation:');
        for ($i = 0; $i < 10; $i++) {
            $factory = new ProjectFactory();
            $data = $factory->definition();
            $this->line("Generated project status: " . $data['status']);
            
            if (!in_array($data['status'], $validProjectStatuses)) {
                $this->error("ERROR: Invalid project status generated: " . $data['status']);
                return 1;
            }
        }

        $this->success('All enum values are valid! ✅');
        return 0;
    }
}
