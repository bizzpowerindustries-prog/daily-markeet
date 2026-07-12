<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Exception\Auth\InvalidToken;
use App\Models\User;

class FirebaseAuthMiddleware
{
    protected Auth $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - No token provided'
            ], 401);
        }

        try {
            // Verify Firebase ID Token
            $verifiedToken = $this->auth->verifyIdToken($token);
            $firebaseUid = $verifiedToken->claims()->get('sub');

            // Find or create user
            $user = User::where('firebase_uid', $firebaseUid)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Attach user to request
            $request->merge(['user_id' => $user->id]);
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

        } catch (InvalidToken $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Firebase token: ' . $e->getMessage()
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage()
            ], 401);
        }

        return $next($request);
    }
}
