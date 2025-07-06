<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateAnioRequest",
 *     title="Solicitud para Actualizar Año Académico",
 *
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del año académico (ej. 2024-2025)"),
 *     @OA\Property(property="fecha_inicio", type="string", format="date", description="Fecha de inicio del año académico"),
 *     @OA\Property(property="fecha_fin", type="string", format="date", description="Fecha de fin del año académico"),
 *     @OA\Property(property="institucion_id", type="integer", description="ID de la institución a la que pertenece el año"),
 *     @OA\Property(property="estado", type="string", enum={"activo", "inactivo"}, description="Estado del año académico"),
 * )
 */
class UpdateAnioRequest extends FormRequest
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
        $anioId = $this->anio?->id;

        return [
            'nombre' => 'sometimes|string|max:255|unique:anios,nombre,'.$anioId,
            'fecha_inicio' => 'sometimes|date',
            'fecha_fin' => 'sometimes|date|after:fecha_inicio',
            'institucion_id' => 'sometimes|integer|exists:instituciones,id',
            'estado' => 'sometimes|string|in:activo,inactivo',
        ];
    }
}
