// Configuración de la API
export const API_CONFIG = {
  // URL base de la API
  baseURL: import.meta.env.VITE_API_URL || 'http://kampus.test/api/v1',
  
  // Timeout para las peticiones (en milisegundos)
  timeout: 10000,
  
  // Headers por defecto
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  
  // Configuración de CORS
  withCredentials: true,
}

// Configuración de autenticación
export const AUTH_CONFIG = {
  // Nombre del token en localStorage
  tokenKey: 'auth-token',
  
  // Nombre del usuario en localStorage
  userKey: 'auth-user',
  
  // Tiempo de expiración del token (en minutos)
  tokenExpiration: 60 * 24, // 24 horas
  
  // Rutas que no requieren autenticación
  publicRoutes: ['/login', '/register', '/forgot-password'],
  
  // Ruta de redirección después del login
  redirectAfterLogin: '/dashboard',
  
  // Ruta de redirección después del logout
  redirectAfterLogout: '/login',
} 