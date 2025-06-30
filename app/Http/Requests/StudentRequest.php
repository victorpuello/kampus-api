<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'tipo_documento' => 'required|string|in:CC,CE,TI,RC',
            'numero_documento' => 'required|string|max:20',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|string|in:M,F,O',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'estado' => 'required|string|in:activo,inactivo',
            'institucion_id' => 'required|exists:instituciones,id',
            'acudiente_id' => 'required|exists:acudientes,id',
        ];

        if ($this->isMethod('POST')) {
            $rules['numero_documento'] .= '|unique:estudiantes,numero_documento';
            $rules['email'] .= '|unique:estudiantes,email';
        } else {
            $rules['numero_documento'] .= '|unique:estudiantes,numero_documento,' . $this->route('estudiante');
            $rules['email'] .= '|unique:estudiantes,email,' . $this->route('estudiante');
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'apellido.required' => 'El apellido es obligatorio',
            'tipo_documento.required' => 'El tipo de documento es obligatorio',
            'tipo_documento.in' => 'El tipo de documento debe ser CC, CE, TI o RC',
            'numero_documento.required' => 'El número de documento es obligatorio',
            'numero_documento.unique' => 'Este número de documento ya está registrado',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria',
            'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida',
            'genero.required' => 'El género es obligatorio',
            'genero.in' => 'El género debe ser M, F u O',
            'direccion.required' => 'La dirección es obligatoria',
            'telefono.required' => 'El teléfono es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado debe ser activo o inactivo',
            'institucion_id.required' => 'La institución es obligatoria',
            'institucion_id.exists' => 'La institución seleccionada no existe',
            'acudiente_id.required' => 'El acudiente es obligatorio',
            'acudiente_id.exists' => 'El acudiente seleccionado no existe',
        ];
    }
} 