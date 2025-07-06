import React from 'react';

interface TextareaProps extends React.TextareaHTMLAttributes<HTMLTextAreaElement> {
  children?: React.ReactNode;
}

export const Textarea: React.FC<TextareaProps> = ({ 
  children, 
  className = '', 
  ...props 
}) => {
  return (
    <textarea
      className={`
        block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
        focus:outline-none focus:ring-blue-500 focus:border-blue-500 
        sm:text-sm resize-vertical ${className}
      `}
      {...props}
    >
      {children}
    </textarea>
  );
}; 