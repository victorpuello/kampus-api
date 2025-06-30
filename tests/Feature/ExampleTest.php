<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Clase de prueba de ejemplo para características de la aplicación.
 *
 * Contiene una prueba básica para verificar el comportamiento de la ruta de login.
 */
class ExampleTest extends TestCase
{
    /**
     * Prueba que la ruta de login devuelve una respuesta de validación esperada (422).
     *
     * Verifica que al intentar acceder a la ruta de login sin credenciales,
     * la API devuelve un código de estado 422 (Unprocessable Entity),
     * indicando un error de validación.
     */
    public function test_the_login_route_returns_a_successful_response(): void
    {
        $response = $this->postJson('/api/v1/login');

        $response->assertStatus(422);
    }
}
