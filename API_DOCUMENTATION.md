# 📚 Documentación de la API - Kampus

Documentación completa de los endpoints de la API REST de Kampus.

## 🔐 Autenticación

La API utiliza Laravel Sanctum para autenticación JWT.

### Login
```http
POST /api/v1/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "123456"
}
```

**Respuesta exitosa:**
```json
{
  "token": "1|abcdefg12345...",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@example.com",
    "roles": [...]
  }
}
```

### Logout
```http
POST /api/v1/logout
Authorization: Bearer {token}
```

### Obtener Usuario Actual
```http
GET /api/v1/me
Authorization: Bearer {token}
```

## 👥 Usuarios

### Listar Usuarios
```http
GET /api/v1/users
Authorization: Bearer {token}
```

### Crear Usuario
```http
POST /api/v1/users
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Nuevo Usuario",
  "email": "usuario@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Obtener Usuario
```http
GET /api/v1/users/{id}
Authorization: Bearer {token}
```

### Actualizar Usuario
```http
PUT /api/v1/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Usuario Actualizado",
  "email": "usuario@example.com"
}
```

### Eliminar Usuario
```http
DELETE /api/v1/users/{id}
Authorization: Bearer {token}
```

## 🎓 Estudiantes

### Listar Estudiantes
```http
GET /api/v1/estudiantes
Authorization: Bearer {token}
```

**Parámetros de consulta:**
- `search`: Búsqueda por nombre, apellido o documento
- `grado_id`: Filtrar por grado
- `grupo_id`: Filtrar por grupo
- `page`: Número de página

### Crear Estudiante
```http
POST /api/v1/estudiantes
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Juan",
  "apellido": "Pérez",
  "tipo_documento": "CC",
  "numero_documento": "12345678",
  "fecha_nacimiento": "2010-05-15",
  "genero": "M",
  "direccion": "Calle 123 #45-67",
  "telefono": "3001234567",
  "email": "juan.perez@example.com",
  "grado_id": 1,
  "grupo_id": 1,
  "acudiente_id": 1
}
```

### Obtener Estudiante
```http
GET /api/v1/estudiantes/{id}
Authorization: Bearer {token}
```

### Actualizar Estudiante
```http
PUT /api/v1/estudiantes/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Juan Carlos",
  "apellido": "Pérez García"
}
```

### Eliminar Estudiante
```http
DELETE /api/v1/estudiantes/{id}
Authorization: Bearer {token}
```

## 👨‍🏫 Docentes

### Listar Docentes
```http
GET /api/v1/docentes
Authorization: Bearer {token}
```

### Crear Docente
```http
POST /api/v1/docentes
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "María",
  "apellido": "González",
  "tipo_documento": "CC",
  "numero_documento": "87654321",
  "fecha_nacimiento": "1985-03-20",
  "genero": "F",
  "direccion": "Carrera 78 #90-12",
  "telefono": "3109876543",
  "email": "maria.gonzalez@example.com",
  "especialidad": "Matemáticas",
  "titulo": "Licenciada en Matemáticas"
}
```

### Obtener Docente
```http
GET /api/v1/docentes/{id}
Authorization: Bearer {token}
```

### Actualizar Docente
```http
PUT /api/v1/docentes/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "especialidad": "Matemáticas y Física",
  "titulo": "Magíster en Educación Matemática"
}
```

### Eliminar Docente
```http
DELETE /api/v1/docentes/{id}
Authorization: Bearer {token}
```

## 🏫 Instituciones

### Listar Instituciones
```http
GET /api/v1/instituciones
Authorization: Bearer {token}
```

### Crear Institución
```http
POST /api/v1/instituciones
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Colegio San José",
  "nit": "900123456-7",
  "direccion": "Calle 15 #23-45",
  "telefono": "6012345678",
  "email": "info@colegiosanjose.edu.co",
  "sitio_web": "https://colegiosanjose.edu.co",
  "rector": "Dr. Carlos Mendoza",
  "escudo": "archivo_escudo.jpg"
}
```

### Obtener Institución
```http
GET /api/v1/instituciones/{id}
Authorization: Bearer {token}
```

### Actualizar Institución
```http
PUT /api/v1/instituciones/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Colegio San José de la Salle",
  "rector": "Dr. Carlos Mendoza López"
}
```

### Eliminar Institución
```http
DELETE /api/v1/instituciones/{id}
Authorization: Bearer {token}
```

### Obtener Sedes de Institución
```http
GET /api/v1/instituciones/{id}/sedes
Authorization: Bearer {token}
```

## 🏢 Sedes

### Listar Sedes
```http
GET /api/v1/sedes
Authorization: Bearer {token}
```

### Crear Sede
```http
POST /api/v1/sedes
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Sede Principal",
  "direccion": "Calle 15 #23-45",
  "telefono": "6012345678",
  "email": "sede.principal@colegiosanjose.edu.co",
  "institucion_id": 1
}
```

### Obtener Sede
```http
GET /api/v1/sedes/{id}
Authorization: Bearer {token}
```

### Actualizar Sede
```http
PUT /api/v1/sedes/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Sede Principal - Campus Norte",
  "telefono": "6012345679"
}
```

### Eliminar Sede
```http
DELETE /api/v1/sedes/{id}
Authorization: Bearer {token}
```

## 👨‍👩‍👧‍👦 Acudientes

### Listar Acudientes
```http
GET /api/v1/acudientes
Authorization: Bearer {token}
```

### Crear Acudiente
```http
POST /api/v1/acudientes
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Pedro",
  "apellido": "Pérez",
  "tipo_documento": "CC",
  "numero_documento": "11223344",
  "fecha_nacimiento": "1980-08-10",
  "genero": "M",
  "direccion": "Calle 123 #45-67",
  "telefono": "3001234567",
  "email": "pedro.perez@example.com",
  "parentesco": "Padre",
  "ocupacion": "Ingeniero"
}
```

### Obtener Acudiente
```http
GET /api/v1/acudientes/{id}
Authorization: Bearer {token}
```

### Actualizar Acudiente
```http
PUT /api/v1/acudientes/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "telefono": "3001234568",
  "email": "pedro.perez.nuevo@example.com"
}
```

### Eliminar Acudiente
```http
DELETE /api/v1/acudientes/{id}
Authorization: Bearer {token}
```

## 📚 Grados

### Listar Grados
```http
GET /api/v1/grados
Authorization: Bearer {token}
```

### Crear Grado
```http
POST /api/v1/grados
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Primero",
  "descripcion": "Primer grado de primaria",
  "nivel": "Primaria",
  "institucion_id": 1
}
```

### Obtener Grado
```http
GET /api/v1/grados/{id}
Authorization: Bearer {token}
```

### Actualizar Grado
```http
PUT /api/v1/grados/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Primero A",
  "descripcion": "Primer grado sección A"
}
```

### Eliminar Grado
```http
DELETE /api/v1/grados/{id}
Authorization: Bearer {token}
```

## 👥 Grupos

### Listar Grupos
```http
GET /api/v1/grupos
Authorization: Bearer {token}
```

### Crear Grupo
```http
POST /api/v1/grupos
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "1A",
  "descripcion": "Primer grado grupo A",
  "capacidad": 30,
  "grado_id": 1,
  "docente_id": 1
}
```

### Obtener Grupo
```http
GET /api/v1/grupos/{id}
Authorization: Bearer {token}
```

### Actualizar Grupo
```http
PUT /api/v1/grupos/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "1A - 2024",
  "capacidad": 35
}
```

### Eliminar Grupo
```http
DELETE /api/v1/grupos/{id}
Authorization: Bearer {token}
```

## 📖 Áreas

### Listar Áreas
```http
GET /api/v1/areas
Authorization: Bearer {token}
```

### Crear Área
```http
POST /api/v1/areas
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Matemáticas",
  "descripcion": "Área de matemáticas",
  "color": "#3B82F6",
  "institucion_id": 1
}
```

### Obtener Área
```http
GET /api/v1/areas/{id}
Authorization: Bearer {token}
```

### Actualizar Área
```http
PUT /api/v1/areas/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Matemáticas y Estadística",
  "color": "#1D4ED8"
}
```

### Eliminar Área
```http
DELETE /api/v1/areas/{id}
Authorization: Bearer {token}
```

## 📝 Asignaturas

### Listar Asignaturas
```http
GET /api/v1/asignaturas
Authorization: Bearer {token}
```

### Crear Asignatura
```http
POST /api/v1/asignaturas
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Matemáticas Básicas",
  "descripcion": "Fundamentos de matemáticas",
  "area_id": 1,
  "grado_id": 1,
  "horas_semanales": 5
}
```

### Obtener Asignatura
```http
GET /api/v1/asignaturas/{id}
Authorization: Bearer {token}
```

### Actualizar Asignatura
```http
PUT /api/v1/asignaturas/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Matemáticas Fundamentales",
  "horas_semanales": 6
}
```

### Eliminar Asignatura
```http
DELETE /api/v1/asignaturas/{id}
Authorization: Bearer {token}
```

## 🏫 Aulas

### Listar Aulas
```http
GET /api/v1/aulas
Authorization: Bearer {token}
```

### Crear Aula
```http
POST /api/v1/aulas
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Aula 101",
  "capacidad": 30,
  "ubicacion": "Primer piso",
  "sede_id": 1
}
```

### Obtener Aula
```http
GET /api/v1/aulas/{id}
Authorization: Bearer {token}
```

### Actualizar Aula
```http
PUT /api/v1/aulas/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Aula 101 - Laboratorio",
  "capacidad": 25
}
```

### Eliminar Aula
```http
DELETE /api/v1/aulas/{id}
Authorization: Bearer {token}
```

## ⏰ Franjas Horarias

### Listar Franjas Horarias
```http
GET /api/v1/franjas-horarias
Authorization: Bearer {token}
```

### Crear Franja Horaria
```http
POST /api/v1/franjas-horarias
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Primera Hora",
  "hora_inicio": "07:00:00",
  "hora_fin": "08:00:00",
  "institucion_id": 1
}
```

### Obtener Franja Horaria
```http
GET /api/v1/franjas-horarias/{id}
Authorization: Bearer {token}
```

### Actualizar Franja Horaria
```http
PUT /api/v1/franjas-horarias/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "Primera Hora - Mañana",
  "hora_inicio": "07:30:00",
  "hora_fin": "08:30:00"
}
```

### Eliminar Franja Horaria
```http
DELETE /api/v1/franjas-horarias/{id}
Authorization: Bearer {token}
```

## 📅 Asignaciones

### Listar Asignaciones
```http
GET /api/v1/asignaciones
Authorization: Bearer {token}
```

### Crear Asignación
```http
POST /api/v1/asignaciones
Authorization: Bearer {token}
Content-Type: application/json

{
  "docente_id": 1,
  "asignatura_id": 1,
  "grupo_id": 1,
  "aula_id": 1,
  "franja_horaria_id": 1,
  "dia_semana": "Lunes"
}
```

### Obtener Asignación
```http
GET /api/v1/asignaciones/{id}
Authorization: Bearer {token}
```

### Actualizar Asignación
```http
PUT /api/v1/asignaciones/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "aula_id": 2,
  "franja_horaria_id": 2
}
```

### Eliminar Asignación
```http
DELETE /api/v1/asignaciones/{id}
Authorization: Bearer {token}
```

## 📊 Años Académicos

### Listar Años
```http
GET /api/v1/anios
Authorization: Bearer {token}
```

### Crear Año
```http
POST /api/v1/anios
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "2024",
  "fecha_inicio": "2024-01-15",
  "fecha_fin": "2024-11-30",
  "estado": "activo",
  "institucion_id": 1
}
```

### Obtener Año
```http
GET /api/v1/anios/{id}
Authorization: Bearer {token}
```

### Actualizar Año
```http
PUT /api/v1/anios/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "nombre": "2024-2025",
  "estado": "finalizado"
}
```

### Eliminar Año
```http
DELETE /api/v1/anios/{id}
Authorization: Bearer {token}
```

## 🔄 Respuestas de Error

### Error de Validación (422)
```json
{
  "message": "Los datos proporcionados no son válidos.",
  "errors": {
    "email": ["El campo email es obligatorio."],
    "password": ["La contraseña debe tener al menos 6 caracteres."]
  }
}
```

### Error de Autenticación (401)
```json
{
  "message": "No autenticado."
}
```

### Error de Autorización (403)
```json
{
  "message": "No autorizado."
}
```

### Error No Encontrado (404)
```json
{
  "message": "Recurso no encontrado."
}
```

### Error del Servidor (500)
```json
{
  "message": "Error interno del servidor."
}
```

## 📋 Códigos de Estado HTTP

- `200` - OK: Solicitud exitosa
- `201` - Created: Recurso creado exitosamente
- `400` - Bad Request: Solicitud malformada
- `401` - Unauthorized: No autenticado
- `403` - Forbidden: No autorizado
- `404` - Not Found: Recurso no encontrado
- `422` - Unprocessable Entity: Error de validación
- `500` - Internal Server Error: Error del servidor

## 🔧 Headers Requeridos

```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}
```

## 📝 Notas Importantes

1. **Autenticación**: Todas las rutas (excepto login) requieren token de autenticación
2. **Validación**: Los datos se validan automáticamente en el backend
3. **Paginación**: Las listas incluyen paginación automática
4. **Relaciones**: Los recursos incluyen relaciones cargadas automáticamente
5. **Soft Deletes**: Algunos recursos usan eliminación suave
6. **Archivos**: Los archivos se manejan con upload automático 