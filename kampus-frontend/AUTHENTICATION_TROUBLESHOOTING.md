# Solución de Problemas de Autenticación

## Error "Unauthenticated"

Si estás experimentando el error "Unauthenticated", sigue estos pasos para solucionarlo:

### 1. Verificar la Configuración del Backend

Asegúrate de que tu servidor Laravel esté funcionando correctamente:

```bash
# Verificar que el servidor esté corriendo
php artisan serve

# Verificar las rutas de la API
php artisan route:list --path=api
```

### 2. Verificar la Base de Datos

Asegúrate de que exista un usuario en la base de datos:

```bash
# Ejecutar las migraciones
php artisan migrate

# Ejecutar los seeders
php artisan db:seed

# Verificar que existe el usuario admin
php artisan tinker
>>> App\Models\User::where('email', 'admin@example.com')->first();
```

### 3. Verificar la Configuración de CORS

En tu archivo `config/cors.php` de Laravel, asegúrate de que esté configurado correctamente:

```php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:5173', 'http://127.0.0.1:5173'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

### 4. Verificar la Configuración de Sanctum

En tu archivo `config/sanctum.php`, asegúrate de que los dominios estén configurados:

```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,localhost:5173,127.0.0.1,127.0.0.1:8000,::1',
    env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
))),
```

### 5. Verificar el Archivo .env

Asegúrate de que tu archivo `.env` tenga la configuración correcta:

```env
APP_URL=http://kampus.test
SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173
SESSION_DOMAIN=.kampus.test
```

### 6. Limpiar la Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### 7. Verificar el Frontend

Asegúrate de que el frontend esté configurado correctamente:

1. **Verificar la URL de la API**: En `src/config/api.ts`
2. **Verificar el store de autenticación**: En `src/store/authStore.ts`
3. **Verificar los interceptores de axios**: En `src/api/axiosClient.ts`

### 8. Probar la Autenticación

Usa los archivos de prueba incluidos:

1. Abre `test-connection.html` en tu navegador
2. Haz clic en "Probar Conexión"
3. Verifica que recibas una respuesta exitosa

### 9. Verificar en el Navegador

1. Abre las herramientas de desarrollador (F12)
2. Ve a la pestaña "Network"
3. Intenta hacer login
4. Verifica que las peticiones incluyan el header `Authorization: Bearer <token>`

### 10. Verificar el LocalStorage

1. Abre las herramientas de desarrollador (F12)
2. Ve a la pestaña "Application" > "Local Storage"
3. Verifica que exista la clave `auth-storage` con el token

## Comandos Útiles para Debugging

### Backend

```bash
# Ver logs de Laravel
tail -f storage/logs/laravel.log

# Verificar rutas protegidas
php artisan route:list --path=api/v1

# Probar autenticación manualmente
php artisan tinker
>>> $user = App\Models\User::first();
>>> $token = $user->createToken('test')->plainTextToken;
>>> echo $token;
```

### Frontend

```bash
# Ver logs del navegador
# Abre las herramientas de desarrollador y ve a la consola

# Limpiar localStorage
localStorage.clear()

# Verificar el estado del store
console.log(useAuthStore.getState())
```

## Problemas Comunes

### 1. Token no se envía en las peticiones

**Solución**: Verifica que el interceptor de axios esté configurado correctamente en `src/api/axiosClient.ts`.

### 2. Error de CORS

**Solución**: Verifica la configuración de CORS en Laravel y asegúrate de que `supports_credentials` esté en `true`.

### 3. Token expirado

**Solución**: El interceptor de axios debería manejar automáticamente los errores 401 y redirigir al login.

### 4. Problemas con el dominio

**Solución**: Asegúrate de que estés usando el dominio correcto en la configuración de Sanctum.

## Contacto

Si sigues teniendo problemas, verifica:

1. Los logs de Laravel en `storage/logs/laravel.log`
2. Los logs del navegador en la consola
3. Las peticiones de red en las herramientas de desarrollador 