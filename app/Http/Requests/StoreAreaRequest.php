<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreAreaRequest",
 *     title="Solicitud para Crear Área",
 *     required={"nombre"},
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del área académica"),
 * )
 */
class StoreAreaRequest extends FormRequest
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
        $user = auth()->user();
        
        if (!$user) {
            return [
                'nombre' => 'required|string|max:255',
            ];
        }
        
        return [
            'nombre' => 'required|string|max:255|unique:areas,nombre,NULL,id,institucion_id,' . $user->institucion_id,
        ];
    }
}
