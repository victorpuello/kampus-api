<?php

use App\Http\Controllers\Api\V1\AcudienteController;
use App\Http\Controllers\Api\V1\AnioController;
use App\Http\Controllers\Api\V1\AreaController;
use App\Http\Controllers\Api\V1\AsignacionController;
use App\Http\Controllers\Api\V1\AsignaturaController;
use App\Http\Controllers\Api\V1\AulaController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DocenteController;
use App\Http\Controllers\Api\V1\FranjaHorariaController;
use App\Http\Controllers\Api\V1\GradoController;
use App\Http\Controllers\Api\V1\GrupoController;
use App\Http\Controllers\Api\V1\InstitucionController;
use App\Http\Controllers\Api\V1\PeriodoController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\SedeController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Rutas de la API para la versión 1 (v1).
 *
 * Este archivo define todas las rutas de la API para la aplicación Kampus.
 * Las rutas están agrupadas por prefijo 'v1' y se dividen en rutas públicas
 * (como el login) y rutas protegidas que requieren autenticación con Sanctum.
 */
Route::prefix('v1')->group(function () {
    // Rutas públicas para autenticación
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
    Route::post('/login', [AuthController::class, 'login']);

    // Rutas protegidas con autenticación por token
    Route::middleware(['auth:sanctum'])->group(function () {
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
        Route::post('/logout', [AuthController::class, 'logout']);

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
        Route::get('/me', [AuthController::class, 'me']);

        /**
         * @OA\Get(
         *     path="/v1/verify-token",
         *     summary="Verifica la validez del token actual y lo renueva si es necesario",
         *     tags={"Autenticación"},
         *     security={{"sanctum":{}}},
         *
         *     @OA\Response(
         *         response=200,
         *         description="Token válido",
         *
         *         @OA\JsonContent(
         *
         *             @OA\Property(property="valid", type="boolean", example=true),
         *             @OA\Property(property="user", type="object", ref="#/components/schemas/UserResource"),
         *             @OA\Property(property="should_refresh", type="boolean", example=false),
         *             @OA\Property(property="expires_at", type="string", format="date-time", nullable=true),
         *             @OA\Property(property="new_token", type="string", nullable=true),
         *         )
         *     ),
         *
         *     @OA\Response(
         *         response=401,
         *         description="Token inválido",
         *     )
         * )
         */
        Route::get('/verify-token', [AuthController::class, 'verifyToken']);

        // Rutas de usuarios
        /**
         * Rutas para la gestión de usuarios (CRUD).
         */
        Route::apiResource('users', UserController::class);

        // Rutas anidadas de roles bajo usuarios
        Route::prefix('users/{user}')->group(function () {
            Route::get('roles', [RoleController::class, 'index']);
            Route::get('roles/{role}', [RoleController::class, 'show']);
            Route::post('roles', [RoleController::class, 'assignRoles']);
        });

        // Rutas generales de roles
        Route::get('roles', [RoleController::class, 'getAllRoles']);
        Route::get('roles/{role}/permissions', [RoleController::class, 'getRolePermissions']);

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

        // Ruta específica para obtener docentes disponibles para grupos
        Route::get('docentes/disponibles-grupo', [DocenteController::class, 'disponiblesGrupo']);

        // Rutas de instituciones
        /**
         * Rutas para la gestión de instituciones (CRUD).
         */
        Route::apiResource('instituciones', InstitucionController::class)->parameters([
            'instituciones' => 'institucion',
        ]);

        // Ruta específica para obtener sedes de una institución
        Route::get('instituciones/{institucion}/sedes', [InstitucionController::class, 'sedes']);

        // Rutas de grupos
        /**
         * Rutas para la gestión de grupos académicos (CRUD).
         */
        Route::apiResource('grupos', GrupoController::class);
        
        // Ruta para desvincular estudiante de un grupo
        Route::delete('grupos/{grupo}/estudiantes/{estudiante}', [GrupoController::class, 'desvincularEstudiante']);
        
        // Ruta para trasladar estudiante a otro grupo
        Route::put('grupos/{grupo}/estudiantes/{estudiante}/trasladar', [GrupoController::class, 'trasladarEstudiante']);

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

        // Rutas anidadas de franjas horarias bajo instituciones
        Route::prefix('instituciones/{institucion}')->group(function () {
            Route::get('franjas-horarias', [FranjaHorariaController::class, 'indexForInstitucion']);
            Route::post('franjas-horarias', [FranjaHorariaController::class, 'storeForInstitucion']);
            Route::get('franjas-horarias/{franja_horaria}', [FranjaHorariaController::class, 'showForInstitucion']);
            Route::put('franjas-horarias/{franja_horaria}', [FranjaHorariaController::class, 'updateForInstitucion']);
            Route::delete('franjas-horarias/{franja_horaria}', [FranjaHorariaController::class, 'destroyForInstitucion']);
        });

        // Rutas de sedes
        /**
         * Rutas para la gestión de sedes (CRUD).
         */
        Route::apiResource('sedes', SedeController::class);

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
        // Ruta específica para obtener niveles educativos disponibles (debe ir antes del resource)
        Route::get('grados/niveles', [GradoController::class, 'niveles']);

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

        // Rutas de asignaciones
        /**
         * Rutas para la gestión de asignaciones (CRUD).
         */
        Route::apiResource('asignaciones', AsignacionController::class);

        // Rutas especializadas de asignaciones
        Route::get('asignaciones/grupo/{grupoId}', [AsignacionController::class, 'porGrupo']);
        Route::get('asignaciones/docente/{docenteId}', [AsignacionController::class, 'porDocente']);
        Route::get('asignaciones/conflictos', [AsignacionController::class, 'conflictos']);

        // Rutas de periodos
        /**
         * Rutas para la gestión de periodos académicos (CRUD).
         */
        Route::apiResource('periodos', PeriodoController::class);

        // Rutas anidadas de periodos bajo años académicos
        Route::get('anios/{anio}/periodos', [PeriodoController::class, 'getByAnio']);
        Route::post('anios/{anio}/periodos', [PeriodoController::class, 'storeForAnio']);
        Route::get('anios/{anio}/periodos/{periodo}', [PeriodoController::class, 'showForAnio']);
        Route::put('anios/{anio}/periodos/{periodo}', [PeriodoController::class, 'updateForAnio']);
        Route::delete('anios/{anio}/periodos/{periodo}', [PeriodoController::class, 'destroyForAnio']);
    });
});
