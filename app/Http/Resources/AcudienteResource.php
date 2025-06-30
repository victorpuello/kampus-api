<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AcudienteResource",
 *     title="Recurso de Acudiente",
 *     description="Representación de un acudiente en la API",
 *     @OA\Property(property="id", type="integer", description="ID del acudiente"),
 *     @OA\Property(property="nombre", type="string", description="Nombre del acudiente"),
 *     @OA\Property(property="telefono", type="string", nullable=true, description="Número de teléfono del acudiente"),
 *     @OA\Property(property="email", type="string", format="email", nullable=true, description="Correo electrónico del acudiente"),
 *     @OA\Property(property="user", type="object", ref="#/components/schemas/UserResource", description="Usuario asociado al acudiente (si existe)"),
 * )
 */
class AcudienteResource extends JsonResource
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
            'telefono' => $this->telefono,
            'email' => $this->email,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
