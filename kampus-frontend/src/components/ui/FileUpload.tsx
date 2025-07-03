import React, { useState, useRef } from 'react';
import { Button } from './Button';

interface FileUploadProps {
  label: string;
  name: string;
  accept?: string;
  multiple?: boolean;
  maxSize?: number; // en MB
  onFileSelect: (files: File[]) => void;
  onFileRemove?: (file: File) => void;
  selectedFiles?: File[];
  error?: string;
  disabled?: boolean;
  className?: string;
  preview?: boolean;
}

export const FileUpload: React.FC<FileUploadProps> = ({
  label,
  name,
  accept = '*/*',
  multiple = false,
  maxSize = 10, // 10MB por defecto
  onFileSelect,
  onFileRemove,
  selectedFiles = [],
  error,
  disabled = false,
  className = '',
  preview = true,
}) => {
  const [dragActive, setDragActive] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const handleDrag = (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    if (e.type === 'dragenter' || e.type === 'dragover') {
      setDragActive(true);
    } else if (e.type === 'dragleave') {
      setDragActive(false);
    }
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setDragActive(false);

    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      handleFiles(Array.from(e.dataTransfer.files));
    }
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    e.preventDefault();
    if (e.target.files && e.target.files[0]) {
      handleFiles(Array.from(e.target.files));
    }
  };

  const handleFiles = (files: File[]) => {
    const validFiles: File[] = [];
    const errors: string[] = [];

    files.forEach((file) => {
      // Validar tamaño
      if (file.size > maxSize * 1024 * 1024) {
        errors.push(`${file.name} es demasiado grande. Máximo ${maxSize}MB`);
        return;
      }

      // Validar tipo de archivo
      if (accept !== '*/*') {
        const acceptedTypes = accept.split(',').map(type => type.trim());
        const fileType = file.type;
        const fileExtension = '.' + file.name.split('.').pop()?.toLowerCase();

        const isAccepted = acceptedTypes.some(type => {
          if (type.startsWith('.')) {
            return fileExtension === type;
          }
          return fileType === type || fileType.startsWith(type.replace('*', ''));
        });

        if (!isAccepted) {
          errors.push(`${file.name} no es un tipo de archivo permitido`);
          return;
        }
      }

      validFiles.push(file);
    });

    if (errors.length > 0) {
      console.error('Errores de validación:', errors);
      // Aquí podrías mostrar las alertas de error
    }

    if (validFiles.length > 0) {
      onFileSelect(validFiles);
    }
  };

  const removeFile = (file: File) => {
    if (onFileRemove) {
      onFileRemove(file);
    }
  };

  const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  };

  const getFileIcon = (file: File): string => {
    const type = file.type;
    if (type.startsWith('image/')) return '🖼️';
    if (type.includes('pdf')) return '📄';
    if (type.includes('word') || type.includes('document')) return '📝';
    if (type.includes('excel') || type.includes('spreadsheet')) return '📊';
    if (type.includes('text')) return '📃';
    return '📎';
  };

  return (
    <div className={`space-y-2 ${className}`}>
      <label className="block text-sm font-medium text-gray-700">
        {label}
      </label>

      <div
        className={`relative border-2 border-dashed rounded-lg p-6 text-center transition-colors ${
          dragActive
            ? 'border-primary-500 bg-primary-50'
            : error
            ? 'border-red-300 bg-red-50'
            : 'border-gray-300 bg-gray-50 hover:border-gray-400'
        } ${disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'}`}
        onDragEnter={handleDrag}
        onDragLeave={handleDrag}
        onDragOver={handleDrag}
        onDrop={handleDrop}
        onClick={() => !disabled && fileInputRef.current?.click()}
      >
        <input
          ref={fileInputRef}
          type="file"
          name={name}
          accept={accept}
          multiple={multiple}
          onChange={handleChange}
          disabled={disabled}
          className="hidden"
        />

        <div className="space-y-2">
          <div className="text-4xl">📁</div>
          <div className="text-sm text-gray-600">
            <span className="font-medium text-primary-600 hover:text-primary-500">
              Haz clic para seleccionar
            </span>{' '}
            o arrastra y suelta
          </div>
          <div className="text-xs text-gray-500">
            {accept !== '*/*' && `Tipos permitidos: ${accept}`}
            <br />
            Tamaño máximo: {maxSize}MB
          </div>
        </div>
      </div>

      {error && (
        <p className="text-sm text-red-600">{error}</p>
      )}

      {/* Vista previa de archivos seleccionados */}
      {preview && selectedFiles.length > 0 && (
        <div className="space-y-2">
          <h4 className="text-sm font-medium text-gray-700">
            Archivos seleccionados ({selectedFiles.length})
          </h4>
          <div className="space-y-2">
            {selectedFiles.map((file, index) => (
              <div
                key={index}
                className="flex items-center justify-between p-3 bg-gray-50 rounded-lg border"
              >
                <div className="flex items-center space-x-3">
                  <span className="text-lg">{getFileIcon(file)}</span>
                  <div>
                    <p className="text-sm font-medium text-gray-900">
                      {file.name}
                    </p>
                    <p className="text-xs text-gray-500">
                      {formatFileSize(file.size)}
                    </p>
                  </div>
                </div>
                {onFileRemove && (
                  <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    onClick={() => removeFile(file)}
                    className="text-red-600 hover:text-red-700"
                  >
                    ✕
                  </Button>
                )}
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}; 