<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateAulaRequest",
 *     title="Solicitud para Actualizar Aula",
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del aula"),
 *     @OA\Property(property="capacidad", type="integer", minimum=1, description="Capacidad de estudiantes del aula"),
 *     @OA\Property(property="institucion_id", type="integer", description="ID de la institución a la que pertenece el aula"),
 * )
 */
class UpdateAulaRequest extends FormRequest
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
        $aulaId = $this->aula?->id;
        
        return [
            'nombre' => 'sometimes|string|max:255|unique:aulas,nombre,' . $aulaId,
            'capacidad' => 'sometimes|integer|min:1',
            'institucion_id' => 'sometimes|integer|exists:instituciones,id',
        ];
    }
}
