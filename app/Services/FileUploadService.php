<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FileUploadService
{
    /**
     * Tipos de archivos permitidos
     */
    const ALLOWED_IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    const ALLOWED_DOCUMENT_TYPES = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
    const ALLOWED_ALL_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];

    /**
     * Tamaños máximos de archivo (en KB)
     */
    const MAX_IMAGE_SIZE = 5120; // 5MB en KB
    const MAX_DOCUMENT_SIZE = 10240; // 10MB en KB

    /**
     * Subir una imagen con optimización
     */
    public function uploadImage(UploadedFile $file, string $path, array $options = []): array
    {
        $this->validateFile($file, self::ALLOWED_IMAGE_TYPES, self::MAX_IMAGE_SIZE);

        // Asegurar que el directorio existe
        $this->ensureDirectoryExists($path);

        $filename = $this->generateFilename($file);
        $fullPath = $path . '/' . $filename;

        // Procesar imagen si es necesario
        if (isset($options['resize']) && $options['resize']) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            
            if (isset($options['width']) && isset($options['height'])) {
                $image->resize($options['width'], $options['height']);
            }

            // Guardar usando el storage de Laravel
            $tempPath = 'temp/' . $filename;
            if (isset($options['quality'])) {
                $image->save(Storage::disk('public')->path($tempPath), $options['quality']);
            } else {
                $image->save(Storage::disk('public')->path($tempPath), 85);
            }
            
            // Mover al destino final
            Storage::disk('public')->move($tempPath, $fullPath);
        } else {
            // Guardar archivo sin procesar
            Storage::disk('public')->putFileAs($path, $file, $filename);
        }

        return [
            'filename' => $filename,
            'path' => $fullPath,
            'url' => Storage::disk('public')->url($fullPath),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    /**
     * Subir un documento
     */
    public function uploadDocument(UploadedFile $file, string $path): array
    {
        $this->validateFile($file, self::ALLOWED_DOCUMENT_TYPES, self::MAX_DOCUMENT_SIZE);

        // Asegurar que el directorio existe
        $this->ensureDirectoryExists($path);

        $filename = $this->generateFilename($file);
        $fullPath = $path . '/' . $filename;

        Storage::disk('public')->putFileAs($path, $file, $filename);

        return [
            'filename' => $filename,
            'path' => $fullPath,
            'url' => Storage::disk('public')->url($fullPath),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    /**
     * Subir cualquier tipo de archivo
     */
    public function uploadFile(UploadedFile $file, string $path): array
    {
        $this->validateFile($file, self::ALLOWED_ALL_TYPES, self::MAX_DOCUMENT_SIZE);

        // Asegurar que el directorio existe
        $this->ensureDirectoryExists($path);

        $filename = $this->generateFilename($file);
        $fullPath = $path . '/' . $filename;

        Storage::disk('public')->putFileAs($path, $file, $filename);

        return [
            'filename' => $filename,
            'path' => $fullPath,
            'url' => Storage::disk('public')->url($fullPath),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    /**
     * Eliminar archivo
     */
    public function deleteFile(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    /**
     * Obtener URL del archivo
     */
    public function getFileUrl(string $path): ?string
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }
        return null;
    }

    /**
     * Validar archivo
     */
    private function validateFile(UploadedFile $file, array $allowedTypes, int $maxSize): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedTypes)) {
            throw new \InvalidArgumentException(
                'Tipo de archivo no permitido. Tipos permitidos: ' . implode(', ', $allowedTypes)
            );
        }

        if ($file->getSize() > ($maxSize * 1024)) {
            throw new \InvalidArgumentException(
                'El archivo es demasiado grande. Tamaño máximo: ' . $maxSize . 'KB'
            );
        }
    }

    /**
     * Generar nombre único para el archivo
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitizedName = Str::slug($originalName);
        
        return $sanitizedName . '_' . time() . '_' . Str::random(8) . '.' . $extension;
    }

    /**
     * Crear directorio si no existe
     */
    public function ensureDirectoryExists(string $path): void
    {
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path);
        }
    }

    /**
     * Obtener información del archivo
     */
    public function getFileInfo(string $path): ?array
    {
        if (Storage::disk('public')->exists($path)) {
            return [
                'size' => Storage::disk('public')->size($path),
                'last_modified' => Storage::disk('public')->lastModified($path),
                'mime_type' => Storage::disk('public')->mimeType($path),
            ];
        }
        return null;
    }

    /**
     * Mover archivo de temporal a ubicación final
     */
    public function moveFromTemp(string $tempPath, string $finalPath): bool
    {
        if (Storage::disk('public')->exists($tempPath)) {
            return Storage::disk('public')->move($tempPath, $finalPath);
        }
        return false;
    }

    /**
     * Limpiar archivos temporales
     */
    public function cleanTempFiles(int $hoursOld = 24): int
    {
        $tempPath = 'temp';
        $files = Storage::disk('public')->files($tempPath);
        $deletedCount = 0;

        foreach ($files as $file) {
            $lastModified = Storage::disk('public')->lastModified($file);
            $hoursDiff = (time() - $lastModified) / 3600;

            if ($hoursDiff > $hoursOld) {
                if (Storage::disk('public')->delete($file)) {
                    $deletedCount++;
                }
            }
        }

        return $deletedCount;
    }
} 