<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="StudentResource",
 *     title="Recurso de Estudiante",
 *     description="Representación de un estudiante en la API",
 *     @OA\Property(property="id", type="integer", description="ID del estudiante"),
 *     @OA\Property(property="codigo_estudiantil", type="string", description="Código estudiantil único"),
 *     @OA\Property(property="user", type="object", ref="#/components/schemas/UserResource", description="Datos del usuario asociado al estudiante"),
 *     @OA\Property(property="institucion", type="object", ref="#/components/schemas/InstitucionResource", description="Institución a la que pertenece el estudiante"),
 *     @OA\Property(property="acudientes", type="array", @OA\Items(ref="#/components/schemas/AcudienteResource"), description="Acudientes asociados al estudiante"),
 * )
 */
class StudentResource extends JsonResource
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
            'codigo_estudiantil' => $this->codigo_estudiantil,
            'user' => new UserResource($this->whenLoaded('user')),
            'institucion' => new InstitucionResource($this->whenLoaded('institucion')),
            'acudientes' => AcudienteResource::collection($this->whenLoaded('acudientes')),
        ];
    }
}