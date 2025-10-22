<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Handle authentication via Sanctum tokens.
 */
class AuthController extends Controller
{
    /**
     * Authenticate the user and issue a Sanctum token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Credenciais inválidas.'], 401);
        }

        $user = $request->user()->load('roles', 'permissions');
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * Invalidate the current Sanctum token.
     */
    public function logout(): JsonResponse
    {
        $user = request()->user();
        $user?->currentAccessToken()?->delete();

        return response()->json(['message' => 'Sessão encerrada com sucesso.']);
    }

    /**
     * Return information about the authenticated user.
     */
    public function me(): JsonResponse
    {
        return response()->json(request()->user()->load('roles', 'permissions'));
    }
}
