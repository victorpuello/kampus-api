<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateStudentRequest",
 *     title="Solicitud para Actualizar Estudiante",
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del estudiante"),
 *     @OA\Property(property="apellido", type="string", maxLength=255, description="Apellido del estudiante"),
 *     @OA\Property(property="tipo_documento", type="string", enum={"CC", "TI", "CE"}, description="Tipo de documento del estudiante"),
 *     @OA\Property(property="numero_documento", type="string", maxLength=20, description="Número de documento del estudiante"),
 *     @OA\Property(property="email", type="string", format="email", maxLength=255, description="Correo electrónico único del estudiante"),
 *     @OA\Property(property="username", type="string", maxLength=255, description="Nombre de usuario único para el estudiante"),
 *     @OA\Property(property="password", type="string", minLength=8, description="Contraseña para el usuario del estudiante"),
 *     @OA\Property(property="codigo_estudiantil", type="string", maxLength=50, description="Código estudiantil único"),
 *     @OA\Property(property="fecha_nacimiento", type="string", format="date", description="Fecha de nacimiento del estudiante"),
 *     @OA\Property(property="genero", type="string", enum={"M", "F", "O"}, description="Género del estudiante"),
 *     @OA\Property(property="direccion", type="string", maxLength=255, description="Dirección del estudiante"),
 *     @OA\Property(property="telefono", type="string", maxLength=20, description="Teléfono del estudiante"),
 *     @OA\Property(property="institucion_id", type="integer", description="ID de la institución a la que pertenece el estudiante"),
 *     @OA\Property(property="grupo_id", type="integer", description="ID del grupo al que pertenece el estudiante"),
 *     @OA\Property(property="acudiente_id", type="integer", description="ID del acudiente del estudiante"),
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
        // Obtener el estudiante del parámetro de ruta
        $estudiante = $this->route('estudiante');
        $studentId = $estudiante?->id;
        $userId = $estudiante?->user_id;
        
        return [
            'nombre' => 'sometimes|string|max:255',
            'apellido' => 'sometimes|string|max:255',
            'tipo_documento' => 'sometimes|string|in:CC,TI,CE',
            'numero_documento' => 'sometimes|string|max:20',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $userId,
            'username' => 'sometimes|string|max:255|unique:users,username,' . $userId,
            'password' => 'sometimes|string|min:8',
            'codigo_estudiantil' => 'sometimes|string|max:50|unique:estudiantes,codigo_estudiantil,' . $studentId,
            'fecha_nacimiento' => 'sometimes|date',
            'genero' => 'sometimes|string|in:M,F,O',
            'direccion' => 'sometimes|string|max:255',
            'telefono' => 'sometimes|string|max:20',
            'institucion_id' => 'sometimes|integer|exists:instituciones,id',
            'grupo_id' => 'sometimes|integer|exists:grupos,id',
            'acudiente_id' => 'sometimes|integer|exists:acudientes,id',
            'estado' => 'sometimes|string|in:activo,inactivo',
        ];
    }
}
