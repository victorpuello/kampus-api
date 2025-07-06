<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateFranjaHorariaRequest",
 *     title="Solicitud para Actualizar Franja Horaria",
 *
 *     @OA\Property(property="hora_inicio", type="string", format="time", description="Hora de inicio de la franja horaria (HH:MM)"),
 *     @OA\Property(property="hora_fin", type="string", format="time", description="Hora de fin de la franja horaria (HH:MM)"),
 *     @OA\Property(property="institucion_id", type="integer", description="ID de la institución a la que pertenece la franja horaria"),
 * )
 */
class UpdateFranjaHorariaRequest extends FormRequest
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
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string',
            'hora_inicio' => 'sometimes|date_format:H:i',
            'hora_fin' => 'sometimes|date_format:H:i|after:hora_inicio',
            'duracion_minutos' => 'nullable|integer|min:1',
            'estado' => 'nullable|string|in:activo,inactivo,pendiente',
            'institucion_id' => 'nullable|integer|exists:instituciones,id',
        ];
    }
}
