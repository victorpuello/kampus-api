<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreDocenteRequest",
 *     title="Solicitud para Crear Docente",
 *     required={
 *         "nombre", "apellido", "email", "username", "password",
 *         "institucion_id", "estado"
 *     },
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del docente"),
 *     @OA\Property(property="apellido", type="string", maxLength=255, description="Apellido del docente"),
 *     @OA\Property(property="email", type="string", format="email", maxLength=255, description="Correo electrónico único del docente"),
 *     @OA\Property(property="username", type="string", maxLength=255, description="Nombre de usuario único para el docente"),
 *     @OA\Property(property="password", type="string", minLength=8, description="Contraseña para el usuario del docente"),
 *     @OA\Property(property="institucion_id", type="integer", description="ID de la institución a la que pertenece el docente"),
 *     @OA\Property(property="estado", type="string", enum={"activo", "inactivo"}, description="Estado del docente"),
 * )
 */
class StoreDocenteRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'institucion_id' => 'required|integer|exists:instituciones,id',
            'estado' => 'required|string|in:activo,inactivo',
        ];
    }
}
