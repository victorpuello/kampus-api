<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="GrupoResource",
 *     title="Recurso de Grupo",
 *     description="Representación de un grupo académico en la API",
 *
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
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'sede' => new SedeResource($this->whenLoaded('sede')),
            'grado' => new GradoResource($this->whenLoaded('grado')),
            'anio' => new AnioResource($this->whenLoaded('anio')),
            'director_docente' => new DocenteResource($this->whenLoaded('directorDocente')),
            'estudiantes_count' => isset($this->estudiantes_count)
                ? $this->estudiantes_count
                : ($this->relationLoaded('estudiantes') ? $this->estudiantes->count() : 0),
            'estudiantes' => $this->whenLoaded('estudiantes', function () {
                return $this->estudiantes->map(function ($est) {
                    return [
                        'id' => $est->id,
                        'nombre' => null, // Los estudiantes no tienen nombre directo
                        'apellido' => null, // Los estudiantes no tienen apellido directo
                        'email' => null, // Los estudiantes no tienen email directo
                        'estado' => $est->estado,
                        'user' => $est->relationLoaded('user') && $est->user ? [
                            'nombre' => $est->user->nombre,
                            'apellido' => $est->user->apellido,
                            'email' => $est->user->email,
                        ] : null,
                    ];
                });
            }),
        ];
    }
}
