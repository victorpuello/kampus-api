import React from 'react';
import { cn } from '../../utils/cn';

interface Option {
  value: string | number;
  label: string;
}

interface FormSelectProps {
  label: string;
  name: string;
  required?: boolean;
  value: string | number;
  onChange: (e: React.ChangeEvent<HTMLSelectElement>) => void;
  options: Option[];
  placeholder?: string;
  error?: string;
  className?: string;
  disabled?: boolean;
}

const FormSelect: React.FC<FormSelectProps> = ({
  label,
  name,
  required = false,
  value,
  onChange,
  options,
  placeholder,
  error,
  className,
  disabled = false,
}) => {
  return (
    <div className={className}>
      <label htmlFor={name} className="block text-sm font-medium text-gray-700">
        {label}
        {required && <span className="text-red-500 ml-1">*</span>}
      </label>
      <select
        name={name}
        id={name}
        required={required}
        value={value}
        onChange={onChange}
        disabled={disabled}
        className={cn(
          "mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm",
          error && "border-red-300 focus:border-red-500 focus:ring-red-500",
          disabled && "bg-gray-50 cursor-not-allowed"
        )}
      >
        {placeholder && (
          <option value="">{placeholder}</option>
        )}
        {options.map((option) => (
          <option key={option.value} value={option.value}>
            {option.label}
          </option>
        ))}
      </select>
      {error && (
        <p className="mt-1 text-sm text-red-600">{error}</p>
      )}
    </div>
  );
};

export default FormSelect; 