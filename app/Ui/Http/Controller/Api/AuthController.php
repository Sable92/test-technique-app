<?php

declare(strict_types=1);

namespace App\Ui\Http\Controller\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Infrastructure\Model\Administrator;

class AuthController extends Controller
{
    /**
     * Connexion administrateur
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        /** @var array{email: string, password: string} $credentials */
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $email = $credentials['email'];
        $password = $credentials['password'];

        $admin = Administrator::where('email', $email)->first();

        if (!$admin instanceof Administrator) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!Hash::check($password, $admin->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $admin->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'admin' => [
                'id' => $admin->id,
                'email' => $admin->email,
            ]
        ]);
    }

    /**
     * Déconnexion administrateur
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user instanceof Administrator) {
            return response()->json([
                'message' => 'User not authenticated'
            ], 401);
        }

        $currentToken = $user->currentAccessToken();
        $currentToken->delete();

        return response()->json(['message' => 'Déconnecté avec succès']);
    }
}
