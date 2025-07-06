<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="GradoResource",
 *     title="Recurso de Grado",
 *     description="Representación de un grado académico en la API",
 *     @OA\Property(property="id", type="integer", description="ID del grado"),
 *     @OA\Property(property="nombre", type="string", description="Nombre del grado"),
 *     @OA\Property(property="nivel", type="integer", description="Nivel numérico del grado"),
 *     @OA\Property(property="institucion", type="object", ref="#/components/schemas/InstitucionResource", description="Institución a la que pertenece el grado"),
 * )
 */
class GradoResource extends JsonResource
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
            'nivel' => $this->nivel,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'institucion' => new InstitucionResource($this->whenLoaded('institucion')),
            'grupos' => GrupoResource::collection($this->whenLoaded('grupos')),
        ];
    }
}
