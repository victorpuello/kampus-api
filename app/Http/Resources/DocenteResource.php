<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="DocenteResource",
 *     title="Recurso de Docente",
 *     description="Representación de un docente en la API",
 *     @OA\Property(property="id", type="integer", description="ID del docente"),
 *     @OA\Property(property="user", type="object", ref="#/components/schemas/UserResource", description="Datos del usuario asociado al docente"),
 * )
 */
class DocenteResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
