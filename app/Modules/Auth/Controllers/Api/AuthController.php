<?php

namespace App\Modules\Auth\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Modules\Auth\Requests\LoginRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function login(LoginRequest $request): JsonResponse
    {
        $request->ensureIsNotRateLimited();

        $staff = Staff::where('email', $request->email)->first();

        if (! $staff || ! Hash::check($request->password, $staff->password)) {
            RateLimiter::hit($request->throttleKey());

            return $this->unauthorized('Credenciales incorrectas');
        }

        RateLimiter::clear($request->throttleKey());

        // Borrar tokens previos si se desea, pero por ahora no es requerido explícitamente excepto logout-all.
        $token = $staff->createToken('auth_token')->plainTextToken;

        $data = [
            'token' => $token,
            'role' => $staff->role,
        ];

        if ($staff->isDoctor()) {
            $data['doctor_id'] = $staff->doctor_id;
        }

        return $this->ok('Inicio de sesión exitoso', $data);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var Staff $staff */
        $staff = $request->user();

        // Revocar el token que se usó para autenticar esta petición
        $staff->currentAccessToken()->delete();

        return $this->ok('Sesión cerrada correctamente');
    }

    public function logoutAll(Request $request): JsonResponse
    {
        /** @var Staff $staff */
        $staff = $request->user();

        // Revocar todos los tokens del usuario
        $staff->tokens()->delete();

        return $this->ok('Todas las sesiones han sido cerradas');
    }

    public function me(Request $request): JsonResponse
    {
        /** @var Staff $staff */
        $staff = $request->user();

        return $this->ok('Datos del perfil obtenidos', $staff);
    }
}
