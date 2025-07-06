<?php

namespace App\Console\Commands;

use App\Models\Grado;
use App\Models\Institucion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDefaultGrados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grados:create-default 
                            {--institucion-id= : ID específico de la institución} 
                            {--force : Forzar recreación de grados existentes}
                            {--tipo=general : Tipo de configuración (general, solo_primaria, solo_secundaria)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea los grados por defecto para todas las instituciones o una específica';

    /**
     * Configuración de grados por tipo de institución
     */
    private array $configuracionGrados = [
        'general' => [
            Grado::NIVEL_PREESCOLAR => [
                'Prejardín',
                'Jardín',
                'Transición',
            ],
            Grado::NIVEL_BASICA_PRIMARIA => [
                'Grado 1º',
                'Grado 2º',
                'Grado 3º',
                'Grado 4º',
                'Grado 5º',
            ],
            Grado::NIVEL_BASICA_SECUNDARIA => [
                'Grado 6º',
                'Grado 7º',
                'Grado 8º',
                'Grado 9º',
            ],
            Grado::NIVEL_EDUCACION_MEDIA => [
                'Grado 10º',
                'Grado 11º',
            ],
        ],
        'solo_primaria' => [
            Grado::NIVEL_PREESCOLAR => [
                'Prejardín',
                'Jardín',
                'Transición',
            ],
            Grado::NIVEL_BASICA_PRIMARIA => [
                'Grado 1º',
                'Grado 2º',
                'Grado 3º',
                'Grado 4º',
                'Grado 5º',
            ],
        ],
        'solo_secundaria' => [
            Grado::NIVEL_BASICA_SECUNDARIA => [
                'Grado 6º',
                'Grado 7º',
                'Grado 8º',
                'Grado 9º',
            ],
            Grado::NIVEL_EDUCACION_MEDIA => [
                'Grado 10º',
                'Grado 11º',
            ],
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $institucionId = $this->option('institucion-id');
        $force = $this->option('force');
        $tipoConfiguracion = $this->option('tipo');

        // Validar tipo de configuración
        if (! array_key_exists($tipoConfiguracion, $this->configuracionGrados)) {
            $this->error("❌ Tipo de configuración inválido: {$tipoConfiguracion}");
            $this->info('Tipos disponibles: '.implode(', ', array_keys($this->configuracionGrados)));

            return 1;
        }

        // Obtener instituciones
        if ($institucionId) {
            $instituciones = Institucion::where('id', $institucionId)->get();
            if ($instituciones->isEmpty()) {
                $this->error("❌ No se encontró la institución con ID: {$institucionId}");

                return 1;
            }
        } else {
            $instituciones = Institucion::all();
            if ($instituciones->isEmpty()) {
                $this->error('❌ No hay instituciones disponibles en el sistema.');

                return 1;
            }
        }

        $estadisticas = [
            'creados' => 0,
            'recreados' => 0,
            'existentes' => 0,
            'errores' => 0,
        ];

        $this->info('🚀 Iniciando creación de grados por defecto...');
        $this->info('📊 Total de instituciones a procesar: '.$instituciones->count());
        $this->info("⚙️  Tipo de configuración: {$tipoConfiguracion}");
        if ($force) {
            $this->warn('⚠️  Modo FORCE activado - se recrearán grados existentes');
        }

        // Procesar cada institución
        foreach ($instituciones as $institucion) {
            $this->procesarInstitucion($institucion, $tipoConfiguracion, $force, $estadisticas);
        }

        // Mostrar resumen final
        $this->mostrarResumenFinal($estadisticas, $tipoConfiguracion);

        return 0;
    }

    /**
     * Procesa una institución específica
     */
    private function procesarInstitucion(Institucion $institucion, string $tipoConfiguracion, bool $force, array &$estadisticas): void
    {
        $this->info("\n🏫 Procesando institución: {$institucion->nombre} (ID: {$institucion->id})");

        $configuracion = $this->configuracionGrados[$tipoConfiguracion];
        $gradosInstitucion = 0;
        $existentesInstitucion = 0;
        $recreadosInstitucion = 0;

        foreach ($configuracion as $nivel => $nombres) {
            $this->line("  📚 Nivel: {$nivel}");

            foreach ($nombres as $nombre) {
                try {
                    $resultado = $this->procesarGrado($institucion, $nombre, $nivel, $force);

                    switch ($resultado) {
                        case 'creado':
                            $gradosInstitucion++;
                            $estadisticas['creados']++;
                            $this->line("    ✅ {$nombre}");

                            break;
                        case 'recreado':
                            $recreadosInstitucion++;
                            $estadisticas['recreados']++;
                            $this->line("    🔄 {$nombre} (recreado)");

                            break;
                        case 'existente':
                            $existentesInstitucion++;
                            $estadisticas['existentes']++;
                            $this->line("    ⏭️  {$nombre} (ya existe)");

                            break;
                    }
                } catch (\Exception $e) {
                    $estadisticas['errores']++;
                    $this->error("    ❌ Error procesando {$nombre}: ".$e->getMessage());
                }
            }
        }

        $this->info("  📊 Institución completada: {$gradosInstitucion} nuevos, {$recreadosInstitucion} recreados, {$existentesInstitucion} existentes");
    }

    /**
     * Procesa un grado individual
     */
    private function procesarGrado(Institucion $institucion, string $nombre, string $nivel, bool $force): string
    {
        // Verificar si el grado ya existe (incluyendo los soft deleted)
        $gradoExistente = Grado::withTrashed()
            ->where('nombre', $nombre)
            ->where('institucion_id', $institucion->id)
            ->first();

        if ($gradoExistente) {
            if ($force) {
                $gradoExistente->delete();
                $gradoExistente->forceDelete(); // Elimina físicamente
                $this->info("    [DEBUG] Eliminado grado (forceDelete): {$nombre} ({$gradoExistente->id})");
                $nuevoGrado = Grado::create([
                    'nombre' => $nombre,
                    'nivel' => $nivel,
                    'institucion_id' => $institucion->id,
                ]);
                $this->info("    [DEBUG] Creado nuevo grado: {$nombre} ({$nuevoGrado->id})");

                return 'recreado';
            } else {
                return 'existente';
            }
        }

        // Crear nuevo grado
        $nuevoGrado = Grado::create([
            'nombre' => $nombre,
            'nivel' => $nivel,
            'institucion_id' => $institucion->id,
        ]);
        $this->info("    [DEBUG] Creado nuevo grado: {$nombre} ({$nuevoGrado->id})");

        return 'creado';
    }

    /**
     * Muestra el resumen final del proceso
     */
    private function mostrarResumenFinal(array $estadisticas, string $tipoConfiguracion): void
    {
        $this->info("\n🎉 ¡Proceso completado exitosamente!");
        $this->info('📊 Resumen final:');
        $this->info("  ✅ Grados creados: {$estadisticas['creados']}");
        $this->info("  🔄 Grados recreados: {$estadisticas['recreados']}");
        $this->info("  ⏭️  Grados existentes: {$estadisticas['existentes']}");
        $this->info("  ❌ Errores: {$estadisticas['errores']}");
        $this->info('  📚 Total procesados: '.array_sum($estadisticas));

        $this->info("\n📋 Configuración aplicada: {$tipoConfiguracion}");
        $configuracion = $this->configuracionGrados[$tipoConfiguracion];
        foreach ($configuracion as $nivel => $nombres) {
            $this->info("  {$nivel}: ".count($nombres).' grados');
        }

        // Mostrar estadísticas por nivel en la base de datos
        $this->mostrarEstadisticasPorNivel();
    }

    /**
     * Muestra estadísticas de grados por nivel
     */
    private function mostrarEstadisticasPorNivel(): void
    {
        $this->info("\n📋 Estadísticas actuales por nivel:");

        $estadisticasNivel = DB::table('grados')
            ->select('nivel', DB::raw('count(*) as total'))
            ->groupBy('nivel')
            ->get();

        if ($estadisticasNivel->isEmpty()) {
            $this->warn('  No hay grados en la base de datos');
        } else {
            foreach ($estadisticasNivel as $estadistica) {
                $this->info("  {$estadistica->nivel}: {$estadistica->total} grados");
            }
        }
    }
}
