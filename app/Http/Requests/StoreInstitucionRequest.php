<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StoreInstitucionRequest",
 *     title="Solicitud para Crear Institución",
 *     required={"nombre", "siglas"},
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre completo de la institución"),
 *     @OA\Property(property="siglas", type="string", maxLength=10, description="Siglas únicas de la institución"),
 * )
 */
class StoreInstitucionRequest extends FormRequest
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
            'nombre' => ['required', 'string', 'min:3', 'max:255'],
            'siglas' => ['required', 'string', 'min:2', 'max:10'],
            'slogan' => ['nullable', 'string', 'max:255'],
            'dane' => ['nullable', 'string', 'max:20'],
            'resolucion_aprobacion' => ['nullable', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'rector' => ['nullable', 'string', 'max:255'],
            'escudo' => [
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:5120', // 5MB
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ],
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
            'nombre.required' => 'El nombre de la institución es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            
            'siglas.required' => 'Las siglas son obligatorias.',
            'siglas.min' => 'Las siglas deben tener al menos 2 caracteres.',
            'siglas.max' => 'Las siglas no pueden tener más de 10 caracteres.',
            
            'slogan.max' => 'El slogan no puede tener más de 255 caracteres.',
            'dane.max' => 'El código DANE no puede tener más de 20 caracteres.',
            'resolucion_aprobacion.max' => 'La resolución de aprobación no puede tener más de 100 caracteres.',
            'direccion.max' => 'La dirección no puede tener más de 255 caracteres.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.max' => 'El email no puede tener más de 255 caracteres.',
            'rector.max' => 'El nombre del rector no puede tener más de 255 caracteres.',
            
            'escudo.file' => 'El escudo debe ser un archivo.',
            'escudo.image' => 'El escudo debe ser una imagen.',
            'escudo.mimes' => 'El escudo debe ser una imagen en formato: jpg, jpeg, png, gif o webp.',
            'escudo.max' => 'El escudo no puede ser mayor a 5MB.',
            'escudo.dimensions' => 'El escudo debe tener dimensiones entre 100x100 y 2000x2000 píxeles.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'nombre' => 'nombre de la institución',
            'siglas' => 'siglas',
            'slogan' => 'slogan',
            'dane' => 'código DANE',
            'resolucion_aprobacion' => 'resolución de aprobación',
            'direccion' => 'dirección',
            'telefono' => 'teléfono',
            'email' => 'email',
            'rector' => 'rector',
            'escudo' => 'escudo',
        ];
    }
}
