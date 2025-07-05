<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="GrupoResource",
 *     title="Recurso de Grupo",
 *     description="Representación de un grupo académico en la API",
 *     @OA\Property(property="id", type="integer", description="ID del grupo"),
 *     @OA\Property(property="nombre", type="string", description="Nombre del grupo"),
 *     @OA\Property(property="anio", type="object", ref="#/components/schemas/AnioResource", description="Año académico al que pertenece el grupo"),
 *     @OA\Property(property="grado", type="object", ref="#/components/schemas/GradoResource", description="Grado al que pertenece el grupo"),
 *     @OA\Property(property="sede", type="object", ref="#/components/schemas/SedeResource", description="Sede a la que pertenece el grupo"),
 *     @OA\Property(property="director_docente", type="object", ref="#/components/schemas/DocenteResource", description="Docente director del grupo"),
 *     @OA\Property(property="estudiantes", type="array", @OA\Items(ref="#/components/schemas/StudentResource"), description="Estudiantes matriculados en el grupo"),
 * )
 */
class GrupoResource extends JsonResource
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
            'anio' => new AnioResource($this->whenLoaded('anio')),
            'grado' => new GradoResource($this->whenLoaded('grado')),
            'sede' => new SedeResource($this->whenLoaded('sede')),
            'director_docente' => new DocenteResource($this->whenLoaded('directorDocente')),
            'estudiantes' => StudentResource::collection($this->whenLoaded('estudiantes')),
        ];
    }
}
