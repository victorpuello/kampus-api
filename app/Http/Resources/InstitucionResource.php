<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="InstitucionResource",
 *     title="Institucion Resource",
 *     description="Recurso de institución",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nombre", type="string", example="Institución Educativa Ejemplo"),
 *     @OA\Property(property="siglas", type="string", example="IEE"),
 *     @OA\Property(property="slogan", type="string", example="Educando para el futuro"),
 *     @OA\Property(property="dane", type="string", example="123456789"),
 *     @OA\Property(property="resolucion_aprobacion", type="string", example="Resolución 1234 de 2020"),
 *     @OA\Property(property="direccion", type="string", example="Calle 123 #45-67"),
 *     @OA\Property(property="telefono", type="string", example="3001234567"),
 *     @OA\Property(property="email", type="string", example="info@institucion.edu.co"),
 *     @OA\Property(property="rector", type="string", example="Dr. Juan Pérez"),
 *     @OA\Property(property="escudo", type="string", example="escudos/institucion.png"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="sedes",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/SedeResource")
 *     )
 * )
 */
class InstitucionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'siglas' => $this->siglas,
            'slogan' => $this->slogan,
            'dane' => $this->dane,
            'resolucion_aprobacion' => $this->resolucion_aprobacion,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'rector' => $this->rector,
            'escudo' => $this->escudo ? asset('storage/' . $this->escudo) : null,
            'created_at' => $this->created_at ? $this->created_at->toISOString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toISOString() : null,
        ];

        // Solo incluir sedes si están cargadas
        if ($this->relationLoaded('sedes')) {
            $data['sedes'] = SedeResource::collection($this->sedes);
        }

        return $data;
    }
}