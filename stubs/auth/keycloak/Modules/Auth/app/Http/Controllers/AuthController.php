<?php

namespace Modules\Auth\app\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\app\Http\Requests\TokenRequest;
use Modules\Auth\app\Http\Resources\UserResource;
use Modules\Auth\app\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(private AuthService $auth) {}

    /**
     * Validate a Keycloak access token sent by the SPA.
     * The SPA handles the Keycloak login redirect and PKCE flow,
     * then sends the resulting access token here for verification.
     */
    public function login(TokenRequest $request): JsonResponse
    {
        $result = $this->auth->validateToken($request->input('access_token'));

        if (! $result) {
            return response()->json(['message' => 'Invalid or expired Keycloak token.'], 401);
        }

        return response()->json([
            'data'    => new UserResource($result['user']),
            'token'   => $result['token'],
            'type'    => 'Bearer',
            'message' => 'Login successful.',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout($request->user());

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => new UserResource($request->user())]);
    }
}
