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
 *     @OA\Property(property="telefono", type="string", nullable=true, description="Teléfono del docente"),
 *     @OA\Property(property="especialidad", type="string", nullable=true, description="Especialidad del docente"),
 *     @OA\Property(property="fecha_contratacion", type="string", format="date", nullable=true, description="Fecha de contratación del docente"),
 *     @OA\Property(property="salario", type="number", format="float", nullable=true, description="Salario del docente"),
 *     @OA\Property(property="horario_trabajo", type="string", nullable=true, description="Horario de trabajo del docente"),
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
            'nombre' => $this->user?->nombre,
            'apellido' => $this->user?->apellido,
            'email' => $this->user?->email,
            'user' => new UserResource($this->whenLoaded('user')),
            'telefono' => $this->telefono,
            'especialidad' => $this->especialidad,
            'estado' => $this->user?->estado,
            'institucion' => $this->user?->institucion ? [
                'id' => $this->user->institucion->id,
                'nombre' => $this->user->institucion->nombre,
            ] : null,
            'fecha_contratacion' => $this->fecha_contratacion,
            'salario' => $this->salario,
            'horario_trabajo' => $this->horario_trabajo,
        ];
    }
}
