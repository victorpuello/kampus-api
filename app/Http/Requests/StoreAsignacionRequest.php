<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreAsignacionRequest",
 *     title="Solicitud para Crear Asignación",
 *     required={"docente_id", "asignatura_id", "grupo_id", "anio_id"},
 *     @OA\Property(property="docente_id", type="integer", description="ID del docente asignado"),
 *     @OA\Property(property="asignatura_id", type="integer", description="ID de la asignatura asignada"),
 *     @OA\Property(property="grupo_id", type="integer", description="ID del grupo al que se asigna"),
 *     @OA\Property(property="anio_id", type="integer", description="ID del año académico de la asignación"),
 * )
 */
class StoreAsignacionRequest extends FormRequest
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
            'docente_id' => 'required|integer|exists:docentes,id',
            'asignatura_id' => 'required|integer|exists:asignaturas,id',
            'grupo_id' => 'required|integer|exists:grupos,id',
            'anio_id' => 'required|integer|exists:anios,id',
        ];
    }
}
