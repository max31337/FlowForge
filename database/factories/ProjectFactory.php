<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $dueDate = $this->faker->optional(0.7)->dateTimeBetween($startDate, '+1 year');

        return [
            'name' => $this->faker->catchPhrase(),
            'description' => $this->faker->paragraph(3),
            'status' => $this->faker->randomElement(['planning', 'active', 'on_hold', 'completed', 'cancelled']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'start_date' => $startDate,
            'due_date' => $dueDate,
            'budget' => $this->faker->optional(0.6)->randomFloat(2, 1000, 100000),
            'is_active' => $this->faker->boolean(90),
        ];
    }

    /**
     * Indicate that the project is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the project is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'is_active' => false,
            'due_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Indicate that the project is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
        ]);
    }

    /**
     * Indicate that the project has a large budget.
     */
    public function largeBudget(): static
    {
        return $this->state(fn (array $attributes) => [
            'budget' => $this->faker->randomFloat(2, 50000, 500000),
        ]);
    }
}
