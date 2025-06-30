<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreAreaRequest",
 *     title="Solicitud para Crear Área",
 *     required={"nombre", "institucion_id"},
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del área académica"),
 *     @OA\Property(property="institucion_id", type="integer", description="ID de la institución a la que pertenece el área"),
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
        return [
            'nombre' => 'required|string|max:255|unique:areas',
            'institucion_id' => 'required|integer|exists:instituciones,id',
        ];
    }
}
