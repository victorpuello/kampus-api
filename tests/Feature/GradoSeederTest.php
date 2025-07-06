<?php

namespace Tests\Feature;

use App\Models\Grado;
use App\Models\Institucion;
use Database\Seeders\GradoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradoSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear instituciones de prueba
        $this->institucionGeneral = Institucion::factory()->create([
            'nombre' => 'Instituto General',
        ]);

        $this->institucionPrimaria = Institucion::factory()->create([
            'nombre' => 'Escuela Primaria',
        ]);

        $this->institucionSecundaria = Institucion::factory()->create([
            'nombre' => 'Liceo Secundaria',
        ]);
    }

    /** @test */
    public function puede_ejecutar_seeder_con_instituciones_existentes()
    {
        $seeder = new GradoSeeder;
        $seeder->run();

        // Verificar que se crearon grados para cada institución
        $this->assertGreaterThan(0, Grado::count());

        foreach ([$this->institucionGeneral, $this->institucionPrimaria, $this->institucionSecundaria] as $institucion) {
            $gradosInstitucion = Grado::where('institucion_id', $institucion->id)->count();
            $this->assertGreaterThan(0, $gradosInstitucion, "La institución {$institucion->nombre} no tiene grados");
        }
    }

    /** @test */
    public function crea_grados_por_nivel_educativo()
    {
        $seeder = new GradoSeeder;
        $seeder->run();

        $niveles = Grado::getNivelesDisponibles();

        foreach ($niveles as $nivel) {
            $gradosPorNivel = Grado::where('nivel', $nivel)->count();
            $this->assertGreaterThan(0, $gradosPorNivel, "No hay grados para el nivel: {$nivel}");
        }
    }

    /** @test */
    public function no_crea_grados_duplicados_por_institucion()
    {
        // Ejecutar seeder dos veces
        $seeder = new GradoSeeder;
        $seeder->run();

        $gradosPrimeraEjecucion = Grado::count();

        $seeder->run();

        $gradosSegundaEjecucion = Grado::count();

        // El número de grados debe ser el mismo (no duplicados)
        $this->assertEquals($gradosPrimeraEjecucion, $gradosSegundaEjecucion);
    }

    /** @test */
    public function respeta_configuracion_por_tipo_de_institucion()
    {
        $seeder = new GradoSeeder;
        $seeder->run();

        // Verificar configuración general (todas las instituciones por defecto)
        $gradosGeneral = Grado::where('institucion_id', $this->institucionGeneral->id)->get();

        // Debe tener grados de todos los niveles
        $nivelesPresentes = $gradosGeneral->pluck('nivel')->unique()->sort()->values()->toArray();
        $nivelesEsperados = collect(Grado::getNivelesDisponibles())->sort()->values()->toArray();

        $this->assertEquals($nivelesEsperados, $nivelesPresentes);
    }

    /** @test */
    public function no_crea_grados_sin_institucion_cuando_no_hay_instituciones()
    {
        // Eliminar todas las instituciones usando delete() en lugar de truncate()
        Institucion::query()->delete();

        $seeder = new GradoSeeder;
        $seeder->run();

        // Verificar que NO se crearon grados sin institución
        $gradosSinInstitucion = Grado::whereNull('institucion_id')->count();
        $this->assertEquals(0, $gradosSinInstitucion);
        $this->assertEquals(0, Grado::count());
    }

    /** @test */
    public function crea_grados_con_nombres_especificos()
    {
        $seeder = new GradoSeeder;
        $seeder->run();

        $nombresEsperados = [
            'Prejardín', 'Jardín', 'Transición',
            'Grado 1º', 'Grado 2º', 'Grado 3º', 'Grado 4º', 'Grado 5º',
            'Grado 6º', 'Grado 7º', 'Grado 8º', 'Grado 9º',
            'Grado 10º', 'Grado 11º',
        ];

        foreach ($nombresEsperados as $nombre) {
            $grado = Grado::where('nombre', $nombre)->first();
            $this->assertNotNull($grado, "No se encontró el grado: {$nombre}");
        }
    }

    /** @test */
    public function asigna_niveles_correctos_a_los_grados()
    {
        $seeder = new GradoSeeder;
        $seeder->run();

        // Verificar asignación de niveles
        $this->assertNotNull(Grado::where('nombre', 'Prejardín')->where('nivel', Grado::NIVEL_PREESCOLAR)->first());
        $this->assertNotNull(Grado::where('nombre', 'Grado 1º')->where('nivel', Grado::NIVEL_BASICA_PRIMARIA)->first());
        $this->assertNotNull(Grado::where('nombre', 'Grado 6º')->where('nivel', Grado::NIVEL_BASICA_SECUNDARIA)->first());
        $this->assertNotNull(Grado::where('nombre', 'Grado 10º')->where('nivel', Grado::NIVEL_EDUCACION_MEDIA)->first());
    }

    /** @test */
    public function mantiene_integridad_referencial()
    {
        $seeder = new GradoSeeder;
        $seeder->run();

        // Verificar que todos los grados tienen institución_id válido
        $gradosSinInstitucion = Grado::whereNotNull('institucion_id')
            ->whereNotExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('instituciones')
                    ->whereRaw('instituciones.id = grados.institucion_id');
            })
            ->count();

        $this->assertEquals(0, $gradosSinInstitucion, 'Hay grados con institución_id inválido');
    }

    /** @test */
    public function no_crea_grados_con_datos_invalidos()
    {
        $seeder = new GradoSeeder;
        $seeder->run();

        // Verificar que no hay grados con datos vacíos o nulos
        $gradosInvalidos = Grado::whereNull('nombre')
            ->orWhere('nombre', '')
            ->orWhereNull('nivel')
            ->orWhere('nivel', '')
            ->count();

        $this->assertEquals(0, $gradosInvalidos, 'Hay grados con datos inválidos');
    }

    /** @test */
    public function distribuye_grados_equitativamente_entre_instituciones()
    {
        $seeder = new GradoSeeder;
        $seeder->run();

        $instituciones = Institucion::all();

        foreach ($instituciones as $institucion) {
            $gradosInstitucion = Grado::where('institucion_id', $institucion->id)->count();
            $this->assertGreaterThan(0, $gradosInstitucion, "La institución {$institucion->nombre} no tiene grados");
        }
    }

    /** @test */
    public function puede_ejecutarse_multiples_veces_sin_errores()
    {
        $seeder = new GradoSeeder;

        // Ejecutar múltiples veces
        for ($i = 0; $i < 3; $i++) {
            $seeder->run();
        }

        // Verificar que no hay errores y los datos están correctos
        $this->assertGreaterThan(0, Grado::count());

        // Verificar que no hay duplicados
        $duplicados = \DB::table('grados')
            ->select('institucion_id', 'nombre', \DB::raw('count(*) as total'))
            ->groupBy('institucion_id', 'nombre')
            ->having('total', '>', 1)
            ->get();

        $this->assertCount(0, $duplicados, 'Se encontraron grados duplicados');
    }
}
