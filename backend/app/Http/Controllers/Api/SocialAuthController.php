<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Handle Google OAuth Callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            $user = $this->findOrCreateUser($googleUser, 'google');
            
            $token = $user->createToken('auth_token')->plainTextToken;

            // Redirect to frontend with token
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            return redirect()->away("{$frontendUrl}/auth/callback?token={$token}");

        } catch (Exception $e) {
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            return redirect()->away("{$frontendUrl}/auth/error?message=" . urlencode($e->getMessage()));
        }
    }

    /**
     * Redirect to Facebook OAuth
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    /**
     * Handle Facebook OAuth Callback
     */
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();
            
            $user = $this->findOrCreateUser($facebookUser, 'facebook');
            
            $token = $user->createToken('auth_token')->plainTextToken;

            // Redirect to frontend with token
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            return redirect()->away("{$frontendUrl}/auth/callback?token={$token}");

        } catch (Exception $e) {
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            return redirect()->away("{$frontendUrl}/auth/error?message=" . urlencode($e->getMessage()));
        }
    }

    /**
     * Find or create user from social provider
     */
    private function findOrCreateUser($socialUser, $provider)
    {
        // Check if user exists with this provider
        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($user) {
            return $user;
        }

        // Check if user exists with this email
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Update user with social provider info
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]);
            return $user;
        }

        // Create new user
        $user = User::create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar(),
            'email_verified_at' => now(),
        ]);

        // Create wallet for new user
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
            'points' => 0,
        ]);

        return $user;
    }
}
