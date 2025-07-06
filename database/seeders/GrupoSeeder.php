<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grupo;
use App\Models\Grado;
use App\Models\Sede;

class GrupoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeder de grupos...');

        $grados = Grado::with('institucion.sedes')->get();
        $this->command->info("📊 Procesando {$grados->count()} grados...");

        $gruposCreados = 0;

        foreach ($grados as $grado) {
            $this->command->info("\n📚 Procesando: {$grado->nombre} - {$grado->institucion->nombre}");
            
            // Obtener las sedes de la institución
            $sedes = $grado->institucion->sedes;
            
            if ($sedes->isEmpty()) {
                $this->command->warn("  ⚠️  No hay sedes para la institución {$grado->institucion->nombre}");
                continue;
            }

            // Crear 2 grupos por grado
            for ($i = 1; $i <= 2; $i++) {
                // Asignar grupo a una sede (rotar entre las sedes disponibles)
                $sede = $sedes[$i % $sedes->count()];
                
                // Obtener el primer año disponible de la institución
                $anio = \App\Models\Anio::where('institucion_id', $grado->institucion_id)->first();
                
                if (!$anio) {
                    $this->command->warn("  ⚠️  No hay años disponibles para la institución {$grado->institucion->nombre}");
                    continue;
                }
                
                $grupo = Grupo::create([
                    'nombre' => "Grupo {$i}",
                    'descripcion' => "Grupo {$i} del {$grado->nombre}",
                    'capacidad' => 35,
                    'grado_id' => $grado->id,
                    'sede_id' => $sede->id,
                    'institucion_id' => $grado->institucion_id,
                    'anio_id' => $anio->id
                ]);
                
                $gruposCreados++;
                $this->command->info("  ✅ Grupo {$i}: {$grupo->nombre} - Sede: {$sede->nombre} - Año: {$anio->anio}");
            }
        }

        $this->command->info("\n🎉 ¡Seeder de grupos completado!");
        $this->command->info("📊 Total grupos creados: {$gruposCreados}");
        
        // Estadísticas por institución
        $this->command->info("\n📋 Estadísticas por institución:");
        $instituciones = \App\Models\Institucion::withCount('grupos')->get();
        foreach ($instituciones as $institucion) {
            $this->command->info("  🏫 {$institucion->nombre}: {$institucion->grupos_count} grupos");
        }
    }
} 