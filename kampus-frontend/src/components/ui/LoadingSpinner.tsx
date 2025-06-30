import React from 'react';
import { cva, type VariantProps } from 'class-variance-authority';
import { cn } from '../../utils/cn';

const spinnerVariants = cva(
  'animate-spin rounded-full border-2 border-gray-300 border-t-primary-600',
  {
    variants: {
      size: {
        sm: 'h-4 w-4',
        md: 'h-6 w-6',
        lg: 'h-8 w-8',
        xl: 'h-12 w-12',
      },
      color: {
        primary: 'border-t-primary-600',
        white: 'border-t-white',
        gray: 'border-t-gray-600',
      },
    },
    defaultVariants: {
      size: 'md',
      color: 'primary',
    },
  }
);

export interface LoadingSpinnerProps
  extends Omit<React.HTMLAttributes<HTMLDivElement>, 'color'>,
    VariantProps<typeof spinnerVariants> {
  text?: string;
  fullScreen?: boolean;
}

const LoadingSpinner = React.forwardRef<HTMLDivElement, LoadingSpinnerProps>(
  ({ className, size, color, text, fullScreen, ...props }, ref) => {
    const spinner = (
      <div
        className={cn(spinnerVariants({ size, color, className }))}
        ref={ref}
        {...props}
      />
    );

    if (fullScreen) {
      return (
        <div className="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50">
          <div className="text-center">
            {spinner}
            {text && (
              <p className="mt-4 text-sm text-gray-600">{text}</p>
            )}
          </div>
        </div>
      );
    }

    if (text) {
      return (
        <div className="flex flex-col items-center justify-center">
          {spinner}
          <p className="mt-2 text-sm text-gray-600">{text}</p>
        </div>
      );
    }

    return spinner;
  }
);

LoadingSpinner.displayName = 'LoadingSpinner';

export { LoadingSpinner, spinnerVariants }; 