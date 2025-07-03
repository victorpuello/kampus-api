import React from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { Alert } from './Alert';

interface ValidateParamsProps {
  children: React.ReactNode;
  paramName: string;
  redirectTo: string;
}

export const ValidateParams: React.FC<ValidateParamsProps> = ({ 
  children, 
  paramName, 
  redirectTo 
}) => {
  const params = useParams();
  const navigate = useNavigate();
  const paramValue = params[paramName];

  // Validar que el parámetro sea válido
  if (!paramValue || paramValue === 'null' || paramValue === 'undefined') {
    console.error(`❌ Invalid ${paramName} parameter:`, paramValue);
    
    return (
      <div className="space-y-6">
        <Alert
          variant="error"
          message={`El parámetro ${paramName} es inválido`}
          onClose={() => navigate(redirectTo)}
        />
        <div className="text-center">
          <button
            onClick={() => navigate(redirectTo)}
            className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            Volver
          </button>
        </div>
      </div>
    );
  }

  return <>{children}</>;
}; 