import React, { createContext, useContext } from 'react';
import type { ReactNode } from 'react';
import { useAlert, type UseAlertReturn } from '../hooks/useAlert';
import AlertContainer from '../components/ui/AlertContainer';

interface AlertContextType extends UseAlertReturn {}

const AlertContext = createContext<AlertContextType | undefined>(undefined);

interface AlertProviderProps {
  children: ReactNode;
  position?: 'top-right' | 'top-left' | 'bottom-right' | 'bottom-left' | 'top-center' | 'bottom-center';
  maxAlerts?: number;
}

export const AlertProvider: React.FC<AlertProviderProps> = ({ 
  children, 
  position = 'top-right',
  maxAlerts = 5 
}) => {
  const alertMethods = useAlert();

  return (
    <AlertContext.Provider value={alertMethods}>
      {children}
      <AlertContainer
        alerts={alertMethods.alerts}
        onRemoveAlert={alertMethods.removeAlert}
        position={position}
        maxAlerts={maxAlerts}
      />
    </AlertContext.Provider>
  );
};

export const useAlertContext = (): AlertContextType => {
  const context = useContext(AlertContext);
  if (context === undefined) {
    throw new Error('useAlertContext must be used within an AlertProvider');
  }
  return context;
}; 