<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the OAuth Provider.
     */
    public function redirectToProvider($provider)
    {
        $validProviders = ['google', 'github', 'microsoft'];
        
        if (!in_array($provider, $validProviders)) {
            return redirect()->route('login')->with('error', 'Invalid OAuth provider.');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from OAuth Provider.
     */
    public function handleProviderCallback($provider)
    {
        $validProviders = ['google', 'github', 'microsoft'];
        
        if (!in_array($provider, $validProviders)) {
            return redirect()->route('login')->with('error', 'Invalid OAuth provider.');
        }

        try {
            // Add debugging
            \Log::info('OAuth callback started', ['provider' => $provider]);
            
            $socialUser = Socialite::driver($provider)->user();
            
            // Add debugging
            \Log::info('OAuth user retrieved', [
                'provider' => $provider,
                'social_user_id' => $socialUser->getId(),
                'email' => $socialUser->getEmail(),
                'name' => $socialUser->getName()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('OAuth authentication failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')->with('error', 'OAuth authentication failed: ' . $e->getMessage());
        }

        // Check if user already exists with this email
        $existingUser = User::where('email', $socialUser->getEmail())->first();

        if ($existingUser) {
            \Log::info('Existing user found', ['user_id' => $existingUser->id]);
            
            // Check if we're in tenant context and user belongs to current tenant
            if (tenancy()->initialized) {
                $currentTenantId = tenant('id');
                
                if ($existingUser->tenant_id !== $currentTenantId) {
                    \Log::warning('User attempted to login from wrong tenant', [
                        'user_id' => $existingUser->id,
                        'user_tenant_id' => $existingUser->tenant_id,
                        'current_tenant_id' => $currentTenantId
                    ]);
                    
                    return redirect()->route('login')
                        ->with('error', 'You do not have access to this organization.');
                }
            }
            
            // Update OAuth info if user exists but doesn't have provider info
            if (!$existingUser->provider) {
                $existingUser->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }
            
            Auth::login($existingUser);
            \Log::info('User logged in', ['user_id' => $existingUser->id]);
            return redirect()->intended('dashboard');
        }

        // Create new user
        try {
            \Log::info('Creating new user');
            
            $userData = [
                'name' => $socialUser->getName() ?: $socialUser->getNickname(),
                'email' => $socialUser->getEmail(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'password' => Hash::make(Str::random(24)), // Random password since they use OAuth
                'email_verified_at' => now(), // Auto-verify OAuth users
            ];
            
            // If we're in tenant context, assign tenant_id
            if (tenancy()->initialized) {
                $userData['tenant_id'] = tenant('id');
                \Log::info('Assigning user to tenant', ['tenant_id' => tenant('id')]);
            }
            
            $user = User::create($userData);

            \Log::info('New user created', ['user_id' => $user->id]);

            Auth::login($user);
            \Log::info('New user logged in', ['user_id' => $user->id]);
            return redirect()->intended('dashboard');
            
        } catch (\Exception $e) {
            \Log::error('User creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')->with('error', 'Failed to create user account: ' . $e->getMessage());
        }
    }
}
