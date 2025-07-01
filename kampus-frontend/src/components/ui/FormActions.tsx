import React from 'react';
import { cn } from '../../utils/cn';

interface FormActionsProps {
  onCancel: () => void;
  onSubmit: () => void;
  loading?: boolean;
  submitText?: string;
  cancelText?: string;
  className?: string;
}

const FormActions: React.FC<FormActionsProps> = ({
  onCancel,
  onSubmit,
  loading = false,
  submitText = 'Guardar',
  cancelText = 'Cancelar',
  className,
}) => {
  return (
    <div className={cn("flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3", className)}>
      <button
        type="button"
        onClick={onCancel}
        disabled={loading}
        className="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 w-full sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {cancelText}
      </button>
      <button
        type="submit"
        disabled={loading}
        className="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 w-full sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {loading ? (
          <>
            <svg className="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
              <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Guardando...
          </>
        ) : (
          submitText
        )}
      </button>
    </div>
  );
};

export default FormActions; 