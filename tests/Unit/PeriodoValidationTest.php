<?php

namespace Tests\Unit;

use App\Http\Requests\StorePeriodoRequest;
use App\Http\Requests\UpdatePeriodoRequest;
use App\Models\Anio;
use App\Models\Institucion;
use App\Models\Periodo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PeriodoValidationTest extends TestCase
{
    use RefreshDatabase;

    private Anio $anio;

    private Periodo $periodo;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear institución de prueba
        $institucion = Institucion::factory()->create([
            'nombre' => 'Instituto de Prueba',
            'siglas' => 'ITP',
        ]);

        // Crear año académico de prueba
        $this->anio = Anio::factory()->create([
            'nombre' => '2024-2025',
            'fecha_inicio' => '2024-01-15',
            'fecha_fin' => '2024-12-15',
            'institucion_id' => $institucion->id,
            'estado' => 'activo',
        ]);

        // Crear periodo de prueba
        $this->periodo = Periodo::factory()->create([
            'nombre' => 'Primer Periodo',
            'fecha_inicio' => '2024-01-15',
            'fecha_fin' => '2024-04-15',
            'anio_id' => $this->anio->id,
        ]);
    }

    /** @test */
    public function puede_crear_periodo_con_fechas_validas()
    {
        $data = [
            'nombre' => 'Segundo Periodo',
            'fecha_inicio' => '2024-05-01',
            'fecha_fin' => '2024-08-15',
            'anio_id' => $this->anio->id,
        ];

        $request = new StorePeriodoRequest;
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function no_puede_crear_periodo_con_fecha_inicio_antes_del_año_academico()
    {
        $data = [
            'nombre' => 'Periodo Inválido',
            'fecha_inicio' => '2023-12-01', // Antes del año académico
            'fecha_fin' => '2024-03-15',
            'anio_id' => $this->anio->id,
        ];

        $request = new StorePeriodoRequest;
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('fecha_inicio', $validator->errors()->toArray());
        $this->assertStringContainsString('no puede ser anterior', $validator->errors()->first('fecha_inicio'));
    }

    /** @test */
    public function no_puede_crear_periodo_con_fecha_fin_despues_del_año_academico()
    {
        $data = [
            'nombre' => 'Periodo Inválido',
            'fecha_inicio' => '2024-10-01',
            'fecha_fin' => '2025-01-15', // Después del año académico
            'anio_id' => $this->anio->id,
        ];

        $request = new StorePeriodoRequest;
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('fecha_fin', $validator->errors()->toArray());
        $this->assertStringContainsString('no puede ser posterior', $validator->errors()->first('fecha_fin'));
    }

    /** @test */
    public function no_puede_crear_periodo_con_fechas_solapadas()
    {
        $data = [
            'nombre' => 'Periodo Solapado',
            'fecha_inicio' => '2024-02-01', // Se solapa con el period existente
            'fecha_fin' => '2024-05-15',
            'anio_id' => $this->anio->id,
        ];

        $request = new StorePeriodoRequest;
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('fecha_inicio', $validator->errors()->toArray());
        $this->assertStringContainsString('se cruzan', $validator->errors()->first('fecha_inicio'));
    }

    /** @test */
    public function puede_actualizar_periodo_con_fechas_validas()
    {
        $data = [
            'nombre' => 'Periodo Actualizado',
            'fecha_inicio' => '2024-05-01',
            'fecha_fin' => '2024-08-15',
            'anio_id' => $this->anio->id,
        ];

        $request = new UpdatePeriodoRequest;
        $request->merge($data);
        $request->setRouteResolver(function () {
            return new class($this->periodo)
            {
                private $periodo;

                public function __construct($periodo)
                {
                    $this->periodo = $periodo;
                }

                public function parameter($name)
                {
                    return $name === 'periodo' ? $this->periodo : null;
                }
            };
        });

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function no_puede_actualizar_periodo_con_fechas_solapadas_excluyendo_el_propio()
    {
        // Crear un segundo periodo
        $segundoPeriodo = Periodo::factory()->create([
            'nombre' => 'Segundo Periodo',
            'fecha_inicio' => '2024-05-01',
            'fecha_fin' => '2024-08-15',
            'anio_id' => $this->anio->id,
        ]);

        // Intentar actualizar el primer periodo con fechas que se solapan con el segundo
        $data = [
            'nombre' => 'Periodo Actualizado',
            'fecha_inicio' => '2024-06-01', // Se solapa con el segundo periodo
            'fecha_fin' => '2024-09-15',
            'anio_id' => $this->anio->id,
        ];

        $request = new UpdatePeriodoRequest;
        $request->merge($data);
        $request->setRouteResolver(function () {
            return new class($this->periodo)
            {
                private $periodo;

                public function __construct($periodo)
                {
                    $this->periodo = $periodo;
                }

                public function parameter($name)
                {
                    return $name === 'periodo' ? $this->periodo : null;
                }
            };
        });

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('fecha_inicio', $validator->errors()->toArray());
        $this->assertStringContainsString('se cruzan', $validator->errors()->first('fecha_inicio'));
    }

    /** @test */
    public function puede_actualizar_periodo_manteniendo_sus_propias_fechas()
    {
        $data = [
            'nombre' => 'Periodo Renombrado',
            'fecha_inicio' => '2024-01-15', // Mantiene las mismas fechas
            'fecha_fin' => '2024-04-15',
            'anio_id' => $this->anio->id,
        ];

        $request = new UpdatePeriodoRequest;
        $request->merge($data);
        $request->setRouteResolver(function () {
            return new class($this->periodo)
            {
                private $periodo;

                public function __construct($periodo)
                {
                    $this->periodo = $periodo;
                }

                public function parameter($name)
                {
                    return $name === 'periodo' ? $this->periodo : null;
                }
            };
        });

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function validacion_falla_si_anio_no_existe()
    {
        $data = [
            'nombre' => 'Periodo Inválido',
            'fecha_inicio' => '2024-05-01',
            'fecha_fin' => '2024-08-15',
            'anio_id' => 99999, // ID que no existe
        ];

        $request = new StorePeriodoRequest;
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('anio_id', $validator->errors()->toArray());
    }

    /** @test */
    public function validacion_falla_si_fecha_inicio_es_mayor_que_fecha_fin()
    {
        $data = [
            'nombre' => 'Periodo Inválido',
            'fecha_inicio' => '2024-08-15',
            'fecha_fin' => '2024-05-01', // Fecha fin antes que inicio
            'anio_id' => $this->anio->id,
        ];

        $request = new StorePeriodoRequest;
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('fecha_fin', $validator->errors()->toArray());
    }

    /** @test */
    public function mensajes_de_error_son_personalizados()
    {
        $data = [
            'nombre' => 'Periodo Test',
            'fecha_inicio' => '2023-12-01', // Antes del año académico
            'fecha_fin' => '2024-03-15',
            'anio_id' => $this->anio->id,
        ];

        $request = new StorePeriodoRequest;
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $errors = $validator->errors();

        $this->assertStringContainsString('no puede ser anterior a la fecha de inicio del año académico', $errors->first('fecha_inicio'));
    }

    /** @test */
    public function validacion_permite_periodos_consecutivos()
    {
        // Crear un periodo que termina justo antes de que empiece otro
        $data = [
            'nombre' => 'Periodo Consecutivo',
            'fecha_inicio' => '2024-04-16', // Un día después del fin del periodo existente
            'fecha_fin' => '2024-07-15',
            'anio_id' => $this->anio->id,
        ];

        $request = new StorePeriodoRequest;
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->passes());
    }
}
