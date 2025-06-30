<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreFranjaHorariaRequest",
 *     title="Solicitud para Crear Franja Horaria",
 *     required={"hora_inicio", "hora_fin", "institucion_id"},
 *     @OA\Property(property="hora_inicio", type="string", format="time", description="Hora de inicio de la franja horaria (HH:MM)"),
 *     @OA\Property(property="hora_fin", type="string", format="time", description="Hora de fin de la franja horaria (HH:MM)"),
 *     @OA\Property(property="institucion_id", type="integer", description="ID de la institución a la que pertenece la franja horaria"),
 * )
 */
class StoreFranjaHorariaRequest extends FormRequest
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
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'institucion_id' => 'required|integer|exists:instituciones,id',
        ];
    }
}
