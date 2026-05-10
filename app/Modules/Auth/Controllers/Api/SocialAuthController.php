<?php

namespace App\Modules\Auth\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Modules\Auth\Services\TokenService;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function __construct(private readonly TokenService $tokenService)
    {
    }

    /**
     * Redirect to social provider (Socialite flow).
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * Handle social callback (Socialite flow).
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            
            $user = User::updateOrCreate([
                'email' => $socialUser->getEmail(),
            ], [
                'name' => $socialUser->getName(),
                "{$provider}_id" => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'password' => bcrypt(Str::random(24)),
                'email_verified_at' => now(),
            ]);

            $tokens = $this->tokenService->issueTokens($user);

            return response()->json([
                'status' => 'success',
                'user' => $user,
                'tokens' => $tokens,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 401);
        }
    }

    /**
     * Authenticate using a Google ID Token (issued by Mobile SDK).
     */
    public function loginWithGoogle(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
            'user_type' => 'nullable|string|in:customer,driver',
            'platform' => 'nullable|string',
            'app_version' => 'nullable|string',
        ]);

        try {
            $idToken = $request->input('id_token');
            $response = \Illuminate\Support\Facades\Http::get("https://oauth2.googleapis.com/tokeninfo?id_token={$idToken}");
            
            if ($response->failed()) {
                throw new \Exception('Invalid Google ID Token.');
            }

            $payload = $response->json();
            
            return $this->processSocialLogin(
                $payload['email'],
                $payload['name'] ?? 'Google User',
                $payload['sub'],
                'google',
                $payload['picture'] ?? null,
                $request
            );

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Google verification failed: ' . $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Authenticate using a Facebook Access Token (issued by Mobile SDK).
     */
    public function loginWithFacebook(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
            'user_type' => 'nullable|string|in:customer,driver',
            'platform' => 'nullable|string',
            'app_version' => 'nullable|string',
        ]);

        try {
            $accessToken = $request->input('access_token');
            $response = \Illuminate\Support\Facades\Http::get("https://graph.facebook.com/me?fields=id,name,email,picture&access_token={$accessToken}");
            
            if ($response->failed()) {
                throw new \Exception('Invalid Facebook Access Token.');
            }

            $payload = $response->json();
            
            if (empty($payload['email'])) {
                throw new \Exception('Facebook account must have an associated email address.');
            }

            return $this->processSocialLogin(
                $payload['email'],
                $payload['name'] ?? 'Facebook User',
                $payload['id'],
                'facebook',
                $payload['picture']['data']['url'] ?? null,
                $request
            );

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Facebook verification failed: ' . $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Shared logic for storing social user and issuing tokens.
     */
    private function processSocialLogin($email, $name, $providerId, $provider, $avatar, Request $request)
    {
        // Find or create the user
        $user = User::updateOrCreate([
            'email' => $email,
        ], [
            'name' => $name,
            "{$provider}_id" => $providerId,
            'avatar_url' => $avatar, // Standardized column name
            'password' => bcrypt(Str::random(24)),
            'email_verified_at' => now(),
            'user_type' => $request->input('user_type', 'customer'),
            'last_platform' => $request->input('platform'),
            'last_app_version' => $request->input('app_version'),
        ]);

        // Issue tokens
        $tokens = $this->tokenService->issueTokens($user);

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'tokens' => $tokens,
        ]);
    }
}
