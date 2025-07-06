<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Schema(
 *     schema="UpdateUserRequest",
 *     title="Solicitud para Actualizar Usuario",
 *
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del usuario"),
 *     @OA\Property(property="apellido", type="string", maxLength=255, description="Apellido del usuario"),
 *     @OA\Property(property="email", type="string", format="email", maxLength=255, description="Correo electrónico único del usuario"),
 *     @OA\Property(property="username", type="string", maxLength=255, description="Nombre de usuario único"),
 *     @OA\Property(property="password", type="string", minLength=8, description="Contraseña del usuario"),
 *     @OA\Property(property="institucion_id", type="integer", description="ID de la institución a la que pertenece el usuario"),
 *     @OA\Property(property="estado", type="string", enum={"activo", "inactivo"}, description="Estado del usuario"),
 *     @OA\Property(property="roles", type="array", @OA\Items(type="integer"), description="IDs de los roles asignados al usuario"),
 * )
 */
class UpdateUserRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepara los datos para la validación.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('password')) {
            $this->merge([
                'password' => Hash::make($this->password),
            ]);
        }
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user?->id;

        return [
            'nombre' => 'sometimes|string|max:255',
            'apellido' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$userId,
            'username' => 'sometimes|string|max:255|unique:users,username,'.$userId,
            'password' => 'sometimes|string|min:8',
            'institucion_id' => 'sometimes|integer|exists:instituciones,id',
            'estado' => 'sometimes|string|in:activo,inactivo',
            'roles' => 'sometimes|array',
            'roles.*' => 'integer|exists:roles,id',
        ];
    }
}
