# 🎓 Kampus - Sistema de Gestión Académica

Bienvenido a **Kampus**, un sistema integral para la gestión académica de instituciones educativas. Este proyecto incluye un backend robusto en **Laravel** y un frontend moderno en **React**.

---

## 🚀 ¿Qué es Kampus?
Kampus es una plataforma diseñada para administrar estudiantes, docentes, asignaturas, grupos, calificaciones, instituciones y mucho más, con un sistema avanzado de autenticación y permisos.

- **Backend:** Laravel 10+ (API RESTful, autenticación, roles y permisos, gestión de datos académicos)
- **Frontend:** React 18 + TypeScript (SPA, UI moderna, consumo de API, manejo de sesiones y permisos)

---

## 📦 Estructura del Proyecto

```
/
├── app/                  # Código fuente del backend (Laravel)
├── kampus-frontend/      # Código fuente del frontend (React)
├── documentacion/        # Documentación centralizada del proyecto
├── database/             # Migraciones, seeders y factories
├── routes/               # Rutas de la API y web
├── tests/                # Pruebas unitarias y funcionales
├── public/               # Archivos públicos (Laravel)
├── resources/            # Vistas y recursos (Laravel)
├── ...
```

---

## 📚 Documentación
Toda la documentación del proyecto está centralizada en la carpeta [`documentacion/`](./documentacion/):
- Guías de despliegue y desarrollo
- Manuales de módulos
- Estructura de la base de datos
- API y autenticación
- Solución de problemas y más

Consulta el [README de documentación](./documentacion/README.md) para navegar por todos los manuales y guías.

---

## 🎨 Formato de Código Automático (Laravel Pint)

Este proyecto utiliza **[Laravel Pint](https://laravel.com/docs/12.x/pint)** para mantener un estilo de código consistente en todo el backend.

- **Formatea automáticamente todo el código PHP:**
  ```bash
  composer format
  ```
- **Verifica el formato sin modificar archivos:**
  ```bash
  composer format:check
  ```

Pint corrige espacios, comillas, orden de imports, comentarios y mucho más siguiendo las reglas recomendadas por Laravel.

> **Recomendación:** Ejecuta `composer format` antes de hacer un commit para mantener el código limpio y uniforme.

---

## ⚡ Inicio Rápido

### 1. Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/kampus.git
cd kampus-api
```

### 2. Backend (Laravel)
- Instala dependencias:
  ```bash
  composer install
  cp .env.example .env
  php artisan key:generate
  # Configura tu base de datos en .env
  php artisan migrate --seed
  php artisan serve
  ```

### 3. Frontend (React)
- Instala dependencias y ejecuta:
  ```bash
  cd kampus-frontend
  npm install
  npm run dev
  ```

---

## 🛡️ Autenticación y Acceso
- El sistema utiliza autenticación basada en tokens (Bearer Token).
- Los permisos y roles se gestionan desde el backend y se reflejan en la UI.
- Usuario admin por defecto: `admin@example.com` / `123456`

---

## 🤝 Contribuciones
¡Las contribuciones son bienvenidas! Consulta las guías en [`documentacion/guides/development/`](./documentacion/guides/development/) para saber cómo colaborar.

---

## 📝 Licencia
Este proyecto es de uso privado para instituciones educativas. Contacta al autor para más información.

---

> **¿Dudas o problemas?**
> Consulta la [documentación central](./documentacion/) o abre un issue. 