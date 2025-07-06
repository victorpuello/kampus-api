<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AreaResource",
 *     title="Recurso de Área",
 *     description="Representación de un área académica en la API",
 *
 *     @OA\Property(property="id", type="integer", description="ID del área"),
 *     @OA\Property(property="nombre", type="string", description="Nombre del área"),
 *     @OA\Property(property="descripcion", type="string", description="Descripción del área"),
 *     @OA\Property(property="color", type="string", description="Color del área"),
 *     @OA\Property(property="institucion", type="object", ref="#/components/schemas/InstitucionResource", description="Institución a la que pertenece el área"),
 *     @OA\Property(property="asignaturas", type="array", @OA\Items(ref="#/components/schemas/AsignaturaResource"), description="Asignaturas asociadas al área"),
 *     @OA\Property(property="asignaturas_count", type="integer", description="Número de asignaturas asociadas al área"),
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
            'descripcion' => $this->descripcion,
            'color' => $this->color,
            'institucion' => new InstitucionResource($this->whenLoaded('institucion')),
            'asignaturas' => AsignaturaResource::collection($this->whenLoaded('asignaturas')),
            'asignaturas_count' => $this->asignaturas_count,
        ];
    }
}
