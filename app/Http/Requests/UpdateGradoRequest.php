<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateGradoRequest",
 *     title="Solicitud para Actualizar Grado",
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del grado (ej. Primero, Undécimo)"),
 *     @OA\Property(property="nivel", type="integer", description="Nivel numérico del grado (ej. 1, 11)"),
 *     @OA\Property(property="institucion_id", type="integer", description="ID de la institución a la que pertenece el grado"),
 * )
 */
class UpdateGradoRequest extends FormRequest
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
        $gradoId = $this->grado?->id;
        
        return [
            'nombre' => 'sometimes|string|max:255|unique:grados,nombre,' . $gradoId,
            'nivel' => 'sometimes|integer',
            'institucion_id' => 'sometimes|integer|exists:instituciones,id',
        ];
    }
}
