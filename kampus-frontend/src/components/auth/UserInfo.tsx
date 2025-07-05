import { useAuth } from '../../hooks/useAuth'
import { useLogout } from '../../hooks/useLogout'
import { Button } from '../ui/Button'
import { Card, CardHeader, CardBody } from '../ui/Card'

const UserInfo = () => {
  const { user, isAuthenticated } = useAuth()
  const { logout } = useLogout()

  if (!isAuthenticated || !user) {
    return null
  }

  return (
    <Card className="shadow-lg">
      <CardHeader>
        <h3 className="text-lg font-semibold text-gray-900">
          Información del Usuario
        </h3>
        <p className="text-sm text-gray-600">
          Datos de la sesión activa
        </p>
      </CardHeader>
      <CardBody>
        <div className="space-y-4">
          {/* Información básica */}
          <div className="flex items-center space-x-3">
            <div className="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
              <span className="text-primary-600 font-semibold text-lg">
                {user.nombre.charAt(0)}{user.apellido.charAt(0)}
              </span>
            </div>
            <div>
              <h4 className="font-medium text-gray-900">
                {user.nombre} {user.apellido}
              </h4>
              <p className="text-sm text-gray-600">{user.email}</p>
            </div>
          </div>

          {/* Detalles del usuario */}
          <div className="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span className="font-medium text-gray-700">Username:</span>
              <p className="text-gray-900">{user.username}</p>
            </div>
            <div>
              <span className="font-medium text-gray-700">Estado:</span>
              <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                user.estado === 'activo' 
                  ? 'bg-green-100 text-green-800' 
                  : 'bg-red-100 text-red-800'
              }`}>
                {user.estado}
              </span>
            </div>
          </div>

          {/* Institución */}
          {user.institucion && (
            <div className="border-t pt-4">
              <h5 className="font-medium text-gray-700 mb-2">Institución</h5>
              <div className="bg-gray-50 p-3 rounded-lg">
                <p className="font-medium text-gray-900">{user.institucion.nombre}</p>
              </div>
            </div>
          )}

          {/* Roles */}
          {user.roles && user.roles.length > 0 && (
            <div className="border-t pt-4">
              <h5 className="font-medium text-gray-700 mb-2">Roles</h5>
              <div className="flex flex-wrap gap-2">
                {user.roles.map((role) => (
                  <span
                    key={role.id}
                    className="inline-flex px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 rounded-full"
                  >
                    {role.nombre}
                  </span>
                ))}
              </div>
            </div>
          )}

          {/* Botón de logout */}
          <div className="border-t pt-4">
            <Button
              onClick={logout}
              variant="secondary"
              className="w-full"
              size="sm"
            >
              <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              Cerrar Sesión
            </Button>
          </div>
        </div>
      </CardBody>
    </Card>
  )
}

export default UserInfo 