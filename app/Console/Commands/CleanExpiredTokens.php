<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:clean {--days=7 : Número de días para considerar un token como expirado}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia tokens de acceso expirados o muy antiguos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        
        $this->info("🧹 Limpiando tokens expirados (más de {$days} días)...");
        
        // Contar tokens antes de la limpieza
        $totalTokens = DB::table('personal_access_tokens')->count();
        $this->info("Total de tokens antes de la limpieza: {$totalTokens}");
        
        // Eliminar tokens expirados
        $expiredTokens = DB::table('personal_access_tokens')
            ->where('expires_at', '<', now())
            ->delete();
            
        $this->info("Tokens expirados eliminados: {$expiredTokens}");
        
        // Eliminar tokens muy antiguos (sin expiración pero muy viejos)
        $oldTokens = DB::table('personal_access_tokens')
            ->whereNull('expires_at')
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
            
        $this->info("Tokens antiguos eliminados: {$oldTokens}");
        
        // Contar tokens después de la limpieza
        $remainingTokens = DB::table('personal_access_tokens')->count();
        $this->info("Total de tokens después de la limpieza: {$remainingTokens}");
        
        $totalDeleted = $expiredTokens + $oldTokens;
        $this->info("✅ Limpieza completada. Se eliminaron {$totalDeleted} tokens.");
        
        return 0;
    }
} 