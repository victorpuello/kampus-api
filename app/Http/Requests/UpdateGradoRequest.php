<?php

namespace App\Http\Requests;

use App\Models\Grado;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateGradoRequest",
 *     title="Solicitud para Actualizar Grado",
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del grado (ej. Primero, Undécimo)"),
 *     @OA\Property(property="nivel", type="string", enum={"Preescolar", "Básica Primaria", "Básica Secundaria", "Educación Media"}, description="Nivel educativo del grado"),
 *     @OA\Property(property="institucion_id", type="integer", description="ID de la institución a la que pertenece el grado"),
 * )
 */
class UpdateGradoRequest extends FormRequest
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
        $gradoId = $this->grado?->id;
        
        return [
            'nombre' => 'sometimes|string|max:255|unique:grados,nombre,' . $gradoId,
            'nivel' => 'sometimes|string|in:' . implode(',', Grado::getNivelesDisponibles()),
            'institucion_id' => 'sometimes|integer|exists:instituciones,id',
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
