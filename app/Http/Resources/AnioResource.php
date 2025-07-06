<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AnioResource",
 *     title="Recurso de Año Académico",
 *     description="Representación de un año académico en la API",
 *
 *     @OA\Property(property="id", type="integer", description="ID del año académico"),
 *     @OA\Property(property="nombre", type="string", description="Nombre del año académico"),
 *     @OA\Property(property="fecha_inicio", type="string", format="date", description="Fecha de inicio del año académico"),
 *     @OA\Property(property="fecha_fin", type="string", format="date", description="Fecha de fin del año académico"),
 *     @OA\Property(property="institucion", type="object", ref="#/components/schemas/InstitucionResource", description="Institución a la que pertenece el año"),
 *     @OA\Property(property="estado", type="string", description="Estado del año académico (activo, inactivo)"),
 * )
 */
class AnioResource extends JsonResource
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
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'institucion' => new InstitucionResource($this->whenLoaded('institucion')),
            'estado' => $this->estado,
        ];
    }
}
