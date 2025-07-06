<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateAcudienteRequest",
 *     title="Solicitud para Actualizar Acudiente",
 *
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre completo del acudiente"),
 *     @OA\Property(property="telefono", type="string", maxLength=50, nullable=true, description="Número de teléfono del acudiente"),
 *     @OA\Property(property="email", type="string", format="email", maxLength=255, nullable=true, description="Correo electrónico único del acudiente"),
 *     @OA\Property(property="user_id", type="integer", nullable=true, description="ID del usuario asociado al acudiente (opcional)"),
 * )
 */
class UpdateAcudienteRequest extends FormRequest
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
        $acudienteId = $this->acudiente?->id;

        return [
            'nombre' => 'sometimes|string|max:255',
            'telefono' => 'sometimes|nullable|string|max:50',
            'email' => 'sometimes|nullable|string|email|max:255|unique:acudientes,email,'.$acudienteId,
            'user_id' => 'sometimes|nullable|integer|exists:users,id',
        ];
    }
}
