<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company();
        $slug = Str::slug($name) . '-' . $this->faker->unique()->numberBetween(100, 999);

        return [
            'name' => $name,
            'slug' => $slug,
            'email' => $this->faker->companyEmail(),
            'plan' => $this->faker->randomElement(['free', 'pro', 'enterprise']),
            'active' => $this->faker->boolean(85), // 85% chance of being active
            'trial_ends_at' => $this->faker->optional(0.3)->dateTimeBetween('now', '+30 days'),
            'data' => [
                'industry' => $this->faker->randomElement([
                    'Technology', 'Healthcare', 'Finance', 'Education', 
                    'Retail', 'Manufacturing', 'Real Estate', 'Marketing'
                ]),
                'size' => $this->faker->randomElement(['1-10', '11-50', '51-200', '201-500', '500+']),
                'timezone' => $this->faker->timezone(),
            ],
        ];
    }

    /**
     * Indicate that the tenant should be active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => true,
        ]);
    }

    /**
     * Indicate that the tenant should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Indicate that the tenant is on a specific plan.
     */
    public function plan(string $plan): static
    {
        return $this->state(fn (array $attributes) => [
            'plan' => $plan,
        ]);
    }

    /**
     * Indicate that the tenant is on trial.
     */
    public function onTrial(): static
    {
        return $this->state(fn (array $attributes) => [
            'trial_ends_at' => now()->addDays(30),
        ]);
    }

    /**
     * Create a tenant with a specific industry.
     */
    public function industry(string $industry): static
    {
        return $this->state(function (array $attributes) use ($industry) {
            $data = $attributes['data'] ?? [];
            $data['industry'] = $industry;
            return ['data' => $data];
        });
    }

    /**
     * Create a tenant with a specific company size.
     */
    public function size(string $size): static
    {
        return $this->state(function (array $attributes) use ($size) {
            $data = $attributes['data'] ?? [];
            $data['size'] = $size;
            return ['data' => $data];
        });
    }

    /**
     * Create a startup-style tenant.
     */
    public function startup(): static
    {
        $startupNames = [
            'InnovateLab', 'StartupHub', 'TechBoost', 'LaunchPad Pro',
            'Venture Co', 'Growth Engine', 'Idea Factory', 'Scale Solutions'
        ];

        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->randomElement($startupNames) . ' ' . $this->faker->word(),
            'plan' => 'free',
            'trial_ends_at' => now()->addDays(30),
            'data' => [
                'industry' => 'Technology',
                'size' => '1-10',
                'timezone' => $this->faker->timezone(),
                'founded_year' => date('Y') - rand(0, 3),
            ],
        ]);
    }

    /**
     * Create an enterprise-style tenant.
     */
    public function enterprise(): static
    {
        $enterpriseNames = [
            'Global Corp', 'International Ltd', 'Enterprise Solutions',
            'Worldwide Inc', 'Major Industries', 'Corporate Group'
        ];

        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->randomElement($enterpriseNames) . ' ' . $this->faker->companySuffix(),
            'plan' => 'enterprise',
            'active' => true,
            'trial_ends_at' => null,
            'data' => [
                'industry' => $this->faker->randomElement(['Finance', 'Manufacturing', 'Healthcare']),
                'size' => '500+',
                'timezone' => $this->faker->timezone(),
                'founded_year' => date('Y') - rand(10, 50),
            ],
        ]);
    }

    /**
     * Create a tenant with specific domain.
     */
    public function withDomain(string $domain): static
    {
        return $this->state(fn (array $attributes) => [
            'slug' => str_replace('.localhost', '', $domain),
        ]);
    }
}
