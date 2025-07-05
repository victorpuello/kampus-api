import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuthStore } from '../../store/authStore'
import { Button } from '../ui/Button'
import { Input } from '../ui/Input'
import { Card, CardHeader, CardBody } from '../ui/Card'
import { useAlertContext } from '../../contexts/AlertContext'

interface LoginFormProps {
  onSuccess?: () => void
  redirectTo?: string
}

const LoginForm = ({ onSuccess, redirectTo = '/dashboard' }: LoginFormProps) => {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [loading, setLoading] = useState(false)
  
  const navigate = useNavigate()
  const { login } = useAuthStore()
  const { showSuccess, showError } = useAlertContext()

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)

    try {
      console.log('🚀 Iniciando login con el nuevo microservicio...')
      
      // Usar el nuevo microservicio de autenticación
      await login(email, password)
      
      console.log('✅ Login exitoso con el nuevo sistema de tokens')
      showSuccess('Inicio de sesión exitoso', 'Bienvenido al sistema')
      
      // Llamar callback de éxito si existe
      if (onSuccess) {
        onSuccess()
      }
      
      // Redirigir al dashboard o ruta especificada
      navigate(redirectTo)
      
    } catch (err: any) {
      console.error('❌ Error en login:', err)
      const errorMessage = err.message || 'Credenciales inválidas'
      showError(errorMessage, 'Error de autenticación')
    } finally {
      setLoading(false)
    }
  }

  return (
    <Card className="shadow-xl">
      <CardHeader>
        <h2 className="text-xl font-semibold text-gray-900 text-center">
          Iniciar Sesión
        </h2>
        <p className="text-sm text-gray-600 text-center mt-1">
          Ingresa tus credenciales para acceder al sistema
        </p>
      </CardHeader>
      <CardBody>
        <form onSubmit={handleSubmit} className="space-y-6">
          <Input
            label="Correo Electrónico"
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="tu@email.com"
            required
            disabled={loading}
            leftIcon={
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
              </svg>
            }
          />

          <Input
            label="Contraseña"
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            placeholder="••••••••"
            required
            disabled={loading}
            leftIcon={
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
            }
          />

          <Button
            type="submit"
            loading={loading}
            className="w-full"
            size="lg"
          >
            {loading ? 'Iniciando sesión...' : 'Iniciar Sesión'}
          </Button>
        </form>

        {/* Información adicional */}
        <div className="mt-6 pt-6 border-t border-gray-200">
          <div className="text-center">
            <p className="text-xs text-gray-500">
              ¿Necesitas ayuda? Contacta al administrador del sistema
            </p>
          </div>
        </div>
      </CardBody>
    </Card>
  )
}

export default LoginForm 