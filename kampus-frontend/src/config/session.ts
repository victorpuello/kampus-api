export const SESSION_CONFIG = {
  // Tiempo de verificación de token (5 minutos)
  TOKEN_CHECK_INTERVAL: 5 * 60 * 1000,
  
  // Tiempo antes de expiración para renovar token (1 hora)
  TOKEN_REFRESH_THRESHOLD: 60 * 60 * 1000,
  
  // Tiempo máximo de inactividad (2 horas)
  MAX_INACTIVITY_TIME: 2 * 60 * 60 * 1000,
  
  // Configuración de almacenamiento
  STORAGE_KEYS: {
    TOKEN: 'kampus_token',
    USER: 'kampus_user',
    LAST_ACTIVITY: 'kampus_last_activity',
  },
  
  // Mensajes de error
  MESSAGES: {
    SESSION_EXPIRED: 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.',
    TOKEN_REFRESHED: 'Tu sesión ha sido renovada automáticamente.',
    INACTIVITY_WARNING: 'Tu sesión expirará pronto por inactividad.',
  },
} as const 