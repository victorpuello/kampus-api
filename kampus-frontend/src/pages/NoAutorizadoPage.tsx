import { useNavigate } from 'react-router-dom';
import { Button } from '../components/ui/Button';
import { Card, CardHeader, CardBody } from '../components/ui/Card';

const NoAutorizadoPage = () => {
  const navigate = useNavigate();

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-md w-full space-y-8">
        <Card>
          <CardHeader>
            <div className="text-center">
              <div className="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg
                  className="h-6 w-6 text-red-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                  />
                </svg>
              </div>
              <h2 className="mt-6 text-3xl font-extrabold text-gray-900">
                Acceso Denegado
              </h2>
              <p className="mt-2 text-sm text-gray-600">
                No tienes permisos para acceder a esta página
              </p>
            </div>
          </CardHeader>
          <CardBody>
            <div className="space-y-4">
              <p className="text-center text-gray-500">
                Contacta al administrador del sistema si crees que deberías tener acceso a esta funcionalidad.
              </p>
              
              <div className="flex flex-col space-y-3">
                <Button
                  variant="primary"
                  onClick={() => navigate('/dashboard')}
                  className="w-full"
                >
                  Volver al Dashboard
                </Button>
                
                <Button
                  variant="secondary"
                  onClick={() => navigate(-1)}
                  className="w-full"
                >
                  Volver Atrás
                </Button>
              </div>
            </div>
          </CardBody>
        </Card>
      </div>
    </div>
  );
};

export default NoAutorizadoPage; 