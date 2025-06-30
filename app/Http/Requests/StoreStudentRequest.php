<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreStudentRequest",
 *     title="Solicitud para Crear Estudiante",
 *     required={
 *         "nombre", "apellido", "email", "username", "password",
 *         "codigo_estudiantil", "institucion_id", "estado"
 *     },
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del estudiante"),
 *     @OA\Property(property="apellido", type="string", maxLength=255, description="Apellido del estudiante"),
 *     @OA\Property(property="email", type="string", format="email", maxLength=255, description="Correo electrónico único del estudiante"),
 *     @OA\Property(property="username", type="string", maxLength=255, description="Nombre de usuario único para el estudiante"),
 *     @OA\Property(property="password", type="string", minLength=8, description="Contraseña para el usuario del estudiante"),
 *     @OA\Property(property="codigo_estudiantil", type="string", maxLength=50, description="Código estudiantil único"),
 *     @OA\Property(property="institucion_id", type="integer", description="ID de la institución a la que pertenece el estudiante"),
 *     @OA\Property(property="estado", type="string", enum={"activo", "inactivo"}, description="Estado del estudiante"),
 * )
 */
class StoreStudentRequest extends FormRequest
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
            'codigo_estudiantil' => 'required|string|max:50|unique:estudiantes',
            'institucion_id' => 'required|integer|exists:instituciones,id',
            'estado' => 'required|string|in:activo,inactivo',
        ];
    }
}
