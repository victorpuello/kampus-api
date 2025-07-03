# Sistema de Autenticación Basado en Tokens API

## Resumen

Se ha implementado un nuevo sistema de autenticación basado en tokens API de Laravel Sanctum, reemplazando el sistema anterior basado en sesiones y cookies. Este nuevo sistema es más simple, confiable y estándar para aplicaciones SPA.

## Características del Nuevo Sistema

### ✅ Ventajas
- **Simplicidad**: No requiere manejo de cookies CSRF
- **Confiabilidad**: Funciona consistentemente en diferentes entornos
- **Estándar**: Usa el patrón Bearer Token estándar
- **Seguridad**: Tokens se invalidan automáticamente en logout
- **Escalabilidad**: Fácil de integrar con microservicios

### 🔧 Componentes Implementados

#### Backend (Laravel)

1. **AuthController** (`app/Http/Controllers/Api/V1/AuthController.php`)
   - `login()`: Autentica usuario y devuelve token + datos del usuario
   - `logout()`: Invalida el token actual
   - `me()`: Obtiene información del usuario autenticado

2. **Rutas** (`routes/api.php`)
   - `/v1/login` (POST): Público
   - `/v1/logout` (POST): Protegido con `auth:sanctum`
   - `/v1/me` (GET): Protegido con `auth:sanctum`

3. **Middleware**: `auth:sanctum` para rutas protegidas

#### Frontend (React + TypeScript)

1. **Store de Autenticación** (`src/store/authStore.ts`)
   - Maneja token y estado del usuario
   - Persistencia automática en localStorage
   - Métodos `login()` y `logout()`

2. **Cliente Axios** (`src/api/axiosClient.ts`)
   - Interceptor automático para agregar token Bearer
   - Manejo de errores 401 (logout automático)

## Flujo de Autenticación

### 1. Login
```typescript
// Frontend
const { login } = useAuthStore();
await login('admin@example.com', '123456');

// Backend responde con:
{
  "token": "1|abcdefg12345...",
  "user": { /* datos del usuario */ }
}
```

### 2. Peticiones Autenticadas
```typescript
// El interceptor de Axios agrega automáticamente:
headers: {
  'Authorization': 'Bearer 1|abcdefg12345...'
}
```

### 3. Logout
```typescript
// Frontend
const { logout } = useAuthStore();
await logout();

// Backend invalida el token y responde:
{
  "message": "Sesión cerrada exitosamente"
}
```

## Endpoints de la API

### POST /api/v1/login
**Público** - Inicia sesión y devuelve token

**Request:**
```json
{
  "email": "admin@example.com",
  "password": "123456"
}
```

**Response (200):**
```json
{
  "token": "1|abcdefg12345...",
  "user": {
    "id": 1,
    "nombre": "Admin",
    "apellido": "Sistema",
    "email": "admin@example.com",
    "roles": [...],
    "institucion": {...}
  }
}
```

### POST /api/v1/logout
**Protegido** - Cierra sesión invalidando token

**Headers:**
```
Authorization: Bearer 1|abcdefg12345...
```

**Response (200):**
```json
{
  "message": "Sesión cerrada exitosamente"
}
```

### GET /api/v1/me
**Protegido** - Obtiene información del usuario autenticado

**Headers:**
```
Authorization: Bearer 1|abcdefg12345...
```

**Response (200):**
```json
{
  "user": {
    "id": 1,
    "nombre": "Admin",
    "apellido": "Sistema",
    "email": "admin@example.com",
    "roles": [...],
    "institucion": {...}
  }
}
```

## Configuración del Frontend

### Store de Autenticación
```typescript
interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
  login: (email: string, password: string) => Promise<void>
  logout: () => void
}
```

### Cliente Axios
```typescript
// Interceptor automático para agregar token
axiosClient.interceptors.request.use((config) => {
  const token = useAuthStore.getState().token;
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});
```

## Pruebas

### Script de Prueba Backend
```bash
php test_token_auth.php
```

### Página de Prueba Frontend
Abrir `kampus-frontend/test-token-auth.html` en el navegador

## Migración desde el Sistema Anterior

### Cambios Realizados

1. **Backend**:
   - ✅ Eliminado manejo de cookies CSRF
   - ✅ Cambiado de sesiones a tokens API
   - ✅ Actualizado middleware de `web` + `auth:web` a `auth:sanctum`
   - ✅ Agregado endpoint `/me`

2. **Frontend**:
   - ✅ Eliminado `withCredentials` y `withXSRFToken`
   - ✅ Agregado manejo de token en store
   - ✅ Actualizado interceptor de Axios
   - ✅ Simplificado flujo de login/logout

### Beneficios de la Migración

- **Eliminación de errores 419**: No más problemas de CSRF
- **Mejor rendimiento**: No hay overhead de cookies
- **Mayor compatibilidad**: Funciona en todos los navegadores
- **Facilidad de debugging**: Tokens visibles en headers
- **Escalabilidad**: Fácil integración con microservicios

## Seguridad

### Medidas Implementadas

1. **Invalidación de Tokens**: Al hacer logout, el token se elimina del servidor
2. **Tokens Únicos**: Cada login genera un nuevo token, revocando los anteriores
3. **Expiración**: Los tokens tienen expiración configurable
4. **HTTPS**: Recomendado para producción

### Configuración de Sanctum

```php
// config/sanctum.php
'expiration' => 60 * 24 * 7, // 7 días
'guard' => ['web'],
```

## Troubleshooting

### Problemas Comunes

1. **Error 401 en todas las peticiones**
   - Verificar que el token se está enviando en el header `Authorization`
   - Verificar que el token no haya expirado

2. **Error 500 en login**
   - Verificar que el modelo User tenga el trait `HasApiTokens`
   - Verificar que la tabla `personal_access_tokens` exista

3. **Token no se guarda en el frontend**
   - Verificar que el store esté configurado correctamente
   - Verificar que la persistencia esté funcionando

### Logs de Debug

```typescript
// En el frontend
console.log('Token:', useAuthStore.getState().token);

// En el backend
Log::info('Token recibido:', ['token' => $request->bearerToken()]);
```

## Próximos Pasos

1. **Testing**: Implementar tests unitarios y de integración
2. **Refresh Tokens**: Considerar implementar refresh tokens para mayor seguridad
3. **Rate Limiting**: Agregar rate limiting a endpoints de autenticación
4. **Auditoría**: Implementar logs de auditoría para acciones de autenticación
5. **Documentación API**: Actualizar documentación OpenAPI/Swagger

---

**Nota**: Este sistema reemplaza completamente el anterior sistema basado en sesiones. Todos los endpoints protegidos ahora usan `auth:sanctum` y requieren el header `Authorization: Bearer <token>`. 