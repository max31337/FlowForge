<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'avatar' => null,
            'provider' => null,
            'provider_id' => null,
            'tenant_id' => null, // default personal unless overridden
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user belongs to a specific tenant.
     */
    public function forTenant(string $tenantId): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenantId,
        ]);
    }

    /**
     * Indicate that the user has a specific role.
     */
    public function withRole(string $roleId): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => $roleId,
        ]);
    }

    /**
     * Indicate that the user is a central admin.
     */
    public function centralAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => null,
            'email' => 'admin@' . fake()->domainName(),
            'name' => fake()->name() . ' (Admin)',
        ]);
    }

    /**
     * Personal (individual) user with no tenant context.
     */
    public function personal(): static
    {
        return $this->state(fn(array $attributes) => [
            'tenant_id' => null,
        ]);
    }

    /**
     * Create a user with OAuth provider information.
     */
    public function withOAuth(string $provider = 'google'): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => $provider,
            'provider_id' => (string) fake()->numberBetween(100000000, 999999999),
            'avatar' => fake()->imageUrl(200, 200, 'people'),
        ]);
    }

    /**
     * Create a user with a specific password.
     */
    public function withPassword(string $password): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => Hash::make($password),
        ]);
    }

    /**
     * Create a user for a specific role type.
     */
    public function asOwner(string $tenantId): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenantId,
            'name' => fake()->name() . ' (Owner)',
            'email' => 'owner@' . fake()->domainWord() . '.com',
        ]);
    }

    /**
     * Create an admin user.
     */
    public function asAdmin(string $tenantId): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenantId,
            'name' => fake()->name() . ' (Admin)',
            'email' => 'admin@' . fake()->domainWord() . '.com',
        ]);
    }

    /**
     * Create a manager user.
     */
    public function asManager(string $tenantId): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenantId,
            'name' => fake()->name() . ' (Manager)',
            'email' => 'manager@' . fake()->domainWord() . '.com',
        ]);
    }

    /**
     * Create a regular user.
     */
    public function asUser(string $tenantId): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenantId,
            'name' => fake()->name(),
            'email' => fake()->firstName() . '@' . fake()->domainWord() . '.com',
        ]);
    }

    /**
     * Create a user with professional-sounding details.
     */
    public function professional(): static
    {
        $departments = ['Engineering', 'Marketing', 'Sales', 'Support', 'Design', 'Operations'];
        $titles = ['Manager', 'Lead', 'Senior', 'Junior', 'Principal', 'Director'];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->firstName() . ' ' . fake()->lastName() . ', ' . 
                     fake()->randomElement($titles) . ' ' . 
                     fake()->randomElement($departments),
        ]);
    }

    /**
     * Create multiple users with different roles for testing.
     */
    public function teamSet(string $tenantId, string $domain): array
    {
        return [
            'owner' => $this->create([
                'name' => 'John Owner',
                'email' => "owner@{$domain}",
                'tenant_id' => $tenantId,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]),
            'admin' => $this->create([
                'name' => 'Sarah Admin',
                'email' => "admin@{$domain}",
                'tenant_id' => $tenantId,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]),
            'manager' => $this->create([
                'name' => 'Mike Manager',
                'email' => "manager@{$domain}",
                'tenant_id' => $tenantId,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]),
            'user' => $this->create([
                'name' => 'Emma User',
                'email' => "user@{$domain}",
                'tenant_id' => $tenantId,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]),
        ];
    }
}
