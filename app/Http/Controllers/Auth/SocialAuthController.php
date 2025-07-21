<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to the OAuth provider.
     */
    public function redirect(string $provider)
    {
        $allowedProviders = ['google', 'github', 'microsoft'];
        
        if (!in_array($provider, $allowedProviders)) {
            return redirect()->route('login')->with('error', 'Provider not supported.');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle OAuth callback.
     */
    public function callback(string $provider)
    {
        try {
            $allowedProviders = ['google', 'github', 'microsoft'];
            
            if (!in_array($provider, $allowedProviders)) {
                return redirect()->route('login')->with('error', 'Provider not supported.');
            }

            $socialUser = Socialite::driver($provider)->user();
            
            // Find existing user by provider
            $user = User::where('provider', $provider)
                       ->where('provider_id', $socialUser->getId())
                       ->first();

            if ($user) {
                // Update user info if needed
                $user->update([
                    'name' => $socialUser->getName(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            } else {
                // Check if user exists with same email
                $existingUser = User::where('email', $socialUser->getEmail())->first();
                
                if ($existingUser) {
                    // Link OAuth account to existing user
                    $existingUser->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'avatar' => $socialUser->getAvatar(),
                    ]);
                    $user = $existingUser;
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $socialUser->getName(),
                        'email' => $socialUser->getEmail(),
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'avatar' => $socialUser->getAvatar(),
                        'email_verified_at' => now(),
                        'password' => Hash::make(Str::random(24)), // Random password for OAuth users
                    ]);
                }
            }

            Auth::login($user, true);

            return redirect()->intended('/dashboard');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Authentication failed. Please try again.');
        }
    }
}
