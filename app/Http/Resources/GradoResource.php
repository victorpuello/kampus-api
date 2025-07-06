<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="GradoResource",
 *     title="Recurso de Grado",
 *     description="Representación de un grado académico en la API",
 *
 *     @OA\Property(property="id", type="integer", description="ID del grado"),
 *     @OA\Property(property="nombre", type="string", description="Nombre del grado"),
 *     @OA\Property(property="nivel", type="string", description="Nivel del grado"),
 *     @OA\Property(property="descripcion", type="string", description="Descripción del grado"),
 *     @OA\Property(property="estado", type="string", description="Estado del grado"),
 *     @OA\Property(property="grupos_count", type="integer", description="Número de grupos asociados al grado"),
 *     @OA\Property(property="institucion", type="object", ref="#/components/schemas/InstitucionResource", description="Institución a la que pertenece el grado"),
 *     @OA\Property(property="grupos", type="array", @OA\Items(ref="#/components/schemas/GrupoResource"), description="Grupos asociados al grado"),
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
            'grupos_count' => $this->grupos_count ?? 0,
            'institucion' => new InstitucionResource($this->whenLoaded('institucion')),
            'grupos' => GrupoResource::collection($this->whenLoaded('grupos')),
        ];
    }
}
