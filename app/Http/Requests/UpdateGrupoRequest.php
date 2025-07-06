<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateGrupoRequest",
 *     title="Solicitud para Actualizar Grupo",
 *
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del grupo (ej. 11A, 5B)"),
 *     @OA\Property(property="sede_id", type="integer", description="ID de la sede a la que pertenece el grupo"),
 *     @OA\Property(property="anio_id", type="integer", description="ID del año académico al que pertenece el grupo"),
 *     @OA\Property(property="grado_id", type="integer", description="ID del grado al que pertenece el grupo"),
 *     @OA\Property(property="director_docente_id", type="integer", nullable=true, description="ID del docente director del grupo (opcional)"),
 * )
 */
class UpdateGrupoRequest extends FormRequest
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
            'nombre' => 'sometimes|string|max:255',
            'sede_id' => 'sometimes|integer|exists:sedes,id',
            'anio_id' => 'sometimes|integer|exists:anios,id',
            'grado_id' => 'sometimes|integer|exists:grados,id',
            'director_docente_id' => 'sometimes|nullable|integer|exists:docentes,id',
        ];
    }
}
