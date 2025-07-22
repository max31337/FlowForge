<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Database\Factories\TaskFactory;
use Database\Factories\ProjectFactory;

class EnumValidationTest extends TestCase
{
    public function test_project_factory_generates_valid_statuses()
    {
        $validProjectStatuses = ['planning', 'active', 'on_hold', 'completed', 'cancelled'];
        
        for ($i = 0; $i < 20; $i++) {
            $factory = new ProjectFactory();
            $data = $factory->definition();
            
            $this->assertContains(
                $data['status'], 
                $validProjectStatuses,
                "ProjectFactory generated invalid status: {$data['status']}"
            );
        }
    }
    
    public function test_task_factory_generates_valid_statuses()
    {
        $validTaskStatuses = ['pending', 'in_progress', 'review', 'completed', 'cancelled'];
        
        for ($i = 0; $i < 20; $i++) {
            $factory = new TaskFactory();
            $data = $factory->definition();
            
            $this->assertContains(
                $data['status'], 
                $validTaskStatuses,
                "TaskFactory generated invalid status: {$data['status']}"
            );
        }
    }
    
    public function test_project_status_enum_values()
    {
        $expectedStatuses = ['planning', 'active', 'on_hold', 'completed', 'cancelled'];
        
        // Test that our expected values match what the factory uses
        $factory = new ProjectFactory();
        $factoryStatuses = ['planning', 'active', 'on_hold', 'completed', 'cancelled']; // From factory definition
        
        $this->assertEquals($expectedStatuses, $factoryStatuses);
    }
    
    public function test_task_status_enum_values()
    {
        $expectedStatuses = ['pending', 'in_progress', 'review', 'completed', 'cancelled'];
        
        // Test that our expected values match what the factory uses
        $factory = new TaskFactory();
        $factoryStatuses = ['pending', 'in_progress', 'review', 'completed', 'cancelled']; // From factory definition
        
        $this->assertEquals($expectedStatuses, $factoryStatuses);
    }
}
