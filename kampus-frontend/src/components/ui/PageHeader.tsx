import React from 'react';
import { cn } from '../../utils/cn';

interface PageHeaderProps {
  title: string;
  description?: string;
  children?: React.ReactNode;
  className?: string;
}

const PageHeader: React.FC<PageHeaderProps> = ({
  title,
  description,
  children,
  className,
}) => {
  return (
    <div className={cn("sm:flex sm:items-center", className)}>
      <div className="sm:flex-auto">
        <h1 className="text-xl font-semibold text-gray-900">{title}</h1>
        {description && (
          <p className="mt-2 text-sm text-gray-700">{description}</p>
        )}
      </div>
      {children && (
        <div className="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
          {children}
        </div>
      )}
    </div>
  );
};

export default PageHeader; 