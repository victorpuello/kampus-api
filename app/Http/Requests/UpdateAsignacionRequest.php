<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Periodo;

/**
 * @OA\Schema(
 *     schema="UpdateAsignacionRequest",
 *     title="Solicitud para Actualizar Asignación",
 *     @OA\Property(property="docente_id", type="integer", description="ID del docente asignado"),
 *     @OA\Property(property="asignatura_id", type="integer", description="ID de la asignatura asignada"),
 *     @OA\Property(property="grupo_id", type="integer", description="ID del grupo al que se asigna"),
 *     @OA\Property(property="franja_horaria_id", type="integer", description="ID de la franja horaria"),
 *     @OA\Property(property="dia_semana", type="string", enum={"lunes","martes","miercoles","jueves","viernes","sabado"}, description="Día de la semana"),
 *     @OA\Property(property="anio_academico_id", type="integer", description="ID del año académico"),
 *     @OA\Property(property="periodo_id", type="integer", nullable=true, description="ID del período (opcional, debe pertenecer al año académico)"),
 *     @OA\Property(property="estado", type="string", enum={"activo","inactivo"}, description="Estado de la asignación"),
 * )
 */
class UpdateAsignacionRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'docente_id' => 'sometimes|integer|exists:docentes,id',
            'asignatura_id' => 'sometimes|integer|exists:asignaturas,id',
            'grupo_id' => 'sometimes|integer|exists:grupos,id',
            'franja_horaria_id' => 'sometimes|integer|exists:franjas_horarias,id',
            'dia_semana' => 'sometimes|string|in:lunes,martes,miercoles,jueves,viernes,sabado',
            'anio_academico_id' => 'sometimes|integer|exists:anios,id',
            'periodo_id' => [
                'nullable',
                'integer',
                'exists:periodos,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $anioId = $this->anio_academico_id ?? $this->route('asignacion')->anio_academico_id;
                        
                        if ($anioId) {
                            $periodo = Periodo::where('id', $value)
                                ->where('anio_id', $anioId)
                                ->first();
                            
                            if (!$periodo) {
                                $fail('El período seleccionado no pertenece al año académico especificado.');
                            }
                        }
                    }
                }
            ],
            'estado' => 'sometimes|string|in:activo,inactivo',
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados para las reglas de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'docente_id.exists' => 'El docente seleccionado no existe.',
            'asignatura_id.exists' => 'La asignatura seleccionada no existe.',
            'grupo_id.exists' => 'El grupo seleccionado no existe.',
            'franja_horaria_id.exists' => 'La franja horaria seleccionada no existe.',
            'dia_semana.in' => 'El día de la semana debe ser válido.',
            'anio_academico_id.exists' => 'El año académico seleccionado no existe.',
            'periodo_id.exists' => 'El período seleccionado no existe.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ];
    }
}
