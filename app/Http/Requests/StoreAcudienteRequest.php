<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreAcudienteRequest",
 *     title="Solicitud para Crear Acudiente",
 *     required={"nombre"},
 *
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre completo del acudiente"),
 *     @OA\Property(property="telefono", type="string", maxLength=50, nullable=true, description="Número de teléfono del acudiente"),
 *     @OA\Property(property="email", type="string", format="email", maxLength=255, nullable=true, description="Correo electrónico único del acudiente"),
 *     @OA\Property(property="user_id", type="integer", nullable=true, description="ID del usuario asociado al acudiente (opcional)"),
 * )
 */
class StoreAcudienteRequest extends FormRequest
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
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|string|email|max:255|unique:acudientes',
            'user_id' => 'nullable|integer|exists:users,id',
        ];
    }
}
