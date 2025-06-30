<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreAnioRequest",
 *     title="Solicitud para Crear Año Académico",
 *     required={
 *         "nombre", "fecha_inicio", "fecha_fin", "institucion_id", "estado"
 *     },
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del año académico (ej. 2024-2025)"),
 *     @OA\Property(property="fecha_inicio", type="string", format="date", description="Fecha de inicio del año académico"),
 *     @OA\Property(property="fecha_fin", type="string", format="date", description="Fecha de fin del año académico"),
 *     @OA\Property(property="institucion_id", type="integer", description="ID de la institución a la que pertenece el año"),
 *     @OA\Property(property="estado", type="string", enum={"activo", "inactivo"}, description="Estado del año académico"),
 * )
 */
class StoreAnioRequest extends FormRequest
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
            'nombre' => 'required|string|max:255|unique:anios',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'institucion_id' => 'required|integer|exists:instituciones,id',
            'estado' => 'required|string|in:activo,inactivo',
        ];
    }
}
