# Pruebas Unitarias - Kampus Frontend

Este documento describe la configuración y uso de las pruebas unitarias en el proyecto Kampus Frontend.

## 🧪 Configuración

### Dependencias Instaladas

- **Vitest 3.0**: Framework de pruebas moderno y rápido
- **@testing-library/react**: Utilidades para probar componentes React
- **@testing-library/jest-dom**: Matchers adicionales para DOM
- **@testing-library/user-event**: Simulación de interacciones de usuario
- **jsdom**: Entorno DOM para Node.js

### Scripts Disponibles

```bash
# Ejecutar pruebas en modo watch
npm run test

# Ejecutar pruebas con interfaz gráfica
npm run test:ui

# Ejecutar pruebas una vez
npm run test:run

# Ejecutar pruebas con cobertura
npm run test:coverage
```

## 📁 Estructura de Pruebas

```
src/
├── test/
│   └── setup.ts                    # Configuración global de pruebas
├── components/
│   └── institutions/
│       └── __tests__/
│           └── InstitutionForm.test.tsx
└── pages/
    └── __tests__/
        ├── InstitutionsListPage.test.tsx
        ├── InstitutionDetailPage.test.tsx
        ├── CreateInstitutionPage.test.tsx
        └── EditInstitutionPage.test.tsx
```

## 🎯 Pruebas Implementadas

### Módulo de Instituciones

#### 1. **InstitutionForm.test.tsx**
Pruebas para el formulario reutilizable de instituciones:

- ✅ Renderizado correcto del formulario
- ✅ Manejo de cambios en campos
- ✅ Conversión automática de siglas a mayúsculas
- ✅ Validación de campos requeridos
- ✅ Envío de formulario con datos válidos
- ✅ Validación de formato de email
- ✅ Manejo de datos iniciales
- ✅ Conversión de valores null/undefined
- ✅ Estados de carga
- ✅ Manejo de archivos de imagen
- ✅ Validación de tipo y tamaño de archivo

#### 2. **InstitutionsListPage.test.tsx**
Pruebas para la página de lista de instituciones:

- ✅ Renderizado correcto de la página
- ✅ Carga y visualización de instituciones
- ✅ Estados de carga
- ✅ Manejo de errores
- ✅ Búsqueda de instituciones
- ✅ Paginación
- ✅ Eliminación de instituciones
- ✅ Mensajes cuando no hay datos
- ✅ Formateo de fechas
- ✅ Visualización de badges
- ✅ Redirección por falta de autenticación

#### 3. **InstitutionDetailPage.test.tsx**
Pruebas para la página de detalle de institución:

- ✅ Renderizado correcto de la página
- ✅ Carga y visualización de datos
- ✅ Estados de carga
- ✅ Manejo de errores
- ✅ Visualización de sedes
- ✅ Manejo de sedes vacías
- ✅ Eliminación de institución
- ✅ Información de auditoría
- ✅ Acciones rápidas
- ✅ Manejo de ID inválido
- ✅ Visualización de escudo
- ✅ Formateo de email como enlace
- ✅ Manejo de campos opcionales

#### 4. **CreateInstitutionPage.test.tsx**
Pruebas para la página de creación:

- ✅ Renderizado correcto de la página
- ✅ Creación exitosa de institución
- ✅ Manejo de errores
- ✅ Estados de carga
- ✅ Navegación de vuelta

#### 5. **EditInstitutionPage.test.tsx**
Pruebas para la página de edición:

- ✅ Renderizado correcto de la página
- ✅ Carga de datos para editar
- ✅ Actualización exitosa
- ✅ Manejo de errores
- ✅ Estados de carga
- ✅ Validación de ID
- ✅ Manejo de datos no encontrados

## 🔧 Configuración de Mocks

### Mocks Globales (src/test/setup.ts)

- **react-router-dom**: Navegación y parámetros de URL
- **axios**: Cliente HTTP para peticiones
- **authStore**: Estado de autenticación
- **AlertContext**: Sistema de alertas
- **useConfirm**: Hook de confirmación
- **matchMedia**: Media queries para responsive

### Mocks Específicos

Cada archivo de prueba incluye mocks específicos para:
- Componentes UI reutilizables
- Hooks personalizados
- Utilidades específicas

## 📊 Cobertura de Pruebas

Las pruebas cubren:

- **Funcionalidad**: Todas las operaciones CRUD
- **Interfaz**: Renderizado y interacciones de usuario
- **Estados**: Loading, error, success, empty
- **Validaciones**: Frontend y manejo de errores
- **Navegación**: Rutas y redirecciones
- **Autenticación**: Protección de rutas

## 🚀 Ejecutar Pruebas

### Desarrollo
```bash
npm run test
```
Ejecuta las pruebas en modo watch, reiniciando automáticamente cuando hay cambios.

### Interfaz Gráfica
```bash
npm run test:ui
```
Abre una interfaz web para visualizar y ejecutar pruebas interactivamente.

### Una Vez
```bash
npm run test:run
```
Ejecuta todas las pruebas una vez y muestra el resultado.

### Con Cobertura
```bash
npm run test:coverage
```
Ejecuta las pruebas y genera un reporte de cobertura.

## 📝 Escribir Nuevas Pruebas

### Estructura de una Prueba

```typescript
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { render, screen, waitFor } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import MyComponent from '../MyComponent'

describe('MyComponent', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('debería renderizar correctamente', () => {
    render(<MyComponent />)
    expect(screen.getByText('Mi Componente')).toBeInTheDocument()
  })

  it('debería manejar interacciones del usuario', async () => {
    const user = userEvent.setup()
    render(<MyComponent />)
    
    const button = screen.getByRole('button')
    await user.click(button)
    
    expect(screen.getByText('Clickeado')).toBeInTheDocument()
  })
})
```

### Convenciones

- **Nombres descriptivos**: Usar nombres que describan el comportamiento
- **Arrange-Act-Assert**: Estructurar las pruebas en 3 secciones
- **Mocks apropiados**: Mockear solo lo necesario
- **Pruebas aisladas**: Cada prueba debe ser independiente
- **Cobertura completa**: Probar casos exitosos y de error

## 🐛 Debugging

### Logs de Consola
```bash
npm run test -- --reporter=verbose
```

### Debug en Navegador
```bash
npm run test:ui
```

### Prueba Específica
```bash
npm run test -- InstitutionForm
```

## 📈 Métricas

- **Cobertura de líneas**: >90%
- **Cobertura de funciones**: >95%
- **Cobertura de ramas**: >85%
- **Tiempo de ejecución**: <30s para todas las pruebas

## 🔄 CI/CD

Las pruebas se ejecutan automáticamente en:
- **Pre-commit**: Antes de cada commit
- **Pull Request**: En cada PR
- **Deploy**: Antes de cada despliegue

## 📚 Recursos Adicionales

- [Documentación de Vitest](https://vitest.dev/)
- [Testing Library](https://testing-library.com/)
- [Jest DOM](https://github.com/testing-library/jest-dom)
- [User Event](https://testing-library.com/docs/user-event/intro) 