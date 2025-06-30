<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="InstitucionResource",
 *     title="Recurso de Institución",
 *     description="Representación de una institución en la API",
 *     @OA\Property(property="id", type="integer", description="ID de la institución"),
 *     @OA\Property(property="nombre", type="string", description="Nombre de la institución"),
 *     @OA\Property(property="siglas", type="string", description="Siglas de la institución"),
 * )
 */
class InstitucionResource extends JsonResource
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
            'siglas' => $this->siglas,
        ];
    }
}