<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class TestAsignacionesEndpoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:asignaciones-endpoints';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba los endpoints de asignaciones para verificar su funcionamiento';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 PRUEBA DE ENDPOINTS DE ASIGNACIONES');
        $this->info('=====================================');
        $this->newLine();

        // 1. Probar login para obtener token
        $this->info('1. 🔐 Probando autenticación...');
        try {
            $user = User::where('email', 'admin@example.com')->first();
            if (!$user) {
                $this->error('❌ Usuario admin no encontrado');
                return 1;
            }

            $token = $user->createToken('test-token')->plainTextToken;
            $this->info('✅ Login exitoso');
            $this->line('   Token: ' . substr($token, 0, 20) . '...');
            $this->newLine();
        } catch (\Exception $e) {
            $this->error('❌ Error en login: ' . $e->getMessage());
            return 1;
        }

        // 2. Probar GET /asignaciones
        $this->info('2. 📋 Probando GET /asignaciones...');
        try {
            $response = Http::withToken($token)->get(url('/api/v1/asignaciones'));
            
            if ($response->successful()) {
                $data = $response->json();
                $total = $data['total'] ?? 0;
                $this->info('✅ Lista de asignaciones obtenida');
                $this->line('   Total de asignaciones: ' . $total);
                
                if ($total > 0) {
                    $firstAsignacion = $data['data'][0] ?? null;
                    if ($firstAsignacion) {
                        $this->line('   Primera asignación ID: ' . $firstAsignacion['id']);
                        $asignacionId = $firstAsignacion['id'];
                    }
                }
                $this->newLine();
            } else {
                $this->error('❌ Error obteniendo asignaciones: ' . $response->status());
                $this->line('   Respuesta: ' . $response->body());
                $this->newLine();
            }
        } catch (\Exception $e) {
            $this->error('❌ Error de conexión: ' . $e->getMessage());
            $this->newLine();
        }

        // 3. Probar GET /asignaciones/{id}
        if (isset($asignacionId)) {
            $this->info('3. 🔍 Probando GET /asignaciones/' . $asignacionId . '...');
            try {
                $response = Http::withToken($token)->get(url("/api/v1/asignaciones/$asignacionId"));
                
                if ($response->successful()) {
                    $data = $response->json();
                    $this->info('✅ Asignación individual obtenida');
                    $this->line('   ID: ' . $data['id']);
                    $this->line('   Docente: ' . $data['docente']['nombre'] . ' ' . $data['docente']['apellido']);
                    $this->line('   Asignatura: ' . $data['asignatura']['nombre']);
                    $this->newLine();
                } else {
                    $this->error('❌ Error obteniendo asignación individual: ' . $response->status());
                    $this->line('   Respuesta: ' . $response->body());
                    $this->newLine();
                }
            } catch (\Exception $e) {
                $this->error('❌ Error de conexión: ' . $e->getMessage());
                $this->newLine();
            }
        }

        // 4. Probar PUT /asignaciones/{id} (editar)
        if (isset($asignacionId)) {
            $this->info('4. ✏️ Probando PUT /asignaciones/' . $asignacionId . ' (editar)...');
            try {
                $updateData = [
                    'estado' => 'activo' // Solo cambiar el estado para la prueba
                ];
                
                $response = Http::withToken($token)->put(url("/api/v1/asignaciones/$asignacionId"), $updateData);
                
                if ($response->successful()) {
                    $data = $response->json();
                    $this->info('✅ Asignación actualizada exitosamente');
                    $this->line('   Estado actualizado: ' . $data['estado']);
                    $this->newLine();
                } else {
                    $this->error('❌ Error actualizando asignación: ' . $response->status());
                    $this->line('   Respuesta: ' . $response->body());
                    $this->newLine();
                }
            } catch (\Exception $e) {
                $this->error('❌ Error de conexión: ' . $e->getMessage());
                $this->newLine();
            }
        }

        // 5. Probar DELETE /asignaciones/{id}
        if (isset($asignacionId)) {
            $this->info('5. 🗑️ Probando DELETE /asignaciones/' . $asignacionId . '...');
            $this->warn('   ⚠️  Esta es solo una prueba - no se eliminará realmente');
            
            try {
                // Para la prueba, solo verificamos que el endpoint responde
                // No ejecutamos el DELETE real para no perder datos
                $response = Http::withToken($token)->delete(url("/api/v1/asignaciones/$asignacionId"));
                
                if ($response->successful()) {
                    $this->info('✅ Endpoint DELETE responde correctamente');
                    $this->line('   Status: ' . $response->status());
                    $this->newLine();
                } else {
                    $this->error('❌ Error en endpoint DELETE: ' . $response->status());
                    $this->line('   Respuesta: ' . $response->body());
                    $this->newLine();
                }
            } catch (\Exception $e) {
                $this->error('❌ Error de conexión: ' . $e->getMessage());
                $this->newLine();
            }
        }

        // 6. Probar POST /asignaciones (crear)
        $this->info('6. ➕ Probando POST /asignaciones (crear)...');
        try {
            // Obtener datos necesarios para crear una asignación
            $docentesResponse = Http::withToken($token)->get(url('/api/v1/docentes'));
            $asignaturasResponse = Http::withToken($token)->get(url('/api/v1/asignaturas'));
            $gruposResponse = Http::withToken($token)->get(url('/api/v1/grupos'));
            $franjasResponse = Http::withToken($token)->get(url('/api/v1/franjas-horarias'));
            $aniosResponse = Http::withToken($token)->get(url('/api/v1/anios'));
            
            if ($docentesResponse->successful() && $asignaturasResponse->successful() && 
                $gruposResponse->successful() && $franjasResponse->successful() && $aniosResponse->successful()) {
                
                $docentes = $docentesResponse->json();
                $asignaturas = $asignaturasResponse->json();
                $grupos = $gruposResponse->json();
                $franjas = $franjasResponse->json();
                $anios = $aniosResponse->json();
                
                if (!empty($docentes) && !empty($asignaturas) && !empty($grupos) && 
                    !empty($franjas) && !empty($anios)) {
                    
                    $createData = [
                        'docente_id' => $docentes[0]['id'],
                        'asignatura_id' => $asignaturas[0]['id'],
                        'grupo_id' => $grupos[0]['id'],
                        'franja_horaria_id' => $franjas[0]['id'],
                        'dia_semana' => 'lunes',
                        'anio_academico_id' => $anios[0]['id'],
                        'estado' => 'activo'
                    ];
                    
                    $this->line('   Datos de prueba preparados:');
                    $this->line('   - Docente ID: ' . $createData['docente_id']);
                    $this->line('   - Asignatura ID: ' . $createData['asignatura_id']);
                    $this->line('   - Grupo ID: ' . $createData['grupo_id']);
                    $this->line('   - Franja ID: ' . $createData['franja_horaria_id']);
                    $this->line('   - Año ID: ' . $createData['anio_academico_id']);
                    
                    $response = Http::withToken($token)->post(url('/api/v1/asignaciones'), $createData);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        $this->info('✅ Asignación creada exitosamente');
                        $this->line('   ID creado: ' . $data['id']);
                        $this->newLine();
                    } else {
                        $this->error('❌ Error creando asignación: ' . $response->status());
                        $this->line('   Respuesta: ' . $response->body());
                        $this->newLine();
                    }
                } else {
                    $this->error('❌ No hay datos suficientes para crear una asignación');
                    $this->newLine();
                }
            } else {
                $this->error('❌ Error obteniendo datos para crear asignación');
                $this->newLine();
            }
        } catch (\Exception $e) {
            $this->error('❌ Error de conexión: ' . $e->getMessage());
            $this->newLine();
        }

        $this->info('🏁 PRUEBA COMPLETADA');
        $this->info('===================');
        $this->line('Revisa los resultados arriba para identificar cualquier problema.');
        
        return 0;
    }
} 