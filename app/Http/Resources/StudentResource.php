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
 *     @OA\Property(property="fecha_nacimiento", type="string", format="date", description="Fecha de nacimiento del estudiante"),
 *     @OA\Property(property="genero", type="string", description="Género del estudiante (M, F, O)"),
 *     @OA\Property(property="direccion", type="string", description="Dirección del estudiante"),
 *     @OA\Property(property="telefono", type="string", description="Teléfono del estudiante"),
 *     @OA\Property(property="estado", type="string", description="Estado del estudiante (activo, inactivo)"),
 *     @OA\Property(property="user", type="object", ref="#/components/schemas/UserResource", description="Datos del usuario asociado al estudiante"),
 *     @OA\Property(property="institucion", type="object", ref="#/components/schemas/InstitucionResource", description="Institución a la que pertenece el estudiante"),
 *     @OA\Property(property="acudiente", type="object", ref="#/components/schemas/AcudienteResource", description="Acudiente principal del estudiante"),
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
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'genero' => $this->genero,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'estado' => $this->estado,
            'user' => new UserResource($this->whenLoaded('user')),
            'institucion' => new InstitucionResource($this->whenLoaded('institucion')),
            'acudiente' => $this->whenLoaded('acudiente', function () {
                return $this->acudiente->first() ? new AcudienteResource($this->acudiente->first()) : null;
            }),
            'acudientes' => AcudienteResource::collection($this->whenLoaded('acudientes')),
        ];
    }
}