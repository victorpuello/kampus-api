<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="RoleResource",
 *     title="Recurso de Rol",
 *     description="Representación de un rol en la API",
 *
 *     @OA\Property(property="id", type="integer", description="ID del rol"),
 *     @OA\Property(property="nombre", type="string", description="Nombre del rol"),
 *     @OA\Property(property="permissions", type="array", @OA\Items(ref="#/components/schemas/PermissionResource"), description="Permisos asociados al rol"),
 * )
 */
class RoleResource extends JsonResource
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
            'permissions' => PermissionResource::collection($this->permissions ?? []),
        ];
    }
}
