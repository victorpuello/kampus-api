<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     *     summary="Inicia sesión de un usuario usando autenticación basada en sesiones",
     *     tags={"Autenticación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inicio de sesión exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Inicio de sesión exitoso"),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/UserResource"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Credenciales incorrectas o error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Las credenciales proporcionadas son incorrectas."),
     *             @OA\Property(property="errors", type="object"),
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'user' => new UserResource($user->load('roles.permissions', 'institucion')),
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Las credenciales proporcionadas son incorrectas.'],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/v1/logout",
     *     summary="Cierra la sesión del usuario actual",
     *     tags={"Autenticación"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sesión cerrada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sesión cerrada exitosamente"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *     )
     * )
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente'
        ]);
    }
}
 