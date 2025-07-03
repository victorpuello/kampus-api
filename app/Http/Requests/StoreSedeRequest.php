<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSedeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'institucion_id' => 'required|exists:instituciones,id',
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:500',
            'telefono' => 'nullable|string|max:20',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'institucion_id.required' => 'La institución es requerida.',
            'institucion_id.exists' => 'La institución seleccionada no existe.',
            'nombre.required' => 'El nombre de la sede es requerido.',
            'nombre.max' => 'El nombre de la sede no puede exceder 255 caracteres.',
            'direccion.required' => 'La dirección es requerida.',
            'direccion.max' => 'La dirección no puede exceder 500 caracteres.',
            'telefono.max' => 'El teléfono no puede exceder 20 caracteres.',
        ];
    }
}
