<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anio;
use App\Models\Institucion;

class AnioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeder de años académicos...');

        $instituciones = Institucion::all();
        $this->command->info("📊 Procesando {$instituciones->count()} instituciones...");

        $aniosCreados = 0;

        foreach ($instituciones as $institucion) {
            $this->command->info("\n🏫 Procesando: {$institucion->nombre}");
            
            // Crear años académicos para los últimos 3 años y el próximo
            $anios = [
                date('Y') - 2, // Hace 2 años
                date('Y') - 1, // Año pasado
                date('Y'),     // Año actual
                date('Y') + 1  // Próximo año
            ];

            foreach ($anios as $anio) {
                $anioModel = Anio::firstOrCreate([
                    'nombre' => (string)$anio,
                    'institucion_id' => $institucion->id
                ], [
                    'fecha_inicio' => "{$anio}-01-01",
                    'fecha_fin' => "{$anio}-12-31",
                    'estado' => $anio == date('Y') ? 'activo' : 'inactivo',
                ]);
                
                $aniosCreados++;
                $this->command->info("  ✅ Año: {$anio} - {$anioModel->estado}");
            }
        }

        $this->command->info("\n🎉 ¡Seeder de años académicos completado!");
        $this->command->info("📊 Total años creados: {$aniosCreados}");
        
        // Estadísticas por institución
        $this->command->info("\n📋 Estadísticas por institución:");
        $instituciones = Institucion::withCount('anios')->get();
        foreach ($instituciones as $institucion) {
            $this->command->info("  🏫 {$institucion->nombre}: {$institucion->anios_count} años");
        }
    }
} 