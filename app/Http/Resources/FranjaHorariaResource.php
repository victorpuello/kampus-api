<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="FranjaHorariaResource",
 *     title="Recurso de Franja Horaria",
 *     description="Representación de una franja horaria en la API",
 *     @OA\Property(property="id", type="integer", description="ID de la franja horaria"),
 *     @OA\Property(property="hora_inicio", type="string", format="time", description="Hora de inicio de la franja horaria (HH:MM)"),
 *     @OA\Property(property="hora_fin", type="string", format="time", description="Hora de fin de la franja horaria (HH:MM)"),
 *     @OA\Property(property="institucion", type="object", ref="#/components/schemas/InstitucionResource", description="Institución a la que pertenece la franja horaria"),
 * )
 */
class FranjaHorariaResource extends JsonResource
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
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'institucion' => new InstitucionResource($this->whenLoaded('institucion')),
        ];
    }
}
