<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="SedeResource",
 *     title="Sede Resource",
 *     description="Recurso de sede",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="institucion_id", type="integer", example=1),
 *     @OA\Property(property="nombre", type="string", example="Sede Principal"),
 *     @OA\Property(property="direccion", type="string", example="Calle 123 #45-67"),
 *     @OA\Property(property="telefono", type="string", example="3001234567"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="institucion",
 *         ref="#/components/schemas/InstitucionResource"
 *     )
 * )
 */
class SedeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'institucion_id' => $this->institucion_id,
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'institucion' => new InstitucionResource($this->whenLoaded('institucion')),
        ];
    }
}
