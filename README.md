# 🎓 Kampus API - Sistema de Gestión Académica

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![React](https://img.shields.io/badge/React-18.x-blue.svg)](https://reactjs.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-blue.svg)](https://www.typescriptlang.org)
[![PHP](https://img.shields.io/badge/PHP-8.2+-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Sistema completo de gestión académica desarrollado con **Laravel 12** para el backend API y **React 18** con **TypeScript** para el frontend. Diseñado para instituciones educativas que necesitan gestionar estudiantes, docentes, asignaturas, calificaciones y más.

## 🚀 Características Principales

### 📚 Gestión Académica
- **Estudiantes**: Registro completo con información personal y académica
- **Docentes**: Gestión de profesores y sus asignaciones
- **Asignaturas**: Organización por áreas y grados académicos
- **Grupos**: Clasificación de estudiantes por grupos de clase
- **Calificaciones**: Sistema completo de notas y evaluaciones
- **Horarios**: Gestión de franjas horarias y asignaciones

### 🏫 Gestión Institucional
- **Instituciones**: Soporte multi-institucional
- **Años Académicos**: Gestión de períodos escolares
- **Grados**: Organización por niveles educativos
- **Áreas**: Categorización de asignaturas por áreas de conocimiento

### 🔐 Seguridad y Autenticación
- **Laravel Sanctum**: Autenticación API con tokens
- **Roles y Permisos**: Sistema granular de autorización
- **Acudientes**: Gestión de responsables de estudiantes
- **Usuarios**: Control de acceso por roles

### 📱 Frontend Moderno
- **React 18** con **TypeScript** para type safety
- **React Router** para navegación SPA
- **Tailwind CSS** para diseño responsive
- **Zustand** para gestión de estado
- **Axios** para comunicación con API

## 🛠️ Tecnologías Utilizadas

### Backend (Laravel 12)
- **PHP 8.2+** - Lenguaje de programación
- **Laravel Framework 12** - Framework PHP
- **Laravel Sanctum** - Autenticación API
- **MySQL/PostgreSQL** - Base de datos
- **Eloquent ORM** - Mapeo objeto-relacional
- **Laravel Migrations** - Control de versiones de BD
- **Laravel Seeders** - Datos de prueba
- **PHPUnit** - Testing

### Frontend (React 18)
- **React 18** - Biblioteca de UI
- **TypeScript 5** - Tipado estático
- **React Router DOM** - Enrutamiento
- **Tailwind CSS** - Framework CSS
- **Zustand** - Gestión de estado
- **Axios** - Cliente HTTP
- **Vite** - Build tool
- **ESLint** - Linting

## 📋 Requisitos del Sistema

### Backend
- PHP >= 8.2
- Composer
- MySQL >= 8.0 o PostgreSQL >= 13
- Node.js >= 18 (para compilar assets)

### Frontend
- Node.js >= 18
- npm o yarn

## 🚀 Instalación y Configuración

### 1. Clonar el Repositorio
```bash
git clone https://github.com/victorpuello/kampus-api.git
cd kampus-api
```

### 2. Configurar Backend (Laravel)

#### Instalar Dependencias PHP
```bash
composer install
```

#### Configurar Variables de Entorno
```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con la configuración de tu base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kampus_api
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

#### Ejecutar Migraciones y Seeders
```bash
php artisan migrate
php artisan db:seed
```

#### Instalar Dependencias de Desarrollo
```bash
npm install
```

### 3. Configurar Frontend (React)

#### Navegar al Directorio Frontend
```bash
cd kampus-frontend
```

#### Instalar Dependencias
```bash
npm install
```

#### Configurar Variables de Entorno
Crear archivo `.env` en `kampus-frontend/`:
```env
VITE_API_URL=http://localhost:8000/api/v1
```

### 4. Ejecutar el Proyecto

#### Backend (Terminal 1)
```bash
# En el directorio raíz
php artisan serve
```

#### Frontend (Terminal 2)
```bash
# En el directorio kampus-frontend
npm run dev
```

## 📚 API Endpoints

### Autenticación
- `POST /api/v1/login` - Iniciar sesión
- `POST /api/v1/logout` - Cerrar sesión

### Gestión de Usuarios
- `GET /api/v1/users` - Listar usuarios
- `POST /api/v1/users` - Crear usuario
- `GET /api/v1/users/{id}` - Ver usuario
- `PUT /api/v1/users/{id}` - Actualizar usuario
- `DELETE /api/v1/users/{id}` - Eliminar usuario

### Gestión de Estudiantes
- `GET /api/v1/estudiantes` - Listar estudiantes
- `POST /api/v1/estudiantes` - Crear estudiante
- `GET /api/v1/estudiantes/{id}` - Ver estudiante
- `PUT /api/v1/estudiantes/{id}` - Actualizar estudiante
- `DELETE /api/v1/estudiantes/{id}` - Eliminar estudiante

### Gestión de Docentes
- `GET /api/v1/docentes` - Listar docentes
- `POST /api/v1/docentes` - Crear docente
- `GET /api/v1/docentes/{id}` - Ver docente
- `PUT /api/v1/docentes/{id}` - Actualizar docente
- `DELETE /api/v1/docentes/{id}` - Eliminar docente

### Gestión Académica
- `GET /api/v1/instituciones` - Gestión de instituciones
- `GET /api/v1/anios` - Gestión de años académicos
- `GET /api/v1/grados` - Gestión de grados
- `GET /api/v1/areas` - Gestión de áreas
- `GET /api/v1/asignaturas` - Gestión de asignaturas
- `GET /api/v1/grupos` - Gestión de grupos
- `GET /api/v1/acudientes` - Gestión de acudientes
- `GET /api/v1/aulas` - Gestión de aulas
- `GET /api/v1/franjas-horarias` - Gestión de horarios
- `GET /api/v1/asignaciones` - Gestión de asignaciones

## 🗄️ Estructura de Base de Datos

El sistema incluye más de **30 tablas** que cubren:

### Entidades Principales
- **users** - Usuarios del sistema
- **estudiantes** - Información de estudiantes
- **docentes** - Información de docentes
- **acudientes** - Responsables de estudiantes
- **instituciones** - Centros educativos

### Gestión Académica
- **anios** - Años académicos
- **periodos** - Períodos escolares
- **grados** - Niveles educativos
- **areas** - Áreas de conocimiento
- **asignaturas** - Materias académicas
- **grupos** - Grupos de clase
- **aulas** - Espacios físicos

### Calificaciones y Evaluaciones
- **notas** - Calificaciones de estudiantes
- **definitivas_asignatura** - Promedios por asignatura
- **definitivas_periodo** - Promedios por período
- **definitivas_finales** - Promedios finales
- **inasistencias** - Control de asistencia

### Horarios y Asignaciones
- **franjas_horarias** - Bloques de tiempo
- **asignaciones** - Docentes asignados a grupos/asignaturas
- **horarios** - Programación de clases

## 🧪 Testing

### Ejecutar Tests del Backend
```bash
php artisan test
```

### Ejecutar Tests Específicos
```bash
php artisan test --filter=StudentControllerTest
```

## 📦 Comandos Útiles

### Backend
```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Generar documentación API
php artisan l5-swagger:generate

# Ejecutar seeders específicos
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=TestingDatabaseSeeder
```

### Frontend
```bash
# Construir para producción
npm run build

# Preview de producción
npm run preview

# Linting
npm run lint
```

## 🔧 Configuración de Desarrollo

### Variables de Entorno Backend (.env)
```env
APP_NAME="Kampus API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kampus_api
DB_USERNAME=root
DB_PASSWORD=

CORS_ALLOWED_ORIGINS=http://localhost:5173
```

### Variables de Entorno Frontend (.env)
```env
VITE_API_URL=http://localhost:8000/api/v1
VITE_APP_NAME="Kampus"
```

## 📱 Características del Frontend

### Páginas Implementadas
- **Login** - Autenticación de usuarios
- **Dashboard** - Panel principal
- **Estudiantes** - Lista, creación, edición y detalle
- **Layout Responsive** - Diseño adaptable

### Componentes Principales
- **DashboardLayout** - Layout principal con navegación
- **StudentForm** - Formulario de estudiantes
- **ProtectedRoute** - Rutas protegidas
- **AuthStore** - Gestión de estado de autenticación

## 🔐 Seguridad

- **Laravel Sanctum** para autenticación API
- **CORS** configurado para desarrollo
- **Validación** de datos en requests
- **Autorización** basada en roles y permisos
- **Sanitización** de inputs

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para detalles.

## 👨‍💻 Autor

**Victor Puello**
- GitHub: [@victorpuello](https://github.com/victorpuello)

## 🙏 Agradecimientos

- [Laravel](https://laravel.com) - Framework PHP
- [React](https://reactjs.org) - Biblioteca de UI
- [Tailwind CSS](https://tailwindcss.com) - Framework CSS
- [Vite](https://vitejs.dev) - Build tool

---

⭐ Si este proyecto te ayuda, ¡dale una estrella en GitHub!
