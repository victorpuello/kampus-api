<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AreaResource",
 *     title="Recurso de Área",
 *     description="Representación de un área académica en la API",
 *     @OA\Property(property="id", type="integer", description="ID del área"),
 *     @OA\Property(property="nombre", type="string", description="Nombre del área"),
 *     @OA\Property(property="institucion", type="object", ref="#/components/schemas/InstitucionResource", description="Institución a la que pertenece el área"),
 * )
 */
class AreaResource extends JsonResource
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
            'institucion' => new InstitucionResource($this->whenLoaded('institucion')),
        ];
    }
}
