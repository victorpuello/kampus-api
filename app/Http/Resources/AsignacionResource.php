<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AsignacionResource",
 *     title="Recurso de Asignación",
 *     description="Representación de una asignación académica completa en la API",
 *
 *     @OA\Property(property="id", type="integer", description="ID de la asignación"),
 *     @OA\Property(property="docente", type="object", ref="#/components/schemas/DocenteResource", description="Docente asignado"),
 *     @OA\Property(property="asignatura", type="object", ref="#/components/schemas/AsignaturaResource", description="Asignatura asignada"),
 *     @OA\Property(property="grupo", type="object", ref="#/components/schemas/GrupoResource", description="Grupo al que se asigna"),
 *     @OA\Property(property="franja_horaria", type="object", ref="#/components/schemas/FranjaHorariaResource", description="Franja horaria de la asignación"),
 *     @OA\Property(property="dia_semana", type="string", description="Día de la semana"),
 *     @OA\Property(property="anio_academico", type="object", ref="#/components/schemas/AnioResource", description="Año académico"),
 *     @OA\Property(property="periodo", type="object", ref="#/components/schemas/PeriodoResource", description="Período académico"),
 *     @OA\Property(property="estado", type="string", description="Estado de la asignación"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de actualización"),
 * )
 */
class AsignacionResource extends JsonResource
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
            'docente' => new DocenteResource($this->whenLoaded('docente')),
            'asignatura' => new AsignaturaResource($this->whenLoaded('asignatura')),
            'grupo' => new GrupoResource($this->whenLoaded('grupo')),
            'franja_horaria' => new FranjaHorariaResource($this->whenLoaded('franjaHoraria')),
            'dia_semana' => $this->dia_semana,
            'anio_academico' => new AnioResource($this->whenLoaded('anioAcademico')),
            'periodo' => new PeriodoResource($this->whenLoaded('periodo')),
            'estado' => $this->estado,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Atributos calculados
            'nombre_docente' => $this->nombre_docente,
            'nombre_asignatura' => $this->nombre_asignatura,
            'nombre_grupo' => $this->nombre_grupo,
        ];
    }
}
