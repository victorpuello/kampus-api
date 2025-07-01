# 🎓 Kampus - Sistema de Gestión Académica

Un sistema completo de gestión académica desarrollado con **Laravel** (Backend API) y **React + TypeScript** (Frontend), diseñado para administrar estudiantes, docentes, instituciones y procesos académicos de manera eficiente.

## ✨ Características Principales

### 🔐 Sistema de Autenticación
- **Autenticación JWT** con Laravel Sanctum
- **Gestión de roles y permisos** (RBAC)
- **Protección de rutas** y middleware de autenticación
- **Persistencia de sesión** con Zustand
- **Interceptores automáticos** para tokens en peticiones

### 👥 Gestión de Usuarios
- **Estudiantes**: Registro completo con información personal, académica y familiar
- **Docentes**: Gestión de personal docente con especialidades y contratos
- **Acudientes**: Sistema de acudientes vinculados a estudiantes
- **Administradores**: Panel de administración con roles y permisos

### 🏫 Gestión Institucional
- **Instituciones**: Configuración de centros educativos
- **Grados y Grupos**: Organización académica por niveles
- **Asignaturas**: Gestión de materias y áreas de conocimiento
- **Aulas**: Administración de espacios físicos

### 📊 Funcionalidades Académicas
- **Notas y Calificaciones**: Sistema de evaluación por períodos
- **Inasistencias**: Control de asistencia con justificaciones
- **Observaciones**: Seguimiento conductual y académico
- **Reportes**: Generación de informes académicos

### 🎨 Interfaz de Usuario
- **Diseño Responsive**: Adaptable a dispositivos móviles y desktop
- **Tema Moderno**: UI/UX con Tailwind CSS y componentes personalizados
- **Tablas Avanzadas**: DataTable con búsqueda, ordenamiento y paginación
- **Sistema de Alertas**: Notificaciones elegantes y personalizables
- **Confirmaciones**: Diálogos de confirmación profesionales

## 🛠️ Tecnologías Utilizadas

### Backend (Laravel)
- **Laravel 10** - Framework PHP
- **Laravel Sanctum** - Autenticación API
- **MySQL** - Base de datos
- **Eloquent ORM** - Mapeo objeto-relacional
- **API Resources** - Transformación de datos
- **Form Requests** - Validación de datos
- **Migrations** - Control de versiones de BD

### Frontend (React)
- **React 18** - Biblioteca de UI
- **TypeScript** - Tipado estático
- **Vite** - Build tool y dev server
- **React Router** - Navegación SPA
- **Zustand** - Gestión de estado
- **Axios** - Cliente HTTP
- **Tailwind CSS** - Framework CSS
- **Class Variance Authority** - Sistema de variantes

### Herramientas de Desarrollo
- **ESLint** - Linting de código
- **Prettier** - Formateo de código
- **Git** - Control de versiones
- **XAMPP** - Entorno de desarrollo local

## 📁 Estructura del Proyecto

```
kampus-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/V1/    # Controladores API
│   │   ├── Requests/              # Validación de formularios
│   │   └── Resources/             # Transformación de datos
│   ├── Models/                    # Modelos Eloquent
│   └── Providers/                 # Proveedores de servicios
├── database/
│   ├── migrations/                # Migraciones de BD
│   ├── seeders/                   # Datos de prueba
│   └── factories/                 # Factories para testing
├── routes/
│   └── api.php                    # Rutas API
└── kampus-frontend/               # Aplicación React
    ├── src/
    │   ├── components/            # Componentes reutilizables
    │   ├── pages/                 # Páginas de la aplicación
    │   ├── hooks/                 # Hooks personalizados
    │   ├── contexts/              # Contextos de React
    │   ├── api/                   # Cliente HTTP
    │   ├── store/                 # Estado global (Zustand)
    │   └── utils/                 # Utilidades
    └── public/                    # Archivos estáticos
```

## 🚀 Instalación y Configuración

### Prerrequisitos
- **PHP 8.1+**
- **Composer**
- **Node.js 18+**
- **MySQL 8.0+**
- **XAMPP** (recomendado para desarrollo local)

### 1. Clonar el Repositorio
```bash
git clone https://github.com/victorpuello/kampus-api.git
cd kampus-api
```

### 2. Configurar el Backend (Laravel)

#### Instalar dependencias
```bash
composer install
```

#### Configurar variables de entorno
```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con la configuración de tu base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kampus_db
DB_USERNAME=root
DB_PASSWORD=
```

#### Ejecutar migraciones y seeders
```bash
php artisan migrate
php artisan db:seed
```

#### Configurar CORS (para desarrollo)
En `config/cors.php`:
```php
'allowed_origins' => ['http://localhost:5173'],
'supports_credentials' => true,
```

#### Configurar Sanctum
En `config/sanctum.php`:
```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost:5173')),
```

### 3. Configurar el Frontend (React)

#### Navegar al directorio del frontend
```bash
cd kampus-frontend
```

#### Instalar dependencias
```bash
npm install
```

#### Configurar variables de entorno
Crear `.env`:
```env
VITE_API_URL=http://kampus.test/api/v1
```

#### Iniciar el servidor de desarrollo
```bash
npm run dev
```

### 4. Configurar el Host Virtual (Opcional)

Para desarrollo local, puedes configurar un host virtual:

1. Editar `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 kampus.test
```

2. Configurar XAMPP para el dominio `kampus.test`

## 🔧 Configuración de Desarrollo

### Credenciales por Defecto
- **Email**: `admin@example.com`
- **Contraseña**: `password`

### Comandos Útiles

#### Backend
```bash
# Limpiar caché
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Ver rutas API
php artisan route:list --path=api

# Ejecutar tests
php artisan test

# Crear migración
php artisan make:migration nombre_migracion

# Crear seeder
php artisan make:seeder NombreSeeder
```

#### Frontend
```bash
# Construir para producción
npm run build

# Linting
npm run lint

# Preview de producción
npm run preview
```

## 📱 Uso del Sistema

### 1. Autenticación
- Acceder a `http://localhost:5173/login`
- Usar las credenciales por defecto
- El sistema redirigirá automáticamente al dashboard

### 2. Gestión de Estudiantes
- **Listar**: Ver todos los estudiantes con filtros y búsqueda
- **Crear**: Formulario completo con validación
- **Editar**: Modificar información existente
- **Eliminar**: Con confirmación de seguridad
- **Ver Detalles**: Información completa del estudiante

### 3. Gestión de Docentes
- **Listar**: Tabla con información docente
- **Crear**: Registro con datos profesionales
- **Editar**: Actualización de información
- **Eliminar**: Con confirmación
- **Ver Detalles**: Perfil completo del docente

### 4. Funcionalidades Avanzadas
- **Búsqueda en tiempo real** en todas las tablas
- **Ordenamiento** por cualquier columna
- **Paginación** configurable
- **Acciones en lote** para múltiples elementos
- **Exportación** de datos (próximamente)

## 🎨 Componentes del Sistema

### Sistema de Alertas
```tsx
import { useAlertContext } from '../contexts/AlertContext';

const { showSuccess, showError, showWarning, showInfo } = useAlertContext();

// Uso
showSuccess('Operación exitosa', 'Éxito');
showError('Error en la operación', 'Error');
```

### Sistema de Confirmaciones
```tsx
import { useConfirm } from '../hooks/useConfirm';

const { confirm } = useConfirm();

// Uso
const confirmed = await confirm({
  title: 'Eliminar Elemento',
  message: '¿Estás seguro?',
  variant: 'danger'
});
```

### DataTable Reutilizable
```tsx
import { DataTable } from '../components/ui/DataTable';

<DataTable
  data={items}
  columns={columns}
  actions={actions}
  searchable={true}
  sortable={true}
  pagination={true}
  selectable={true}
  bulkActions={bulkActions}
/>
```

## 🔒 Seguridad

### Autenticación
- **JWT Tokens** con Laravel Sanctum
- **Expiración automática** de tokens
- **Refresh tokens** para renovación
- **Logout seguro** con invalidación de tokens

### Autorización
- **Sistema de roles** (Admin, Docente, Estudiante)
- **Permisos granulares** por funcionalidad
- **Middleware de autorización** en rutas
- **Validación de acceso** en frontend

### Validación
- **Form Requests** en Laravel para validación backend
- **Validación en tiempo real** en formularios React
- **Sanitización** de datos de entrada
- **Protección CSRF** en formularios

## 🧪 Testing

### Backend Tests
```bash
# Ejecutar todos los tests
php artisan test

# Tests específicos
php artisan test --filter=StudentControllerTest
```

### Frontend Tests
```bash
# Ejecutar tests (cuando se implementen)
npm test
```

## 📊 Base de Datos

### Principales Entidades
- **users**: Usuarios del sistema
- **estudiantes**: Información de estudiantes
- **docentes**: Información de docentes
- **instituciones**: Centros educativos
- **grupos**: Grupos académicos
- **asignaturas**: Materias
- **notas**: Calificaciones
- **inasistencias**: Control de asistencia

### Relaciones
- Estudiante ↔ Acudiente (Muchos a Muchos)
- Estudiante ↔ Grupo (Muchos a Muchos con historial)
- Docente ↔ Asignatura (Muchos a Muchos)
- Usuario ↔ Rol (Muchos a Muchos)

## 🚀 Despliegue

### Backend (Laravel)
1. Configurar servidor web (Apache/Nginx)
2. Configurar PHP 8.1+
3. Configurar MySQL
4. Ejecutar `composer install --optimize-autoloader --no-dev`
5. Ejecutar `php artisan config:cache`
6. Ejecutar `php artisan route:cache`

### Frontend (React)
1. Ejecutar `npm run build`
2. Servir archivos de `dist/` desde servidor web
3. Configurar proxy para API

## 🤝 Contribución

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

Para soporte técnico o preguntas:
- **Email**: soporte@kampus.com
- **Documentación**: [docs.kampus.com](https://docs.kampus.com)
- **Issues**: [GitHub Issues](https://github.com/victorpuello/kampus-api/issues)

## 🔄 Changelog

### v1.0.0 (2025-01-XX)
- ✅ Sistema de autenticación completo
- ✅ Gestión de estudiantes y docentes
- ✅ Sistema de alertas personalizado
- ✅ DataTable avanzado con funcionalidades completas
- ✅ Interfaz responsive y moderna
- ✅ Validaciones robustas
- ✅ Sistema de confirmaciones elegante

---

**Desarrollado con ❤️ por el equipo de Kampus**
