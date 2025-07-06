<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="PermissionResource",
 *     title="Recurso de Permiso",
 *     description="Representación de un permiso en la API",
 *
 *     @OA\Property(property="id", type="integer", description="ID del permiso"),
 *     @OA\Property(property="nombre", type="string", description="Nombre del permiso"),
 *     @OA\Property(property="descripcion", type="string", description="Descripción del permiso"),
 * )
 */
class PermissionResource extends JsonResource
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
        ];
    }
}
