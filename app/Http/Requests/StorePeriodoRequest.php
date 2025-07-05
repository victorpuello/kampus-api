<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePeriodoRequest extends FormRequest
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
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'anio_id' => 'required|exists:anios,id',
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
            'nombre.required' => 'El nombre del periodo es requerido.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'fecha_inicio.required' => 'La fecha de inicio es requerida.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_fin.required' => 'La fecha de fin es requerida.',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'anio_id.required' => 'El año académico es requerido.',
            'anio_id.exists' => 'El año académico seleccionado no existe.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $anioId = $this->input('anio_id');
            $fechaInicio = $this->input('fecha_inicio');
            $fechaFin = $this->input('fecha_fin');

            if (!$anioId || !$fechaInicio || !$fechaFin) {
                return;
            }

            $anio = \App\Models\Anio::find($anioId);
            if (!$anio) {
                return;
            }

            // 1. Validar que el periodo no inicia antes del año académico
            if ($fechaInicio < $anio->fecha_inicio) {
                $validator->errors()->add('fecha_inicio', 'La fecha de inicio del periodo no puede ser anterior a la fecha de inicio del año académico ('.$anio->fecha_inicio.').');
            }

            // 2. Validar que el periodo no termina después del año académico
            if ($fechaFin > $anio->fecha_fin) {
                $validator->errors()->add('fecha_fin', 'La fecha de fin del periodo no puede ser posterior a la fecha de fin del año académico ('.$anio->fecha_fin.').');
            }

            // 3. Validar que no se crucen fechas con otros periodos del mismo año
            $periodos = $anio->periodos()->get();
            foreach ($periodos as $periodo) {
                // Si el nuevo periodo se solapa con uno existente
                if (
                    ($fechaInicio <= $periodo->fecha_fin && $fechaFin >= $periodo->fecha_inicio)
                ) {
                    $validator->errors()->add('fecha_inicio', 'Las fechas del periodo se cruzan con el periodo existente: "'.$periodo->nombre.'" ('.$periodo->fecha_inicio.' a '.$periodo->fecha_fin.').');
                    break;
                }
            }
        });
    }
} 