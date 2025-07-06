<?php

namespace Tests\Unit;

use App\Http\Middleware\CheckPermission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class CheckPermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_permite_acceso_en_entorno_local()
    {
        Config::set('app.env', 'local');
        $middleware = new CheckPermission;
        $request = Request::create('/fake', 'GET');
        $called = false;
        $next = function ($req) use (&$called) {
            $called = true;

            return response('ok', 200);
        };
        $response = $middleware->handle($request, $next, 'ver_asignaciones');
        $this->assertTrue($called);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_bloquea_si_no_hay_usuario()
    {
        Config::set('app.env', 'testing');
        Auth::shouldReceive('user')->andReturn(null);
        $middleware = new CheckPermission;
        $request = Request::create('/fake', 'GET');
        $next = function ($req) {
            return response('ok', 200);
        };
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Usuario no autenticado');
        $middleware->handle($request, $next, 'ver_asignaciones');
    }

    public function test_bloquea_si_usuario_sin_permiso()
    {
        Config::set('app.env', 'testing');
        $user = User::factory()->create();
        Auth::shouldReceive('user')->andReturn($user);
        $middleware = new CheckPermission;
        $request = Request::create('/fake', 'GET');
        $next = function ($req) {
            return response('ok', 200);
        };
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('No tienes permisos para ver_asignaciones');
        $middleware->handle($request, $next, 'ver_asignaciones');
    }

    public function test_permite_si_usuario_tiene_permiso()
    {
        Config::set('app.env', 'testing');
        $user = User::factory()->create();
        // Crear un mock del usuario con el método hasPermissionTo sobrescrito
        $userMock = Mockery::mock($user)->makePartial();
        $userMock->shouldReceive('hasPermissionTo')
            ->with('ver_asignaciones')
            ->andReturn(true);
        Auth::shouldReceive('user')->andReturn($userMock);
        $middleware = new CheckPermission;
        $request = Request::create('/fake', 'GET');
        $called = false;
        $next = function ($req) use (&$called) {
            $called = true;

            return response('ok', 200);
        };
        $response = $middleware->handle($request, $next, 'ver_asignaciones');
        $this->assertTrue($called);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
