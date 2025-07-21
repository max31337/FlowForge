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
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'OAuth authentication failed. Please try again.');
        }

        // Check if user already exists with this email
        $existingUser = User::where('email', $socialUser->getEmail())->first();

        if ($existingUser) {
            // Update OAuth info if user exists but doesn't have provider info
            if (!$existingUser->provider) {
                $existingUser->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }
            
            Auth::login($existingUser);
            return redirect()->intended('dashboard');
        }

        // Create new user
        $user = User::create([
            'name' => $socialUser->getName() ?: $socialUser->getNickname(),
            'email' => $socialUser->getEmail(),
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar(),
            'password' => Hash::make(Str::random(24)), // Random password since they use OAuth
            'email_verified_at' => now(), // Auto-verify OAuth users
        ]);

        Auth::login($user);
        return redirect()->intended('dashboard');
    }
}
