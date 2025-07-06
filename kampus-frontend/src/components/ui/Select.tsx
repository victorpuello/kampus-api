import React from 'react';

interface SelectProps extends React.SelectHTMLAttributes<HTMLSelectElement> {
  children: React.ReactNode;
}

export const Select: React.FC<SelectProps> = ({ 
  children, 
  className = '', 
  ...props 
}) => {
  return (
    <select
      className={`
        block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
        focus:outline-none focus:ring-blue-500 focus:border-blue-500 
        sm:text-sm ${className}
      `}
      {...props}
    >
      {children}
    </select>
  );
}; 