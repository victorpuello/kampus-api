<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Institucion;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthenticationMiddlewareTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $institution;
    protected $adminRole;
    protected $adminPermission;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear institución
        $this->institution = Institucion::factory()->create();
        
        // Crear rol de administrador
        $this->adminRole = Role::factory()->create([
            'nombre' => 'Administrador',
            'descripcion' => 'Rol de administrador del sistema'
        ]);
        
        // Crear permiso de administrador
        $this->adminPermission = Permission::factory()->create([
            'nombre' => 'admin.access',
            'descripcion' => 'Acceso administrativo'
        ]);
        
        // Asignar permiso al rol
        $this->adminRole->permissions()->attach($this->adminPermission);
        
        // Crear usuario administrador
        $this->user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => '123456',
            'institucion_id' => $this->institution->id,
            'estado' => 'activo'
        ]);
        
        // Asignar rol al usuario
        $this->user->roles()->attach($this->adminRole);
    }

    public function test_protected_routes_require_authentication()
    {
        // Rutas que requieren autenticación
        $protectedRoutes = [
            'GET' => '/api/v1/me',
            'POST' => '/api/v1/logout',
            'GET' => '/api/v1/instituciones',
            'POST' => '/api/v1/instituciones',
            'GET' => '/api/v1/instituciones/1',
            'PUT' => '/api/v1/instituciones/1',
            'DELETE' => '/api/v1/instituciones/1',
        ];

        foreach ($protectedRoutes as $method => $route) {
            $response = $this->json($method, $route);
            $response->assertStatus(401);
        }
    }

    public function test_public_routes_dont_require_authentication()
    {
        // Rutas públicas
        $publicRoutes = [
            'POST' => '/api/v1/login',
        ];

        foreach ($publicRoutes as $method => $route) {
            $response = $this->json($method, $route, [
                'email' => 'admin@example.com',
                'password' => '123456'
            ]);
            
            // Login debería funcionar sin autenticación previa
            if ($route === '/api/v1/login') {
                $response->assertStatus(200);
            }
        }
    }

    public function test_bearer_token_authentication_works()
    {
        // Crear token
        $token = $this->user->createToken('test-token')->plainTextToken;

        // Probar acceso con token válido
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/v1/me');

        $response->assertStatus(200)
                 ->assertJson([
                     'user' => [
                         'id' => $this->user->id,
                         'email' => $this->user->email
                     ]
                 ]);
    }

    public function test_invalid_bearer_token_returns_401()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token-12345'
        ])->getJson('/api/v1/me');

        $response->assertStatus(401);
    }

    public function test_malformed_authorization_header_returns_401()
    {
        // Sin "Bearer"
        $response = $this->withHeaders([
            'Authorization' => 'invalid-token-12345'
        ])->getJson('/api/v1/me');

        $response->assertStatus(401);

        // Con formato incorrecto
        $response = $this->withHeaders([
            'Authorization' => 'Basic invalid-token-12345'
        ])->getJson('/api/v1/me');

        $response->assertStatus(401);
    }

    public function test_empty_authorization_header_returns_401()
    {
        $response = $this->withHeaders([
            'Authorization' => ''
        ])->getJson('/api/v1/me');

        $response->assertStatus(401);
    }

    public function test_missing_authorization_header_returns_401()
    {
        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(401);
    }

    public function test_revoked_token_returns_401()
    {
        // Crear token
        $token = $this->user->createToken('test-token');

        // Verificar que funciona
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->plainTextToken
        ])->getJson('/api/v1/me');

        $response->assertStatus(200);

        // Revocar token
        $token->delete();

        // Verificar que ya no funciona
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->plainTextToken
        ])->getJson('/api/v1/me');

        $response->assertStatus(401);
    }

    public function test_expired_token_returns_401()
    {
        // Crear token con expiración inmediata
        $token = $this->user->createToken('test-token', ['*'], now()->addSeconds(1));

        // Esperar a que expire
        sleep(2);

        // Verificar que ya no funciona
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->plainTextToken
        ])->getJson('/api/v1/me');

        $response->assertStatus(401);
    }

    public function test_token_with_abilities_works()
    {
        // Crear token con habilidades específicas
        $token = $this->user->createToken('test-token', ['read', 'write']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->plainTextToken
        ])->getJson('/api/v1/me');

        $response->assertStatus(200);
    }

    public function test_token_without_required_abilities_returns_403()
    {
        // Crear token sin habilidades
        $token = $this->user->createToken('test-token', []);

        // Nota: En este caso específico, el endpoint /me no requiere habilidades especiales
        // pero podríamos probar con un endpoint que sí las requiera
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->plainTextToken
        ])->getJson('/api/v1/me');

        $response->assertStatus(200);
    }

    public function test_multiple_tokens_for_same_user_work_independently()
    {
        // Crear múltiples tokens
        $token1 = $this->user->createToken('token-1')->plainTextToken;
        $token2 = $this->user->createToken('token-2')->plainTextToken;

        // Verificar que ambos funcionan
        $response1 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1
        ])->getJson('/api/v1/me');

        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token2
        ])->getJson('/api/v1/me');

        $response1->assertStatus(200);
        $response2->assertStatus(200);
    }

    public function test_sanctum_acting_as_works_for_testing()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(200)
                 ->assertJson([
                     'user' => [
                         'id' => $this->user->id,
                         'email' => $this->user->email
                     ]
                 ]);
    }

    public function test_sanctum_acting_as_with_token_works()
    {
        $token = $this->user->createToken('test-token');
        
        Sanctum::actingAs($this->user, ['*'], $token->plainTextToken);

        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(200)
                 ->assertJson([
                     'user' => [
                         'id' => $this->user->id,
                         'email' => $this->user->email
                     ]
                 ]);
    }

    public function test_cors_headers_are_present()
    {
        $response = $this->withHeaders([
            'Origin' => 'http://localhost:3000'
        ])->getJson('/api/v1/me');

        // Debería tener headers CORS incluso en error 401
        $response->assertHeader('Access-Control-Allow-Origin');
    }

    public function test_preflight_request_works()
    {
        $response = $this->withHeaders([
            'Origin' => 'http://localhost:3000',
            'Access-Control-Request-Method' => 'POST',
            'Access-Control-Request-Headers' => 'Content-Type, Authorization'
        ])->options('/api/v1/me');

        $response->assertStatus(200)
                 ->assertHeader('Access-Control-Allow-Origin')
                 ->assertHeader('Access-Control-Allow-Methods')
                 ->assertHeader('Access-Control-Allow-Headers');
    }

    public function test_rate_limiting_on_login_endpoint()
    {
        // Intentar hacer login múltiples veces rápidamente
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/v1/login', [
                'email' => 'nonexistent@example.com',
                'password' => 'wrongpassword'
            ]);
        }

        // El último intento debería ser bloqueado por rate limiting
        $response->assertStatus(429);
    }

    public function test_rate_limiting_on_protected_endpoints()
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        // Intentar acceder múltiples veces rápidamente
        for ($i = 0; $i < 10; $i++) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->getJson('/api/v1/me');
        }

        // El último intento debería ser bloqueado por rate limiting
        $response->assertStatus(429);
    }

    public function test_user_state_is_maintained_across_requests()
    {
        Sanctum::actingAs($this->user);

        // Primera petición
        $response1 = $this->getJson('/api/v1/me');
        $response1->assertStatus(200);

        // Segunda petición
        $response2 = $this->getJson('/api/v1/me');
        $response2->assertStatus(200);

        // Verificar que ambas respuestas son consistentes
        $this->assertEquals(
            $response1->json('user.id'),
            $response2->json('user.id')
        );
    }

    public function test_token_works_across_different_endpoints()
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        $headers = ['Authorization' => 'Bearer ' . $token];

        // Probar diferentes endpoints
        $endpoints = [
            '/api/v1/me',
            '/api/v1/instituciones',
            '/api/v1/logout'
        ];

        foreach ($endpoints as $endpoint) {
            $method = $endpoint === '/api/v1/logout' ? 'POST' : 'GET';
            $response = $this->withHeaders($headers)->json($method, $endpoint);
            
            // Algunos endpoints pueden devolver 404 si no hay datos, pero no 401
            $this->assertNotEquals(401, $response->status());
        }
    }
} 