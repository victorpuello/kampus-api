<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     title="Recurso de Usuario",
 *     description="Representación de un usuario en la API",
 *     @OA\Property(property="id", type="integer", description="ID del usuario"),
 *     @OA\Property(property="nombre", type="string", description="Nombre del usuario"),
 *     @OA\Property(property="apellido", type="string", description="Apellido del usuario"),
 *     @OA\Property(property="username", type="string", description="Nombre de usuario"),
 *     @OA\Property(property="email", type="string", format="email", description="Correo electrónico del usuario"),
 *     @OA\Property(property="tipo_documento", type="string", description="Tipo de documento (CC, TI, CE, etc.)"),
 *     @OA\Property(property="numero_documento", type="string", description="Número de documento"),
 *     @OA\Property(property="estado", type="string", description="Estado del usuario (activo, inactivo)"),
 *     @OA\Property(property="roles", type="array", @OA\Items(ref="#/components/schemas/RoleResource"), description="Roles del usuario"),
 *     @OA\Property(property="institucion", type="object", ref="#/components/schemas/InstitucionResource", description="Institución a la que pertenece el usuario"),
 * )
 */
class UserResource extends JsonResource
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
            'apellido' => $this->apellido,
            'username' => $this->username,
            'email' => $this->email,
            'tipo_documento' => $this->tipo_documento,
            'numero_documento' => $this->numero_documento,
            'institucion_id' => $this->institucion_id,
            'estado' => $this->estado,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'institucion' => new InstitucionResource($this->whenLoaded('institucion')),
        ];
    }
}