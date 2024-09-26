<?php


namespace App\Http\Controllers;

use App\Services\AuthentificationServiceInterface;
use Illuminate\Http\Request;

class AuthController extends Controller {
    protected $authService;

    public function __construct(AuthentificationServiceInterface $authService) {
        $this->authService = $authService;
        $this->authService->setAuthMode(config('app.auth_mode'));
    }

    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');
    $authResponse = $this->authService->authenticate($credentials);

    if (is_array($authResponse) && isset($authResponse['success']) && $authResponse['success']) {
        return response()->json([
            'access_token' => $authResponse['token'],
            'token_type' => 'Bearer',
            'user' => $authResponse['user'],
        ]);
    }

    return response()->json(['error' => $authResponse['message'] ?? 'Unauthorized'], 401);
}

    public function logout() {
        $this->authService->logout();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    
}
