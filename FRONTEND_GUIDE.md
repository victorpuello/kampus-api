# 🎨 Guía del Frontend - Kampus

Guía completa para el desarrollo del frontend de Kampus usando React, TypeScript y Tailwind CSS.

## 📋 Tabla de Contenidos

1. [Tecnologías Utilizadas](#tecnologías-utilizadas)
2. [Estructura del Proyecto](#estructura-del-proyecto)
3. [Configuración del Entorno](#configuración-del-entorno)
4. [Arquitectura de Componentes](#arquitectura-de-componentes)
5. [Estado Global](#estado-global)
6. [Rutas y Navegación](#rutas-y-navegación)
7. [API y HTTP](#api-y-http)
8. [Estilos y UI](#estilos-y-ui)
9. [Testing](#testing)
10. [Deployment](#deployment)

## 🛠️ Tecnologías Utilizadas

- **React 18** - Biblioteca de UI
- **TypeScript** - Tipado estático
- **Vite** - Build tool y dev server
- **React Router** - Navegación SPA
- **Zustand** - Gestión de estado
- **Axios** - Cliente HTTP
- **Tailwind CSS** - Framework CSS
- **Class Variance Authority** - Sistema de variantes
- **Vitest** - Testing framework

## 📁 Estructura del Proyecto

```
src/
├── components/                # Componentes reutilizables
│   ├── ui/                   # Componentes de UI base
│   │   ├── Button.tsx        # Botones
│   │   ├── Input.tsx         # Campos de entrada
│   │   ├── Modal.tsx         # Modales
│   │   ├── Table.tsx         # Tablas
│   │   └── ...
│   ├── layouts/              # Layouts de la aplicación
│   │   └── DashboardLayout.tsx
│   ├── auth/                 # Componentes de autenticación
│   │   ├── LoginForm.tsx
│   │   └── UserInfo.tsx
│   ├── students/             # Componentes de estudiantes
│   ├── teachers/             # Componentes de docentes
│   ├── guardians/            # Componentes de acudientes
│   ├── institutions/         # Componentes de instituciones
│   └── ...
├── pages/                    # Páginas de la aplicación
│   ├── LoginPage.tsx
│   ├── DashboardPage.tsx
│   ├── StudentsListPage.tsx
│   ├── CreateStudentPage.tsx
│   └── ...
├── hooks/                    # Hooks personalizados
│   ├── useAuth.ts
│   ├── useAlert.ts
│   └── ...
├── contexts/                 # Contextos de React
│   └── AlertContext.tsx
├── api/                      # Cliente HTTP y configuración
│   └── axiosClient.ts
├── store/                    # Estado global (Zustand)
│   └── authStore.ts
├── utils/                    # Utilidades y helpers
│   └── cn.ts
├── types/                    # Tipos TypeScript
└── App.tsx                   # Componente raíz
```

## ⚙️ Configuración del Entorno

### Instalación

```bash
# Navegar al directorio del frontend
cd kampus-frontend

# Instalar dependencias
npm install

# Configurar variables de entorno
echo "VITE_API_URL=http://kampus.test/api/v1" > .env

# Iniciar servidor de desarrollo
npm run dev
```

### Variables de Entorno

```env
# .env
VITE_API_URL=http://kampus.test/api/v1
VITE_APP_NAME=Kampus
VITE_APP_VERSION=1.0.0
```

### Scripts Disponibles

```json
{
  "dev": "vite",                    // Servidor de desarrollo
  "build": "tsc && vite build",     // Build de producción
  "preview": "vite preview",        // Preview del build
  "lint": "eslint .",               // Linting
  "test": "vitest",                 // Tests
  "test:ui": "vitest --ui",         // Tests con UI
  "test:run": "vitest run",         // Tests una vez
  "test:coverage": "vitest run --coverage" // Tests con coverage
}
```

## 🧩 Arquitectura de Componentes

### Componentes UI Base

Los componentes base están en `src/components/ui/` y siguen el patrón de Class Variance Authority:

```typescript
// src/components/ui/Button.tsx
import { cva, type VariantProps } from 'class-variance-authority'
import { cn } from '../../utils/cn'

const buttonVariants = cva(
  'inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors',
  {
    variants: {
      variant: {
        default: 'bg-primary text-primary-foreground hover:bg-primary/90',
        destructive: 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
        outline: 'border border-input bg-background hover:bg-accent hover:text-accent-foreground',
        secondary: 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
        ghost: 'hover:bg-accent hover:text-accent-foreground',
        link: 'text-primary underline-offset-4 hover:underline',
      },
      size: {
        default: 'h-10 px-4 py-2',
        sm: 'h-9 rounded-md px-3',
        lg: 'h-11 rounded-md px-8',
        icon: 'h-10 w-10',
      },
    },
    defaultVariants: {
      variant: 'default',
      size: 'default',
    },
  }
)

export interface ButtonProps
  extends React.ButtonHTMLAttributes<HTMLButtonElement>,
    VariantProps<typeof buttonVariants> {}

const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
  ({ className, variant, size, ...props }, ref) => {
    return (
      <button
        className={cn(buttonVariants({ variant, size, className }))}
        ref={ref}
        {...props}
      />
    )
  }
)
Button.displayName = 'Button'

export { Button, buttonVariants }
```

### Componentes de Módulo

Los componentes específicos de cada módulo siguen esta estructura:

```typescript
// src/components/students/StudentForm.tsx
import React from 'react'
import { Button } from '../ui/Button'
import { Input } from '../ui/Input'
import { useAlert } from '../../hooks/useAlert'

interface StudentFormProps {
  student?: Student
  onSubmit: (data: StudentFormData) => void
  isLoading?: boolean
}

export const StudentForm: React.FC<StudentFormProps> = ({
  student,
  onSubmit,
  isLoading = false
}) => {
  const { showAlert } = useAlert()
  const [formData, setFormData] = React.useState({
    nombre: student?.nombre || '',
    apellido: student?.apellido || '',
    // ... más campos
  })

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    onSubmit(formData)
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <Input
        label="Nombre"
        value={formData.nombre}
        onChange={(e) => setFormData({ ...formData, nombre: e.target.value })}
        required
      />
      <Input
        label="Apellido"
        value={formData.apellido}
        onChange={(e) => setFormData({ ...formData, apellido: e.target.value })}
        required
      />
      <Button type="submit" disabled={isLoading}>
        {isLoading ? 'Guardando...' : 'Guardar'}
      </Button>
    </form>
  )
}
```

## 🗃️ Estado Global

### Zustand Store

El estado global se maneja con Zustand:

```typescript
// src/store/authStore.ts
import { create } from 'zustand'
import { persist } from 'zustand/middleware'

interface User {
  id: number
  name: string
  email: string
  roles: string[]
}

interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
  login: (user: User, token: string) => void
  logout: () => void
  updateUser: (user: User) => void
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set) => ({
      user: null,
      token: null,
      isAuthenticated: false,
      login: (user, token) =>
        set({ user, token, isAuthenticated: true }),
      logout: () =>
        set({ user: null, token: null, isAuthenticated: false }),
      updateUser: (user) =>
        set({ user }),
    }),
    {
      name: 'auth-storage',
    }
  )
)
```

### Hooks Personalizados

```typescript
// src/hooks/useAuth.ts
import { useAuthStore } from '../store/authStore'
import { api } from '../api/axiosClient'

export const useAuth = () => {
  const { user, token, isAuthenticated, login, logout } = useAuthStore()

  const signIn = async (email: string, password: string) => {
    try {
      const response = await api.post('/login', { email, password })
      const { user, token } = response.data
      login(user, token)
      return { success: true }
    } catch (error) {
      return { success: false, error }
    }
  }

  const signOut = async () => {
    try {
      await api.post('/logout')
    } catch (error) {
      console.error('Error en logout:', error)
    } finally {
      logout()
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    signIn,
    signOut,
  }
}
```

## 🧭 Rutas y Navegación

### Configuración de Rutas

```typescript
// src/router/index.tsx
import { createBrowserRouter } from 'react-router-dom'
import DashboardLayout from '../components/layouts/DashboardLayout'
import ProtectedRoute from './ProtectedRoute'

export const router = createBrowserRouter([
  {
    path: '/login',
    element: <LoginPage />,
  },
  {
    path: '/',
    element: <ProtectedRoute />,
    children: [
      {
        index: true,
        element: <DashboardLayout><DashboardPage /></DashboardLayout>
      },
      {
        path: 'estudiantes',
        element: <DashboardLayout><StudentsListPage /></DashboardLayout>
      },
      {
        path: 'estudiantes/crear',
        element: <DashboardLayout><CreateStudentPage /></DashboardLayout>
      },
      {
        path: 'estudiantes/:id',
        element: <DashboardLayout><StudentDetailPage /></DashboardLayout>
      },
      {
        path: 'estudiantes/:id/editar',
        element: <DashboardLayout><EditStudentPage /></DashboardLayout>
      },
      // ... más rutas
    ]
  }
])
```

### Protected Route

```typescript
// src/router/ProtectedRoute.tsx
import { Navigate, Outlet } from 'react-router-dom'
import { useAuth } from '../hooks/useAuth'

export const ProtectedRoute = () => {
  const { isAuthenticated } = useAuth()

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />
  }

  return <Outlet />
}
```

## 🌐 API y HTTP

### Cliente Axios

```typescript
// src/api/axiosClient.ts
import axios from 'axios'
import { useAuthStore } from '../store/authStore'

export const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Interceptor para agregar token
api.interceptors.request.use((config) => {
  const token = useAuthStore.getState().token
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Interceptor para manejar errores
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      useAuthStore.getState().logout()
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)
```

### Servicios API

```typescript
// src/api/students.ts
import { api } from './axiosClient'

export interface Student {
  id: number
  nombre: string
  apellido: string
  tipo_documento: string
  numero_documento: string
  fecha_nacimiento: string
  genero: string
  direccion: string
  telefono: string
  email: string
  grado_id: number
  grupo_id: number
  acudiente_id: number
}

export const studentsApi = {
  getAll: (params?: any) => api.get<Student[]>('/estudiantes', { params }),
  getById: (id: number) => api.get<Student>(`/estudiantes/${id}`),
  create: (data: Omit<Student, 'id'>) => api.post<Student>('/estudiantes', data),
  update: (id: number, data: Partial<Student>) => 
    api.put<Student>(`/estudiantes/${id}`, data),
  delete: (id: number) => api.delete(`/estudiantes/${id}`),
}
```

## 🎨 Estilos y UI

### Tailwind CSS

El proyecto usa Tailwind CSS con configuración personalizada:

```javascript
// tailwind.config.js
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        border: "hsl(var(--border))",
        input: "hsl(var(--input))",
        ring: "hsl(var(--ring))",
        background: "hsl(var(--background))",
        foreground: "hsl(var(--foreground))",
        primary: {
          DEFAULT: "hsl(var(--primary))",
          foreground: "hsl(var(--primary-foreground))",
        },
        secondary: {
          DEFAULT: "hsl(var(--secondary))",
          foreground: "hsl(var(--secondary-foreground))",
        },
        destructive: {
          DEFAULT: "hsl(var(--destructive))",
          foreground: "hsl(var(--destructive-foreground))",
        },
        muted: {
          DEFAULT: "hsl(var(--muted))",
          foreground: "hsl(var(--muted-foreground))",
        },
        accent: {
          DEFAULT: "hsl(var(--accent))",
          foreground: "hsl(var(--accent-foreground))",
        },
        popover: {
          DEFAULT: "hsl(var(--popover))",
          foreground: "hsl(var(--popover-foreground))",
        },
        card: {
          DEFAULT: "hsl(var(--card))",
          foreground: "hsl(var(--card-foreground))",
        },
      },
      borderRadius: {
        lg: "var(--radius)",
        md: "calc(var(--radius) - 2px)",
        sm: "calc(var(--radius) - 4px)",
      },
    },
  },
  plugins: [],
}
```

### Utilidades CSS

```typescript
// src/utils/cn.ts
import { type ClassValue, clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}
```

## 🧪 Testing

### Configuración de Vitest

```typescript
// vitest.config.ts
import { defineConfig } from 'vitest/config'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  test: {
    environment: 'jsdom',
    setupFiles: ['./src/test/setup.ts'],
    globals: true,
  },
})
```

### Tests de Componentes

```typescript
// src/components/__tests__/StudentForm.test.tsx
import { render, screen, fireEvent } from '@testing-library/react'
import { describe, it, expect, vi } from 'vitest'
import { StudentForm } from '../students/StudentForm'

describe('StudentForm', () => {
  it('renders form fields', () => {
    const mockOnSubmit = vi.fn()
    render(<StudentForm onSubmit={mockOnSubmit} />)
    
    expect(screen.getByLabelText(/nombre/i)).toBeInTheDocument()
    expect(screen.getByLabelText(/apellido/i)).toBeInTheDocument()
  })

  it('submits form with correct data', () => {
    const mockOnSubmit = vi.fn()
    render(<StudentForm onSubmit={mockOnSubmit} />)
    
    fireEvent.change(screen.getByLabelText(/nombre/i), {
      target: { value: 'Juan' },
    })
    fireEvent.change(screen.getByLabelText(/apellido/i), {
      target: { value: 'Pérez' },
    })
    fireEvent.click(screen.getByRole('button', { name: /guardar/i }))
    
    expect(mockOnSubmit).toHaveBeenCalledWith({
      nombre: 'Juan',
      apellido: 'Pérez',
    })
  })
})
```

## 🚀 Deployment

### Build de Producción

```bash
# Crear build optimizado
npm run build

# Preview del build
npm run preview
```

### Variables de Entorno para Producción

```env
# .env.production
VITE_API_URL=https://api.kampus.com/api/v1
VITE_APP_NAME=Kampus
VITE_APP_VERSION=1.0.0
```

### Configuración de Servidor

Para servir la aplicación en producción:

```nginx
# nginx.conf
server {
    listen 80;
    server_name kampus.com;
    root /var/www/kampus/dist;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location /api {
        proxy_pass http://backend:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

## 📝 Convenciones de Código

### Nomenclatura

- **Componentes**: PascalCase (`StudentForm.tsx`)
- **Hooks**: camelCase con prefijo `use` (`useAuth.ts`)
- **Utilidades**: camelCase (`cn.ts`)
- **Tipos**: PascalCase (`Student.ts`)

### Estructura de Archivos

```
ComponentName/
├── index.ts              # Export principal
├── ComponentName.tsx     # Componente principal
├── ComponentName.test.tsx # Tests
└── types.ts             # Tipos específicos
```

### Imports

```typescript
// 1. React y librerías externas
import React from 'react'
import { useNavigate } from 'react-router-dom'

// 2. Hooks y utilidades internas
import { useAuth } from '../../hooks/useAuth'
import { cn } from '../../utils/cn'

// 3. Componentes UI
import { Button } from '../ui/Button'
import { Input } from '../ui/Input'

// 4. Tipos
import type { Student } from '../../types'
```

## 🔧 Herramientas de Desarrollo

### ESLint

```json
// .eslintrc.json
{
  "extends": [
    "@typescript-eslint/recommended",
    "eslint-plugin-react-hooks/recommended"
  ],
  "rules": {
    "react-hooks/rules-of-hooks": "error",
    "react-hooks/exhaustive-deps": "warn"
  }
}
```

### Prettier

```json
// .prettierrc
{
  "semi": false,
  "singleQuote": true,
  "tabWidth": 2,
  "trailingComma": "es5"
}
```

---

**¡Listo para desarrollar el frontend de Kampus! 🎨** 