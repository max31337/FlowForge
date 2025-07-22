<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = $this->faker->dateTimeBetween('-3 months', 'now');
        $dueDate = $this->faker->optional(0.8)->dateTimeBetween('now', '+2 months');

        $taskTypes = [
            'Research and analyze',
            'Design and prototype',
            'Develop and implement',
            'Test and validate',
            'Review and approve',
            'Deploy and monitor',
            'Document and train',
            'Plan and organize',
            'Create and build',
            'Update and maintain'
        ];

        $taskSubjects = [
            'user interface components',
            'database schema',
            'API endpoints',
            'authentication system',
            'payment integration',
            'email notifications',
            'user dashboard',
            'reporting features',
            'mobile responsiveness',
            'performance optimization',
            'security measures',
            'backup procedures',
            'user documentation',
            'admin panel',
            'search functionality'
        ];

        return [
            'title' => $this->faker->randomElement($taskTypes) . ' ' . $this->faker->randomElement($taskSubjects),
            'description' => $this->faker->paragraph(2),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'review', 'completed', 'cancelled']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'due_date' => $dueDate,
            'estimated_hours' => $this->faker->optional(0.7)->numberBetween(1, 40),
            'actual_hours' => $this->faker->optional(0.4)->numberBetween(1, 50),
            'created_at' => $createdAt,
            'updated_at' => $this->faker->dateTimeBetween($createdAt, 'now'),
            // Note: created_by must be provided when creating tasks
        ];
    }

    /**
     * Indicate that the task is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'actual_hours' => $this->faker->numberBetween(1, 20),
        ]);
    }

    /**
     * Indicate that the task is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'actual_hours' => $this->faker->optional(0.6)->numberBetween(1, 15),
        ]);
    }

    /**
     * Indicate that the task is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
        ]);
    }

    /**
     * Indicate that the task is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
            'status' => $this->faker->randomElement(['pending', 'in_progress']),
        ]);
    }

    /**
     * Indicate that the task has a long estimation.
     */
    public function longTask(): static
    {
        return $this->state(fn (array $attributes) => [
            'estimated_hours' => $this->faker->numberBetween(20, 80),
        ]);
    }
}
