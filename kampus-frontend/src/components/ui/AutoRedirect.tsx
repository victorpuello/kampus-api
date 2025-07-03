import React, { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Alert } from './Alert';

interface AutoRedirectProps {
  id: string | undefined;
  redirectTo: string;
  message?: string;
  delay?: number;
}

export const AutoRedirect: React.FC<AutoRedirectProps> = ({ 
  id, 
  redirectTo, 
  message = 'ID inválido, redirigiendo...',
  delay = 2000 
}) => {
  const navigate = useNavigate();

  useEffect(() => {
    if (!id || id === 'null' || id === 'undefined') {
      console.error('❌ Invalid ID detected, redirecting to:', redirectTo);
      const timer = setTimeout(() => {
        navigate(redirectTo);
      }, delay);
      
      return () => clearTimeout(timer);
    }
  }, [id, redirectTo, navigate, delay]);

  if (!id || id === 'null' || id === 'undefined') {
    return (
      <div className="space-y-6">
        <Alert
          variant="error"
          message={message}
          onClose={() => navigate(redirectTo)}
        />
        <div className="text-center">
          <p className="text-gray-600">Redirigiendo en {delay / 1000} segundos...</p>
          <button
            onClick={() => navigate(redirectTo)}
            className="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            Ir ahora
          </button>
        </div>
      </div>
    );
  }

  return null;
}; 