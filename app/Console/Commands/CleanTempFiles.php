<?php

namespace App\Console\Commands;

use App\Services\FileUploadService;
use Illuminate\Console\Command;

class CleanTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:clean-temp {--hours=24 : Horas de antigüedad para considerar archivos como temporales}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia archivos temporales del storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $fileService = app(FileUploadService::class);

        $this->info("Limpiando archivos temporales con más de {$hours} horas de antigüedad...");

        try {
            $deletedCount = $fileService->cleanTempFiles($hours);

            if ($deletedCount > 0) {
                $this->info("✅ Se eliminaron {$deletedCount} archivos temporales.");
            } else {
                $this->info("ℹ️  No se encontraron archivos temporales para eliminar.");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Error al limpiar archivos temporales: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
