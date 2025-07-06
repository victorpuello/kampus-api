<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreAulaRequest",
 *     title="Solicitud para Crear Aula",
 *     required={"nombre", "tipo", "capacidad", "institucion_id"},
 *
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre del aula"),
 *     @OA\Property(property="tipo", type="string", enum={"Salón", "Laboratorio", "Auditorio", "Deportivo"}, description="Tipo de aula"),
 *     @OA\Property(property="capacidad", type="integer", minimum=1, description="Capacidad de estudiantes del aula"),
 *     @OA\Property(property="institucion_id", type="integer", description="ID de la institución a la que pertenece el aula"),
 * )
 */
class StoreAulaRequest extends FormRequest
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
            'nombre' => 'required|string|max:255|unique:aulas',
            'tipo' => 'required|string|in:Salón,Laboratorio,Auditorio,Deportivo',
            'capacidad' => 'required|integer|min:1',
            'institucion_id' => 'required|integer|exists:instituciones,id',
        ];
    }
}
