<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     title="Solicitud de Inicio de Sesión",
 *     required={"email", "password"},
 *
 *     @OA\Property(property="email", type="string", format="email", description="Correo electrónico del usuario"),
 *     @OA\Property(property="password", type="string", format="password", description="Contraseña del usuario"),
 * )
 */
class LoginRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required',
        ];
    }
}
