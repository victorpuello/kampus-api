<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateInstitucionRequest",
 *     title="Solicitud para Actualizar Institución",
 *     @OA\Property(property="nombre", type="string", maxLength=255, description="Nombre completo de la institución"),
 *     @OA\Property(property="siglas", type="string", maxLength=10, description="Siglas únicas de la institución"),
 *     @OA\Property(property="slogan", type="string", maxLength=255, description="Slogan de la institución"),
 *     @OA\Property(property="dane", type="string", maxLength=20, description="Código DANE de la institución"),
 *     @OA\Property(property="resolucion_aprobacion", type="string", maxLength=100, description="Resolución de aprobación"),
 *     @OA\Property(property="direccion", type="string", maxLength=500, description="Dirección de la institución"),
 *     @OA\Property(property="telefono", type="string", maxLength=20, description="Teléfono de la institución"),
 *     @OA\Property(property="email", type="string", maxLength=255, description="Email de la institución"),
 *     @OA\Property(property="rector", type="string", maxLength=255, description="Nombre del rector"),
 *     @OA\Property(property="escudo", type="string", maxLength=255, description="Ruta del escudo de la institución"),
 * )
 */
class UpdateInstitucionRequest extends FormRequest
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
        $institucionId = $this->institucion?->id;
        
        return [
            'nombre' => 'sometimes|string|max:255|min:3',
            'siglas' => 'sometimes|string|max:10|min:2|unique:instituciones,siglas,' . $institucionId,
            'slogan' => 'nullable|string|max:255',
            'dane' => 'nullable|string|max:20',
            'resolucion_aprobacion' => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'rector' => 'nullable|string|max:255',
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
            'nombre.required' => 'El nombre de la institución es requerido.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'siglas.required' => 'Las siglas son requeridas.',
            'siglas.min' => 'Las siglas deben tener al menos 2 caracteres.',
            'siglas.max' => 'Las siglas no pueden exceder 10 caracteres.',
            'siglas.unique' => 'Las siglas ya están en uso.',
            'slogan.max' => 'El slogan no puede exceder 255 caracteres.',
            'dane.max' => 'El código DANE no puede exceder 20 caracteres.',
            'resolucion_aprobacion.max' => 'La resolución de aprobación no puede exceder 100 caracteres.',
            'direccion.max' => 'La dirección no puede exceder 500 caracteres.',
            'telefono.max' => 'El teléfono no puede exceder 20 caracteres.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.max' => 'El email no puede exceder 255 caracteres.',
            'rector.max' => 'El nombre del rector no puede exceder 255 caracteres.',
            'escudo.file' => 'El escudo debe ser un archivo.',
            'escudo.image' => 'El escudo debe ser una imagen.',
            'escudo.mimes' => 'El escudo debe ser una imagen en formato: jpg, jpeg, png, gif o webp.',
            'escudo.max' => 'El escudo no puede ser mayor a 5MB.',
            'escudo.dimensions' => 'El escudo debe tener dimensiones entre 100x100 y 2000x2000 píxeles.',
        ];
    }
}
