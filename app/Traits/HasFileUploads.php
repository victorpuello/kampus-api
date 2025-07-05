<?php

namespace App\Traits;

use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasFileUploads
{
    /**
     * Campos de archivo que maneja el modelo
     */
    protected $fileFields = [];

    /**
     * Rutas base para cada tipo de archivo
     */
    protected $filePaths = [];

    /**
     * Inicializa las propiedades del trait para evitar problemas de null/undefined.
     * Debe llamarse en el constructor y en los eventos del modelo.
     */
    public function initializeHasFileUploads()
    {
        if (!is_array($this->fileFields)) {
            $this->fileFields = [];
        }
        if (!is_array($this->filePaths)) {
            $this->filePaths = [];
        }
    }

    /**
     * Subir archivo y actualizar modelo
     */
    public function uploadFile(UploadedFile $file, string $field, array $options = []): bool
    {
        if (!in_array($field, $this->fileFields)) {
            throw new \InvalidArgumentException("El campo '{$field}' no está configurado para archivos");
        }

        $fileService = app(FileUploadService::class);
        $path = $this->getFilePath($field);

        try {
            // Eliminar archivo anterior si existe
            if ($this->$field) {
                $this->deleteFile($field);
            }

            // Determinar tipo de archivo y subir
            $fileInfo = $this->determineFileTypeAndUpload($file, $path, $options);

            // Actualizar modelo
            $this->update([$field => $fileInfo['path']]);

            return true;
        } catch (\Exception $e) {
            \Log::error("Error uploading file for field {$field}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar archivo del campo especificado
     */
    public function deleteFile(string $field): bool
    {
        if (!in_array($field, $this->fileFields)) {
            return false;
        }

        if ($this->$field && Storage::disk('public')->exists($this->$field)) {
            $fileService = app(FileUploadService::class);
            return $fileService->deleteFile($this->$field);
        }

        return false;
    }

    /**
     * Obtener URL del archivo
     */
    public function getFileUrl(string $field): ?string
    {
        // Si no hay valor en el campo, retornar imagen por defecto para escudos
        if (!$this->$field) {
            if ($field === 'escudo') {
                return asset('storage/instituciones/escudos/default.png');
            }
            return null;
        }

        // Si los campos no están configurados, usar el servicio directamente
        if (empty($this->fileFields) || !in_array($field, $this->fileFields)) {
            $fileService = app(FileUploadService::class);
            return $fileService->getFileUrl($this->$field);
        }

        // Verificar si el archivo existe, si no, retornar imagen por defecto para escudos
        if (!Storage::disk('public')->exists($this->$field)) {
            if ($field === 'escudo') {
                return asset('storage/instituciones/escudos/default.png');
            }
            return null;
        }

        $fileService = app(FileUploadService::class);
        return $fileService->getFileUrl($this->$field);
    }

    /**
     * Obtener información del archivo
     */
    public function getFileInfo(string $field): ?array
    {
        if (!in_array($field, $this->fileFields) || !$this->$field) {
            return null;
        }

        $fileService = app(FileUploadService::class);
        return $fileService->getFileInfo($this->$field);
    }

    /**
     * Determinar tipo de archivo y subir
     */
    protected function determineFileTypeAndUpload(UploadedFile $file, string $path, array $options = []): array
    {
        $fileService = app(FileUploadService::class);
        $mimeType = $file->getMimeType();

        // Verificar si es imagen
        if (str_starts_with($mimeType, 'image/')) {
            return $fileService->uploadImage($file, $path, $options);
        }

        // Verificar si es documento
        if (in_array($mimeType, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain'
        ])) {
            return $fileService->uploadDocument($file, $path);
        }

        // Archivo genérico
        return $fileService->uploadFile($file, $path);
    }

    /**
     * Obtener ruta del archivo para el campo especificado
     */
    protected function getFilePath(string $field): string
    {
        if (isset($this->filePaths[$field])) {
            return $this->filePaths[$field];
        }

        // Ruta por defecto basada en el nombre del modelo
        $modelName = strtolower(class_basename($this));
        return $modelName . '/' . $field;
    }

    /**
     * Boot del trait
     */
    protected static function bootHasFileUploads()
    {
        static::deleting(function ($model) {
            // Eliminar archivos cuando se elimina el modelo
            foreach ($model->fileFields as $field) {
                if ($model->$field) {
                    $model->deleteFile($field);
                }
            }
        });
    }

    /**
     * Configurar campos de archivo
     */
    public function setFileFields(array $fields): void
    {
        $this->fileFields = $fields;
        \Log::info('setFileFields llamado', [
            'fields' => $fields,
            'result' => $this->fileFields
        ]);
    }

    /**
     * Configurar rutas de archivo
     */
    public function setFilePaths(array $paths): void
    {
        $this->filePaths = $paths;
        \Log::info('setFilePaths llamado', [
            'paths' => $paths,
            'result' => $this->filePaths
        ]);
    }

    /**
     * Verificar si el campo tiene archivo
     */
    public function hasFile(string $field): bool
    {
        return in_array($field, $this->fileFields) && !empty($this->$field);
    }

    /**
     * Obtener tamaño del archivo en formato legible
     */
    public function getFileSize(string $field): ?string
    {
        $fileInfo = $this->getFileInfo($field);
        
        if (!$fileInfo) {
            return null;
        }

        $bytes = $fileInfo['size'];
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Obtener extensión del archivo
     */
    public function getFileExtension(string $field): ?string
    {
        if (!$this->hasFile($field)) {
            return null;
        }

        return pathinfo($this->$field, PATHINFO_EXTENSION);
    }

    /**
     * Verificar si el archivo es una imagen
     */
    public function isImage(string $field): bool
    {
        $extension = $this->getFileExtension($field);
        return in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * Verificar si el archivo es un documento
     */
    public function isDocument(string $field): bool
    {
        $extension = $this->getFileExtension($field);
        return in_array(strtolower($extension), ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt']);
    }
} 