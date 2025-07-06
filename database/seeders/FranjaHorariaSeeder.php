<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FranjaHoraria;
use App\Models\Institucion;

class FranjaHorariaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeder de franjas horarias...');

        $instituciones = Institucion::all();
        $this->command->info("📊 Procesando {$instituciones->count()} instituciones...");

        $franjasCreadas = 0;

        foreach ($instituciones as $institucion) {
            $this->command->info("\n🏫 Procesando: {$institucion->nombre}");
            
            // Crear 6 franjas horarias desde las 6:00 AM
            $franjasData = [
                [
                    'nombre' => 'Primera Hora',
                    'hora_inicio' => '06:00',
                    'hora_fin' => '07:00',
                    'descripcion' => 'Primera hora de clases del día'
                ],
                [
                    'nombre' => 'Segunda Hora',
                    'hora_inicio' => '07:00',
                    'hora_fin' => '08:00',
                    'descripcion' => 'Segunda hora de clases del día'
                ],
                [
                    'nombre' => 'Tercera Hora',
                    'hora_inicio' => '08:00',
                    'hora_fin' => '09:00',
                    'descripcion' => 'Tercera hora de clases del día'
                ],
                [
                    'nombre' => 'Cuarta Hora',
                    'hora_inicio' => '09:00',
                    'hora_fin' => '10:00',
                    'descripcion' => 'Cuarta hora de clases del día'
                ],
                [
                    'nombre' => 'Quinta Hora',
                    'hora_inicio' => '10:00',
                    'hora_fin' => '11:00',
                    'descripcion' => 'Quinta hora de clases del día'
                ],
                [
                    'nombre' => 'Sexta Hora',
                    'hora_inicio' => '11:00',
                    'hora_fin' => '12:00',
                    'descripcion' => 'Sexta hora de clases del día'
                ]
            ];

            foreach ($franjasData as $franjaData) {
                $franja = FranjaHoraria::create([
                    'nombre' => $franjaData['nombre'],
                    'hora_inicio' => $franjaData['hora_inicio'],
                    'hora_fin' => $franjaData['hora_fin'],
                    'descripcion' => $franjaData['descripcion'],
                    'institucion_id' => $institucion->id
                ]);
                
                $franjasCreadas++;
                $this->command->info("  ✅ {$franja->nombre}: {$franja->hora_inicio} - {$franja->hora_fin}");
            }
        }

        $this->command->info("\n🎉 ¡Seeder de franjas horarias completado!");
        $this->command->info("📊 Total franjas creadas: {$franjasCreadas}");
        
        // Estadísticas por institución
        $this->command->info("\n📋 Estadísticas por institución:");
        $instituciones = Institucion::withCount('franjasHorarias')->get();
        foreach ($instituciones as $institucion) {
            $this->command->info("  🏫 {$institucion->nombre}: {$institucion->franjas_horarias_count} franjas");
        }
    }
} 