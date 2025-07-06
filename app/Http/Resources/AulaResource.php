<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AulaResource",
 *     title="Recurso de Aula",
 *     description="Representación de un aula en la API",
 *
 *     @OA\Property(property="id", type="integer", description="ID del aula"),
 *     @OA\Property(property="nombre", type="string", description="Nombre del aula"),
 *     @OA\Property(property="capacidad", type="integer", description="Capacidad del aula"),
 *     @OA\Property(property="institucion", type="object", ref="#/components/schemas/InstitucionResource", description="Institución a la que pertenece el aula"),
 * )
 */
class AulaResource extends JsonResource
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
            'capacidad' => $this->capacidad,
            'institucion' => new InstitucionResource($this->whenLoaded('institucion')),
        ];
    }
}
