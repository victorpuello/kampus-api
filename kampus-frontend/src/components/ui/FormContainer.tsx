import React from 'react';
import { cn } from '../../utils/cn';

interface FormContainerProps {
  children: React.ReactNode;
  onSubmit: (e: React.FormEvent) => void;
  error?: string | null;
  className?: string;
}

const FormContainer: React.FC<FormContainerProps> = ({
  children,
  onSubmit,
  error,
  className,
}) => {
  return (
    <form onSubmit={onSubmit} className={cn("space-y-6", className)}>
      {error && (
        <div className="rounded-md bg-red-50 p-4">
          <div className="flex">
            <div className="ml-3">
              <h3 className="text-sm font-medium text-red-800">{error}</h3>
            </div>
          </div>
        </div>
      )}
      
      <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
        {children}
      </div>
    </form>
  );
};

export default FormContainer; 