<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Autenticación",
 *     description="Operaciones de autenticación de usuarios"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/login",
     *     summary="Inicia sesión de un usuario y devuelve un token de acceso",
     *     tags={"Autenticación"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Inicio de sesión exitoso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="token", type="string", example="1|abcdefg12345"),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/UserResource"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Credenciales incorrectas o error de validación",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Las credenciales proporcionadas son incorrectas."),
     *             @OA\Property(property="errors", type="object"),
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Revocar tokens previos del usuario
        $user->tokens()->delete();

        // Generar nuevo token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user->load('roles.permissions', 'institucion')),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/v1/logout",
     *     summary="Cierra la sesión del usuario actual invalidando su token de acceso",
     *     tags={"Autenticación"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Sesión cerrada exitosamente",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Sesión cerrada exitosamente"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *     )
     * )
     */
    public function logout(Request $request)
    {
        // Invalidar el token actual del usuario
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/v1/me",
     *     summary="Obtiene la información del usuario autenticado",
     *     tags={"Autenticación"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Información del usuario",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/UserResource"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *     )
     * )
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => new UserResource($request->user()->load('roles.permissions', 'institucion')),
        ]);
    }

    /**
     * Verificar la validez del token actual
     */
    public function verifyToken(Request $request)
    {
        try {
            $user = $request->user();

            if (! $user) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Token inválido',
                ], 401);
            }

            $token = $user->currentAccessToken();

            // Verificar si el token expira pronto (menos de 1 hora)
            $shouldRefresh = false;
            if ($token && $token->expires_at) {
                $minutesUntilExpiry = $token->expires_at->diffInMinutes(now());
                $shouldRefresh = $minutesUntilExpiry < 60;
            }

            $response = [
                'valid' => true,
                'user' => new UserResource($user),
                'should_refresh' => $shouldRefresh,
                'expires_at' => $token ? $token->expires_at : null,
            ];

            // Si debe renovarse, crear un nuevo token
            if ($shouldRefresh) {
                $newToken = $user->createToken('auth-token', $token->abilities ?? ['*']);
                $response['new_token'] = $newToken->plainTextToken;

                // Eliminar el token anterior
                $token->delete();
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Error al verificar token: '.$e->getMessage(),
            ], 500);
        }
    }
}
