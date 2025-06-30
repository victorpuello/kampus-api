<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateStudentRequest",
 *     title="Solicitud para Actualizar Estudiante",
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
class UpdateStudentRequest extends FormRequest
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
            'nombre' => 'sometimes|string|max:255',
            'apellido' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $this->student->user_id,
            'username' => 'sometimes|string|max:255|unique:users,username,' . $this->student->user_id,
            'password' => 'sometimes|string|min:8',
            'codigo_estudiantil' => 'sometimes|string|max:50|unique:estudiantes,codigo_estudiantil,' . $this->student->id,
            'institucion_id' => 'sometimes|integer|exists:instituciones,id',
            'estado' => 'sometimes|string|in:activo,inactivo',
        ];
    }
}
