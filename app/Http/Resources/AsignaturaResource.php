<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AsignaturaResource",
 *     title="Recurso de Asignatura",
 *     description="Representación de una asignatura en la API",
 *     @OA\Property(property="id", type="integer", description="ID de la asignatura"),
 *     @OA\Property(property="nombre", type="string", description="Nombre de la asignatura"),
 *     @OA\Property(property="porcentaje_area", type="number", format="float", description="Porcentaje que representa la asignatura dentro del área"),
 *     @OA\Property(property="area", type="object", ref="#/components/schemas/AreaResource", description="Área a la que pertenece la asignatura"),
 * )
 */
class AsignaturaResource extends JsonResource
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
            'nombre' => $this->nombre,
            'porcentaje_area' => $this->porcentaje_area,
            'area' => new AreaResource($this->whenLoaded('area')),
        ];
    }
}
