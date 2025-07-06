<?php

namespace Database\Seeders;

use App\Models\Grado;
use App\Models\Institucion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradoSeeder extends Seeder
{
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
     * Run the database seeds.
     */
    public function run(): void
    {
        if (isset($this->command)) {
            $this->command->info('🌱 Iniciando seeder de grados...');
        } else {
            echo "🌱 Iniciando seeder de grados...\n";
        }

        $instituciones = Institucion::all();
        if ($instituciones->isEmpty()) {
            if (isset($this->command)) {
                $this->command->warn('⚠️  No hay instituciones disponibles. Creando grados sin institución.');
            } else {
                echo "⚠️  No hay instituciones disponibles. Creando grados sin institución.\n";
            }
            $this->crearGradosSinInstitucion();

            return;
        }

        if (isset($this->command)) {
            $this->command->info("📊 Procesando {$instituciones->count()} instituciones...");
        } else {
            echo "📊 Procesando {$instituciones->count()} instituciones...\n";
        }

        $estadisticas = [
            'creados' => 0,
            'existentes' => 0,
            'errores' => 0,
        ];

        foreach ($instituciones as $institucion) {
            $this->procesarInstitucion($institucion, $estadisticas);
        }

        $this->mostrarResumenFinal($estadisticas);
    }

    /**
     * Procesa una institución específica
     */
    private function procesarInstitucion(Institucion $institucion, array &$estadisticas): void
    {
        if (isset($this->command)) {
            $this->command->info("\n🏫 Procesando: {$institucion->nombre} (ID: {$institucion->id})");
        } else {
            echo "\n🏫 Procesando: {$institucion->nombre} (ID: {$institucion->id})\n";
        }

        $configuracion = $this->determinarConfiguracion($institucion);
        $gradosInstitucion = 0;
        $existentesInstitucion = 0;

        foreach ($configuracion as $nivel => $nombres) {
            if (isset($this->command)) {
                $this->command->line("  📚 Nivel: {$nivel}");
            } else {
                echo "  📚 Nivel: {$nivel}\n";
            }
            foreach ($nombres as $nombre) {
                try {
                    $resultado = $this->crearGradoSiNoExiste($institucion, $nombre, $nivel);
                    if ($resultado === 'creado') {
                        $gradosInstitucion++;
                        $estadisticas['creados']++;
                        if (isset($this->command)) {
                            $this->command->line("    ✅ {$nombre}");
                        } else {
                            echo "    ✅ {$nombre}\n";
                        }
                    } elseif ($resultado === 'existente') {
                        $existentesInstitucion++;
                        $estadisticas['existentes']++;
                        if (isset($this->command)) {
                            $this->command->line("    ⏭️  {$nombre} (ya existe)");
                        } else {
                            echo "    ⏭️  {$nombre} (ya existe)\n";
                        }
                    }
                } catch (\Exception $e) {
                    $estadisticas['errores']++;
                    if (isset($this->command)) {
                        $this->command->error("    ❌ Error creando {$nombre}: ".$e->getMessage());
                    } else {
                        echo "    ❌ Error creando {$nombre}: ".$e->getMessage()."\n";
                    }
                }
            }
        }
        if (isset($this->command)) {
            $this->command->info("  📊 Institución completada: {$gradosInstitucion} nuevos, {$existentesInstitucion} existentes");
        } else {
            echo "  📊 Institución completada: {$gradosInstitucion} nuevos, {$existentesInstitucion} existentes\n";
        }
    }

    /**
     * Determina la configuración de grados según el tipo de institución
     */
    private function determinarConfiguracion(Institucion $institucion): array
    {
        // Por defecto usar configuración general
        $tipoConfiguracion = 'general';

        // Aquí podrías agregar lógica para determinar el tipo según características de la institución
        // Por ejemplo, basado en el nombre, tipo, o configuración específica

        // Ejemplo de lógica (puedes personalizar según tus necesidades):
        $nombre = strtolower($institucion->nombre);

        if (str_contains($nombre, 'primaria') || str_contains($nombre, 'básica')) {
            $tipoConfiguracion = 'solo_primaria';
        } elseif (str_contains($nombre, 'secundaria') || str_contains($nombre, 'media')) {
            $tipoConfiguracion = 'solo_secundaria';
        }

        return $this->configuracionGrados[$tipoConfiguracion];
    }

    /**
     * Crea un grado si no existe
     */
    private function crearGradoSiNoExiste(Institucion $institucion, string $nombre, string $nivel): string
    {
        // Verificar si ya existe
        $gradoExistente = Grado::where('nombre', $nombre)
            ->where('institucion_id', $institucion->id)
            ->first();

        if ($gradoExistente) {
            return 'existente';
        }

        // Crear nuevo grado
        Grado::create([
            'nombre' => $nombre,
            'nivel' => $nivel,
            'institucion_id' => $institucion->id,
        ]);

        return 'creado';
    }

    /**
     * Crea grados sin institución (para casos de prueba o desarrollo)
     */
    private function crearGradosSinInstitucion(): void
    {
        if (isset($this->command)) {
            $this->command->info('🔧 Creando grados de ejemplo sin institución...');
        } else {
            echo "🔧 Creando grados de ejemplo sin institución...\n";
        }

        $gradosEjemplo = [
            ['nombre' => 'Grado 1º', 'nivel' => Grado::NIVEL_BASICA_PRIMARIA],
            ['nombre' => 'Grado 2º', 'nivel' => Grado::NIVEL_BASICA_PRIMARIA],
            ['nombre' => 'Grado 6º', 'nivel' => Grado::NIVEL_BASICA_SECUNDARIA],
            ['nombre' => 'Grado 10º', 'nivel' => Grado::NIVEL_EDUCACION_MEDIA],
        ];

        foreach ($gradosEjemplo as $grado) {
            try {
                // Intentar crear sin institución_id
                Grado::create($grado);

                if (isset($this->command)) {
                    $this->command->line("  ✅ Creado: {$grado['nombre']} ({$grado['nivel']})");
                } else {
                    echo "  ✅ Creado: {$grado['nombre']} ({$grado['nivel']})\n";
                }
            } catch (\Exception $e) {
                if (isset($this->command)) {
                    $this->command->warn("  ⚠️  Saltando {$grado['nombre']} - requiere institución_id");
                } else {
                    echo "  ⚠️  Saltando {$grado['nombre']} - requiere institución_id\n";
                }
            }
        }
    }

    /**
     * Muestra el resumen final del proceso
     */
    private function mostrarResumenFinal(array $estadisticas): void
    {
        if (isset($this->command)) {
            $this->command->info("\n🎉 ¡Seeder de grados completado!");
            $this->command->info('📊 Resumen final:');
            $this->command->info("  ✅ Grados creados: {$estadisticas['creados']}");
            $this->command->info("  ⏭️  Grados existentes: {$estadisticas['existentes']}");
            $this->command->info("  ❌ Errores: {$estadisticas['errores']}");
            $this->command->info('  📚 Total procesados: '.array_sum($estadisticas));
        } else {
            echo "\n🎉 ¡Seeder de grados completado!\n";
            echo "📊 Resumen final:\n";
            echo "  ✅ Grados creados: {$estadisticas['creados']}\n";
            echo "  ⏭️  Grados existentes: {$estadisticas['existentes']}\n";
            echo "  ❌ Errores: {$estadisticas['errores']}\n";
            echo '  📚 Total procesados: '.array_sum($estadisticas)."\n";
        }
        $this->mostrarEstadisticasPorNivel();
    }

    /**
     * Muestra estadísticas de grados por nivel
     */
    private function mostrarEstadisticasPorNivel(): void
    {
        if (isset($this->command)) {
            $this->command->info("\n📋 Estadísticas por nivel:");
        } else {
            echo "\n📋 Estadísticas por nivel:\n";
        }
        $estadisticasNivel = DB::table('grados')
            ->select('nivel', DB::raw('count(*) as total'))
            ->groupBy('nivel')
            ->get();
        foreach ($estadisticasNivel as $estadistica) {
            if (isset($this->command)) {
                $this->command->info("  {$estadistica->nivel}: {$estadistica->total} grados");
            } else {
                echo "  {$estadistica->nivel}: {$estadistica->total} grados\n";
            }
        }
    }
}
