import { useAuth } from '../hooks/useAuth'
import { Button } from '../components/ui/Button'
import { Card, CardHeader, CardBody } from '../components/ui/Card'

const DashboardPage = () => {
  const { user, isAuthenticated, logout } = useAuth()

  const handleLogout = async () => {
    try {
      await logout()
    } catch (error) {
      console.error('Error al cerrar sesión:', error)
    }
  }

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Dashboard</h1>
          <p className="text-gray-600 mt-1">
            Bienvenido al sistema de gestión académica
          </p>
        </div>
        <Button
          variant="secondary"
          onClick={handleLogout}
          leftIcon={
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          }
        >
          Cerrar Sesión
        </Button>
      </div>

      {/* Información de autenticación */}
      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Estado de Autenticación</h2>
        </CardHeader>
        <CardBody>
          <div className="space-y-4">
            <div className="flex items-center justify-between">
              <span className="text-sm font-medium text-gray-500">Autenticado:</span>
              <span className={`inline-flex rounded-full px-2 text-xs font-semibold leading-5 ${
                isAuthenticated ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
              }`}>
                {isAuthenticated ? 'Sí' : 'No'}
              </span>
            </div>
            
            {user && (
              <>
                <div className="flex items-center justify-between">
                  <span className="text-sm font-medium text-gray-500">Usuario:</span>
                  <span className="text-sm text-gray-900">{user.nombre} {user.apellido}</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm font-medium text-gray-500">Email:</span>
                  <span className="text-sm text-gray-900">{user.email}</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm font-medium text-gray-500">Institución:</span>
                  <span className="text-sm text-gray-900">{user.institucion?.nombre || 'No asignada'}</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm font-medium text-gray-500">Roles:</span>
                  <div className="flex gap-1">
                    {user.roles?.map((role) => (
                      <span
                        key={role.id}
                        className="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-blue-100 text-blue-800"
                      >
                        {role.nombre}
                      </span>
                    ))}
                  </div>
                </div>
              </>
            )}
          </div>
        </CardBody>
      </Card>

      {/* Estadísticas rápidas */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card>
          <CardBody>
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
                  <svg className="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                  </svg>
                </div>
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-500">Estudiantes</p>
                <p className="text-2xl font-semibold text-gray-900">0</p>
              </div>
            </div>
          </CardBody>
        </Card>

        <Card>
          <CardBody>
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                  <svg className="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-500">Docentes</p>
                <p className="text-2xl font-semibold text-gray-900">0</p>
              </div>
            </div>
          </CardBody>
        </Card>

        <Card>
          <CardBody>
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                  <svg className="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-500">Instituciones</p>
                <p className="text-2xl font-semibold text-gray-900">0</p>
              </div>
            </div>
          </CardBody>
        </Card>
      </div>
    </div>
  )
}

export default DashboardPage 