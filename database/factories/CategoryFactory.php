<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            ['name' => 'Development', 'description' => 'Software development and programming tasks', 'color' => '#3B82F6'],
            ['name' => 'Design', 'description' => 'UI/UX design and creative tasks', 'color' => '#8B5CF6'],
            ['name' => 'Marketing', 'description' => 'Marketing campaigns and promotional activities', 'color' => '#EF4444'],
            ['name' => 'Sales', 'description' => 'Sales activities and customer acquisition', 'color' => '#10B981'],
            ['name' => 'Support', 'description' => 'Customer support and help desk tasks', 'color' => '#F59E0B'],
            ['name' => 'Research', 'description' => 'Research and analysis activities', 'color' => '#06B6D4'],
            ['name' => 'Operations', 'description' => 'Operational and administrative tasks', 'color' => '#84CC16'],
            ['name' => 'Finance', 'description' => 'Financial planning and accounting tasks', 'color' => '#F97316'],
            ['name' => 'HR', 'description' => 'Human resources and recruitment', 'color' => '#EC4899'],
            ['name' => 'Legal', 'description' => 'Legal compliance and documentation', 'color' => '#6B7280'],
            ['name' => 'Quality Assurance', 'description' => 'Testing and quality control', 'color' => '#DC2626'],
            ['name' => 'Documentation', 'description' => 'Documentation and knowledge management', 'color' => '#7C3AED'],
        ];

        $category = $this->faker->randomElement($categories);

        return [
            'name' => $category['name'],
            'description' => $category['description'],
            'color' => $category['color'],
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Indicate that the category is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a category with a specific color.
     */
    public function withColor(string $color): static
    {
        return $this->state(fn (array $attributes) => [
            'color' => $color,
        ]);
    }
}
