<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateInstitucionRequest",
 *     title="Solicitud para Actualizar Institución",
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre completo de la institución"),
 *     @OA\Property(property="siglas", type="string", maxLength=10, description="Siglas únicas de la institución"),
 * )
 */
class UpdateInstitucionRequest extends FormRequest
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
        $institucionId = $this->institucion?->id;
        
        return [
            'nombre' => 'sometimes|string|max:255',
            'siglas' => 'sometimes|string|max:10|unique:instituciones,siglas,' . $institucionId,
        ];
    }
}
