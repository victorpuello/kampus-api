<?php

namespace App\Http\Requests;

use App\Models\Grado;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreGradoRequest",
 *     title="Solicitud para Crear Grado",
 *     required={"nombre", "nivel"},
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del grado (ej. Primero, Undécimo)"),
 *     @OA\Property(property="nivel", type="string", enum={"Preescolar", "Básica Primaria", "Básica Secundaria", "Educación Media"}, description="Nivel educativo del grado"),
 * )
 */
class StoreGradoRequest extends FormRequest
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
        $user = auth()->user();
        
        if (!$user) {
            return [
                'nombre' => 'required|string|max:255',
                'nivel' => 'required|string|in:' . implode(',', Grado::getNivelesDisponibles()),
            ];
        }
        
        return [
            'nombre' => 'required|string|max:255|unique:grados,nombre,NULL,id,institucion_id,' . $user->institucion_id,
            'nivel' => 'required|string|in:' . implode(',', Grado::getNivelesDisponibles()),
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
            'nivel.in' => 'El nivel debe ser uno de los siguientes: ' . implode(', ', Grado::getNivelesDisponibles()),
        ];
    }
}
