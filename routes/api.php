<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\DocenteController;
use App\Http\Controllers\Api\V1\InstitucionController;
use App\Http\Controllers\Api\V1\AnioController;
use App\Http\Controllers\Api\V1\AcudienteController;
use App\Http\Controllers\Api\V1\GradoController;
use App\Http\Controllers\Api\V1\AreaController;
use App\Http\Controllers\Api\V1\AsignaturaController;
use App\Http\Controllers\Api\V1\GrupoController;
use App\Http\Controllers\Api\V1\AulaController;
use App\Http\Controllers\Api\V1\FranjaHorariaController;
use App\Http\Controllers\Api\V1\AsignacionController;
use Illuminate\Support\Facades\Route;

/**
 * Rutas de la API para la versión 1 (v1).
 *
 * Este archivo define todas las rutas de la API para la aplicación Kampus.
 * Las rutas están agrupadas por prefijo 'v1' y se dividen en rutas públicas
 * (como el login) y rutas protegidas que requieren autenticación con Sanctum.
 */
Route::prefix('v1')->group(function () {
    // Rutas públicas
    /**
     * @OA\Post(
     *     path="/v1/login",
     *     summary="Inicia sesión de un usuario y devuelve un token de acceso",
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
     *             @OA\Property(property="token", type="string", example="1|abcdefg12345"),
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
    Route::post('/login', [AuthController::class, 'login']);

    // Rutas protegidas
    /**
     * Grupo de rutas que requieren autenticación con Laravel Sanctum.
     */
    Route::middleware('auth:sanctum')->group(function () {
        /**
         * @OA\Post(
         *     path="/v1/logout",
         *     summary="Cierra la sesión del usuario actual invalidando su token de acceso",
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
        Route::post('/logout', [AuthController::class, 'logout']);
        
        // Rutas de usuarios
        /**
         * Rutas para la gestión de usuarios (CRUD).
         */
        Route::apiResource('users', UserController::class);

        // Rutas de estudiantes
        /**
         * Rutas para la gestión de estudiantes (CRUD).
         */
        Route::apiResource('estudiantes', StudentController::class);

        // Rutas de docentes
        /**
         * Rutas para la gestión de docentes (CRUD).
         */
        Route::apiResource('docentes', DocenteController::class);

        // Rutas de instituciones
        /**
         * Rutas para la gestión de instituciones (CRUD).
         */
        Route::apiResource('instituciones', InstitucionController::class);

        // Rutas de años
        /**
         * Rutas para la gestión de años académicos (CRUD).
         */
        Route::apiResource('anios', AnioController::class);

        // Rutas de acudientes
        /**
         * Rutas para la gestión de acudientes (CRUD).
         */
        Route::apiResource('acudientes', AcudienteController::class);

        // Rutas de grados
        /**
         * Rutas para la gestión de grados académicos (CRUD).
         */
        Route::apiResource('grados', GradoController::class);

        // Rutas de áreas
        /**
         * Rutas para la gestión de áreas académicas (CRUD).
         */
        Route::apiResource('areas', AreaController::class);

        // Rutas de asignaturas
        /**
         * Rutas para la gestión de asignaturas (CRUD).
         */
        Route::apiResource('asignaturas', AsignaturaController::class);

        // Rutas de grupos
        /**
         * Rutas para la gestión de grupos académicos (CRUD).
         */
        Route::apiResource('grupos', GrupoController::class);

        // Rutas de aulas
        /**
         * Rutas para la gestión de aulas (CRUD).
         */
        Route::apiResource('aulas', AulaController::class);

        // Rutas de franjas horarias
        /**
         * Rutas para la gestión de franjas horarias (CRUD).
         */
        Route::apiResource('franjas-horarias', FranjaHorariaController::class);

        // Rutas de asignaciones
        /**
         * Rutas para la gestión de asignaciones (CRUD).
         */
        Route::apiResource('asignaciones', AsignacionController::class);
    });
}); 