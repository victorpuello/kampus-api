<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateAreaRequest",
 *     title="Solicitud para Actualizar Área",
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del área académica"),
 *     @OA\Property(property="institucion_id", type="integer", description="ID de la institución a la que pertenece el área"),
 * )
 */
class UpdateAreaRequest extends FormRequest
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
        $areaId = $this->area?->id;
        
        return [
            'nombre' => 'sometimes|string|max:255|unique:areas,nombre,' . $areaId,
            'institucion_id' => 'sometimes|integer|exists:instituciones,id',
        ];
    }
}
