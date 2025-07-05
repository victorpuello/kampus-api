# 🚀 Guía del Desarrollador - Kampus API

Esta guía está diseñada para ayudar a nuevos desarrolladores a entender, configurar y contribuir al proyecto Kampus API.

## 📋 Tabla de Contenidos

1. [Visión General del Proyecto](#visión-general-del-proyecto)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Configuración del Entorno de Desarrollo](#configuración-del-entorno-de-desarrollo)
4. [Estructura del Código](#estructura-del-código)
5. [Flujo de Trabajo de Desarrollo](#flujo-de-trabajo-de-desarrollo)
6. [Convenciones de Código](#convenciones-de-código)
7. [Testing](#testing)
8. [API Documentation](#api-documentation)
9. [Troubleshooting](#troubleshooting)
10. [Contribución](#contribución)

## 🎯 Visión General del Proyecto

Kampus es un sistema de gestión académica completo que incluye:

- **Backend**: API REST con Laravel 12 y Sanctum para autenticación
- **Frontend**: Aplicación React con TypeScript y Tailwind CSS
- **Base de Datos**: MySQL con migraciones y seeders
- **Autenticación**: Sistema JWT con roles y permisos

### Funcionalidades Principales

- ✅ Gestión de estudiantes, docentes y acudientes
- ✅ Administración de instituciones y sedes
- ✅ Control de grados, grupos y asignaturas
- ✅ Sistema de notas y calificaciones
- ✅ Gestión de horarios y aulas
- ✅ Control de asistencia e inasistencias
- ✅ Sistema de roles y permisos

## 🏗️ Arquitectura del Sistema

### Backend (Laravel)

```
app/
├── Http/
│   ├── Controllers/Api/V1/    # Controladores de la API
│   ├── Requests/              # Validación de formularios
│   ├── Resources/             # Transformación de datos
│   └── Middleware/            # Middlewares personalizados
├── Models/                    # Modelos Eloquent
├── Services/                  # Lógica de negocio
└── Traits/                    # Traits reutilizables
```

### Frontend (React)

```
src/
├── components/                # Componentes reutilizables
│   ├── ui/                   # Componentes de UI base
│   ├── layouts/              # Layouts de la aplicación
│   └── [module]/             # Componentes específicos por módulo
├── pages/                    # Páginas de la aplicación
├── hooks/                    # Hooks personalizados
├── contexts/                 # Contextos de React
├── api/                      # Cliente HTTP y configuración
├── store/                    # Estado global (Zustand)
└── utils/                    # Utilidades y helpers
```

## ⚙️ Configuración del Entorno de Desarrollo

### Prerrequisitos

- **PHP 8.2+**
- **Composer 2.0+**
- **Node.js 18+**
- **MySQL 8.0+**
- **XAMPP** (recomendado para desarrollo local)

### 1. Configuración Inicial

```bash
# Clonar el repositorio
git clone https://github.com/victorpuello/kampus-api.git
cd kampus-api

# Instalar dependencias del backend
composer install

# Configurar variables de entorno
cp .env.example .env
php artisan key:generate
```

### 2. Configuración de la Base de Datos

Editar `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kampus_db
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# Crear la base de datos
php artisan migrate:fresh --seed
```

### 3. Configuración del Frontend

```bash
# Navegar al directorio del frontend
cd kampus-frontend

# Instalar dependencias
npm install

# Configurar variables de entorno
echo "VITE_API_URL=http://kampus.test/api/v1" > .env
```

### 4. Configuración del Host Virtual (Opcional)

Para desarrollo local, configurar un host virtual:

1. Editar `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 kampus.test
```

2. Configurar XAMPP para el dominio `kampus.test`

### 5. Iniciar Servidores

```bash
# Terminal 1 - Backend
php artisan serve --host=kampus.test --port=80

# Terminal 2 - Frontend
cd kampus-frontend
npm run dev
```

## 📁 Estructura del Código

### Modelos (Backend)

Los modelos principales incluyen:

- **User**: Usuarios del sistema con roles y permisos
- **Institucion**: Centros educativos
- **Sede**: Sedes de las instituciones
- **Estudiante**: Estudiantes con información académica
- **Docente**: Personal docente
- **Acudiente**: Acudientes de estudiantes
- **Grado**: Niveles académicos
- **Grupo**: Grupos de estudiantes
- **Area**: Áreas de conocimiento
- **Asignatura**: Materias académicas

### Controladores (Backend)

Todos los controladores siguen el patrón REST:

```php
// Ejemplo de controlador típico
class StudentController extends Controller
{
    public function index()     // GET /api/v1/estudiantes
    public function store()     // POST /api/v1/estudiantes
    public function show()      // GET /api/v1/estudiantes/{id}
    public function update()    // PUT /api/v1/estudiantes/{id}
    public function destroy()   // DELETE /api/v1/estudiantes/{id}
}
```

### Componentes (Frontend)

Los componentes están organizados por módulos:

```typescript
// Ejemplo de componente típico
interface Student {
  id: number;
  nombre: string;
  apellido: string;
  // ... más propiedades
}

const StudentForm: React.FC<StudentFormProps> = ({ student, onSubmit }) => {
  // Lógica del componente
};
```

## 🔄 Flujo de Trabajo de Desarrollo

### 1. Crear una Nueva Rama

```bash
git checkout -b feature/nueva-funcionalidad
```

### 2. Desarrollo

- Implementar cambios en el backend (Laravel)
- Implementar cambios en el frontend (React)
- Escribir tests para nuevas funcionalidades

### 3. Testing

```bash
# Backend tests
php artisan test

# Frontend tests
cd kampus-frontend
npm run test
```

### 4. Commit y Push

```bash
git add .
git commit -m "feat: agregar nueva funcionalidad"
git push origin feature/nueva-funcionalidad
```

### 5. Pull Request

Crear un PR en GitHub con:
- Descripción clara de los cambios
- Tests incluidos
- Documentación actualizada

## 📝 Convenciones de Código

### Backend (PHP/Laravel)

```php
// Nombres de clases: PascalCase
class StudentController extends Controller

// Nombres de métodos: camelCase
public function getStudentById($id)

// Nombres de variables: camelCase
$studentName = 'Juan';

// Nombres de constantes: UPPER_SNAKE_CASE
const MAX_STUDENTS = 30;
```

### Frontend (TypeScript/React)

```typescript
// Nombres de componentes: PascalCase
const StudentForm: React.FC<StudentFormProps>

// Nombres de funciones: camelCase
const handleSubmit = (data: FormData) => {}

// Nombres de variables: camelCase
const studentName = 'Juan';

// Nombres de constantes: UPPER_SNAKE_CASE
const MAX_STUDENTS = 30;
```

### Commits

Usar [Conventional Commits](https://www.conventionalcommits.org/):

```
feat: agregar nueva funcionalidad
fix: corregir bug en autenticación
docs: actualizar documentación
style: formatear código
refactor: refactorizar componente
test: agregar tests
chore: actualizar dependencias
```

## 🧪 Testing

### Backend Tests

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar tests específicos
php artisan test --filter=StudentControllerTest

# Ejecutar tests con coverage
php artisan test --coverage
```

### Frontend Tests

```bash
# Ejecutar tests
npm run test

# Ejecutar tests en modo watch
npm run test:watch

# Ejecutar tests con UI
npm run test:ui

# Ejecutar tests con coverage
npm run test:coverage
```

## 📚 API Documentation

### Autenticación

```bash
# Login
POST /api/v1/login
{
  "email": "admin@example.com",
  "password": "123456"
}

# Logout
POST /api/v1/logout
Authorization: Bearer {token}

# Obtener usuario actual
GET /api/v1/me
Authorization: Bearer {token}
```

### Endpoints Principales

```bash
# Estudiantes
GET    /api/v1/estudiantes
POST   /api/v1/estudiantes
GET    /api/v1/estudiantes/{id}
PUT    /api/v1/estudiantes/{id}
DELETE /api/v1/estudiantes/{id}

# Docentes
GET    /api/v1/docentes
POST   /api/v1/docentes
GET    /api/v1/docentes/{id}
PUT    /api/v1/docentes/{id}
DELETE /api/v1/docentes/{id}

# Instituciones
GET    /api/v1/instituciones
POST   /api/v1/instituciones
GET    /api/v1/instituciones/{id}
PUT    /api/v1/instituciones/{id}
DELETE /api/v1/instituciones/{id}
```

## 🔧 Troubleshooting

### Problemas Comunes

#### 1. Error de CORS

```php
// En config/cors.php
'allowed_origins' => ['http://localhost:5173', 'http://kampus.test:5173'],
'supports_credentials' => true,
```

#### 2. Error de Autenticación

```bash
# Limpiar caché
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

#### 3. Error de Base de Datos

```bash
# Recrear base de datos
php artisan migrate:fresh --seed
```

#### 4. Error de Dependencias Frontend

```bash
# Limpiar node_modules
rm -rf node_modules package-lock.json
npm install
```

### Logs y Debugging

```bash
# Ver logs de Laravel
tail -f storage/logs/laravel.log

# Debug con Laravel Pail
php artisan pail
```

## 🤝 Contribución

### Antes de Contribuir

1. **Leer la documentación** completa
2. **Entender la arquitectura** del proyecto
3. **Revisar issues existentes** en GitHub
4. **Discutir cambios grandes** antes de implementar

### Proceso de Contribución

1. **Fork** el repositorio
2. **Crear rama** para tu feature
3. **Implementar** cambios
4. **Escribir tests** para nuevas funcionalidades
5. **Actualizar documentación** si es necesario
6. **Crear Pull Request** con descripción detallada

### Checklist para PRs

- [ ] Código sigue las convenciones del proyecto
- [ ] Tests pasan
- [ ] Documentación actualizada
- [ ] No hay conflictos de merge
- [ ] Descripción clara de los cambios

### Contacto

Para dudas o preguntas:
- Crear un issue en GitHub
- Contactar al equipo de desarrollo
- Revisar la documentación existente

---

**¡Gracias por contribuir al proyecto Kampus! 🎓** 