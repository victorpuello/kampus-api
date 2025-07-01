import { useState, useCallback } from 'react';

export type AlertType = 'success' | 'error' | 'warning' | 'info';

export interface AlertConfig {
  id: string;
  type: AlertType;
  title?: string;
  message: string;
  autoClose?: boolean;
  autoCloseDelay?: number;
  showIcon?: boolean;
}

export interface UseAlertReturn {
  alerts: AlertConfig[];
  showAlert: (type: AlertType, message: string, title?: string, options?: Partial<AlertConfig>) => void;
  showSuccess: (message: string, title?: string, options?: Partial<AlertConfig>) => void;
  showError: (message: string, title?: string, options?: Partial<AlertConfig>) => void;
  showWarning: (message: string, title?: string, options?: Partial<AlertConfig>) => void;
  showInfo: (message: string, title?: string, options?: Partial<AlertConfig>) => void;
  removeAlert: (id: string) => void;
  clearAlerts: () => void;
}

export const useAlert = (): UseAlertReturn => {
  const [alerts, setAlerts] = useState<AlertConfig[]>([]);

  const generateId = useCallback(() => {
    return `alert-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
  }, []);

  const showAlert = useCallback((
    type: AlertType, 
    message: string, 
    title?: string, 
    options: Partial<AlertConfig> = {}
  ) => {
    const id = generateId();
    const newAlert: AlertConfig = {
      id,
      type,
      title,
      message,
      autoClose: true,
      autoCloseDelay: 5000,
      showIcon: true,
      ...options,
    };

    setAlerts(prev => [...prev, newAlert]);

    // Si autoClose está habilitado, remover automáticamente
    if (newAlert.autoClose) {
      setTimeout(() => {
        removeAlert(id);
      }, newAlert.autoCloseDelay);
    }
  }, [generateId]);

  const showSuccess = useCallback((message: string, title?: string, options?: Partial<AlertConfig>) => {
    showAlert('success', message, title, options);
  }, [showAlert]);

  const showError = useCallback((message: string, title?: string, options?: Partial<AlertConfig>) => {
    showAlert('error', message, title, options);
  }, [showAlert]);

  const showWarning = useCallback((message: string, title?: string, options?: Partial<AlertConfig>) => {
    showAlert('warning', message, title, options);
  }, [showAlert]);

  const showInfo = useCallback((message: string, title?: string, options?: Partial<AlertConfig>) => {
    showAlert('info', message, title, options);
  }, [showAlert]);

  const removeAlert = useCallback((id: string) => {
    setAlerts(prev => prev.filter(alert => alert.id !== id));
  }, []);

  const clearAlerts = useCallback(() => {
    setAlerts([]);
  }, []);

  return {
    alerts,
    showAlert,
    showSuccess,
    showError,
    showWarning,
    showInfo,
    removeAlert,
    clearAlerts,
  };
}; 