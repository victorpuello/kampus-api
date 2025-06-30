<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AsignacionResource",
 *     title="Recurso de Asignación",
 *     description="Representación de una asignación de docente a asignatura y grupo en la API",
 *     @OA\Property(property="id", type="integer", description="ID de la asignación"),
 *     @OA\Property(property="docente", type="object", ref="#/components/schemas/DocenteResource", description="Docente asignado"),
 *     @OA\Property(property="asignatura", type="object", ref="#/components/schemas/AsignaturaResource", description="Asignatura asignada"),
 *     @OA\Property(property="grupo", type="object", ref="#/components/schemas/GrupoResource", description="Grupo al que se asigna"),
 *     @OA\Property(property="anio", type="object", ref="#/components/schemas/AnioResource", description="Año académico de la asignación"),
 * )
 */
class AsignacionResource extends JsonResource
{
    /**
     * Transforma el recurso en un array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'docente' => new DocenteResource($this->whenLoaded('docente')),
            'asignatura' => new AsignaturaResource($this->whenLoaded('asignatura')),
            'grupo' => new GrupoResource($this->whenLoaded('grupo')),
            'anio' => new AnioResource($this->whenLoaded('anio')),
        ];
    }
}
