<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreGrupoRequest",
 *     title="Solicitud para Crear Grupo",
 *     required={"nombre", "anio_id", "grado_id"},
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del grupo (ej. 11A, 5B)"),
 *     @OA\Property(property="anio_id", type="integer", description="ID del año académico al que pertenece el grupo"),
 *     @OA\Property(property="grado_id", type="integer", description="ID del grado al que pertenece el grupo"),
 *     @OA\Property(property="director_docente_id", type="integer", nullable=true, description="ID del docente director del grupo (opcional)"),
 * )
 */
class StoreGrupoRequest extends FormRequest
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
            'anio_id' => 'required|integer|exists:anios,id',
            'grado_id' => 'required|integer|exists:grados,id',
            'director_docente_id' => 'nullable|integer|exists:docentes,id',
        ];
    }
}
