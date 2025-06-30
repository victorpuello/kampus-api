<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreAsignaturaRequest",
 *     title="Solicitud para Crear Asignatura",
 *     required={"nombre", "porcentaje_area", "area_id"},
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre de la asignatura"),
 *     @OA\Property(property="porcentaje_area", type="number", format="float", minimum=0, maximum=100, description="Porcentaje que representa la asignatura dentro del área"),
 *     @OA\Property(property="area_id", type="integer", description="ID del área a la que pertenece la asignatura"),
 * )
 */
class StoreAsignaturaRequest extends FormRequest
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
            'porcentaje_area' => 'required|numeric|min:0|max:100',
            'area_id' => 'required|integer|exists:areas,id',
        ];
    }
}
