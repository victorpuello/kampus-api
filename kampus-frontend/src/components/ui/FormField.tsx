import React from 'react';
import { cn } from '../../utils/cn';

interface FormFieldProps {
  label: string;
  name: string;
  type?: 'text' | 'email' | 'password' | 'tel' | 'date' | 'number' | 'url';
  required?: boolean;
  value: string | number;
  onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
  placeholder?: string;
  error?: string;
  className?: string;
  disabled?: boolean;
  min?: string | number;
  max?: string | number;
  step?: string | number;
}

const FormField: React.FC<FormFieldProps> = ({
  label,
  name,
  type = 'text',
  required = false,
  value,
  onChange,
  placeholder,
  error,
  className,
  disabled = false,
  min,
  max,
  step,
}) => {
  return (
    <div className={className}>
      <label htmlFor={name} className="block text-sm font-medium text-gray-700">
        {label}
        {required && <span className="text-red-500 ml-1">*</span>}
      </label>
      <input
        type={type}
        name={name}
        id={name}
        required={required}
        value={value}
        onChange={onChange}
        placeholder={placeholder}
        disabled={disabled}
        min={min}
        max={max}
        step={step}
        className={cn(
          "mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm",
          error && "border-red-300 focus:border-red-500 focus:ring-red-500",
          disabled && "bg-gray-50 cursor-not-allowed"
        )}
      />
      {error && (
        <p className="mt-1 text-sm text-red-600">{error}</p>
      )}
    </div>
  );
};

export default FormField; 