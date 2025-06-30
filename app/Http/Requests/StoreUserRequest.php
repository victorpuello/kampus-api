<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreUserRequest",
 *     title="Solicitud para Crear Usuario",
 *     required={
 *         "nombre", "apellido", "email", "username", "password",
 *         "institucion_id", "estado", "roles"
 *     },
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
class StoreUserRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return $this->user()->can('users.create');
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
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8',
            'institucion_id' => 'required|exists:instituciones,id',
            'estado' => 'required|in:activo,inactivo',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados para las reglas de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'apellido.required' => 'El apellido es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Este email ya está registrado',
            'username.required' => 'El nombre de usuario es obligatorio',
            'username.unique' => 'Este nombre de usuario ya está en uso',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'institucion_id.required' => 'La institución es obligatoria',
            'institucion_id.exists' => 'La institución seleccionada no existe',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado debe ser activo o inactivo',
            'roles.required' => 'Debe seleccionar al menos un rol',
            'roles.array' => 'Los roles deben ser un array',
            'roles.*.exists' => 'Uno o más roles seleccionados no existen',
        ];
    }
}
 