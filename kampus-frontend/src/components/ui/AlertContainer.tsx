import React from 'react';
import { Alert } from './Alert';
import type { AlertConfig } from '../../hooks/useAlert';

interface AlertContainerProps {
  alerts: AlertConfig[];
  onRemoveAlert: (id: string) => void;
  position?: 'top-right' | 'top-left' | 'bottom-right' | 'bottom-left' | 'top-center' | 'bottom-center';
  maxAlerts?: number;
}

const AlertContainer: React.FC<AlertContainerProps> = ({
  alerts,
  onRemoveAlert,
  position = 'top-right',
  maxAlerts = 5,
}) => {
  const getPositionClasses = () => {
    switch (position) {
      case 'top-right':
        return 'top-4 right-4';
      case 'top-left':
        return 'top-4 left-4';
      case 'bottom-right':
        return 'bottom-4 right-4';
      case 'bottom-left':
        return 'bottom-4 left-4';
      case 'top-center':
        return 'top-4 left-1/2 transform -translate-x-1/2';
      case 'bottom-center':
        return 'bottom-4 left-1/2 transform -translate-x-1/2';
      default:
        return 'top-4 right-4';
    }
  };

  const getWidthClasses = () => {
    switch (position) {
      case 'top-center':
      case 'bottom-center':
        return 'w-full max-w-md';
      default:
        return 'w-full max-w-sm';
    }
  };

  // Limitar el número de alertas mostradas
  const visibleAlerts = alerts.slice(0, maxAlerts);

  if (visibleAlerts.length === 0) {
    return null;
  }

  return (
    <div className={`fixed z-50 ${getPositionClasses()} ${getWidthClasses()} space-y-2`}>
      {visibleAlerts.map((alert) => (
        <Alert
          key={alert.id}
          variant={alert.type}
          title={alert.title}
          message={alert.message}
          autoClose={alert.autoClose}
          autoCloseDelay={alert.autoCloseDelay}
          showIcon={alert.showIcon}
          onClose={() => onRemoveAlert(alert.id)}
          className="shadow-lg"
        />
      ))}
    </div>
  );
};

export default AlertContainer; 