# Sistema de Diseño - Kampus

Este documento describe el sistema de diseño utilizado en el proyecto Kampus, basado en el estilo visual exitoso del módulo de estudiantes.

## 🎨 Principios de Diseño

### Consistencia Visual
- **Colores**: Paleta basada en Tailwind CSS con indigo como color primario
- **Tipografía**: Inter como fuente principal
- **Espaciado**: Sistema de espaciado consistente usando Tailwind
- **Bordes**: Bordes redondeados (`rounded-md`) para todos los elementos

### Responsive Design
- **Mobile-first**: Diseño optimizado para móviles primero
- **Grid adaptativo**: `grid-cols-1 sm:grid-cols-2` para formularios
- **Flexbox**: Para layouts complejos y alineaciones

## 🧩 Componentes del Sistema

### FormContainer
Contenedor principal para formularios que maneja:
- Layout de grid responsivo
- Manejo de errores
- Espaciado consistente

```tsx
import { FormContainer } from '../components/ui';

<FormContainer onSubmit={handleSubmit} error={error}>
  {/* Campos del formulario */}
</FormContainer>
```

### FormField
Campo de entrada de texto con:
- Label consistente
- Validación visual
- Estados de error y disabled
- Soporte para diferentes tipos de input

```tsx
import { FormField } from '../components/ui';

<FormField
  label="Nombre"
  name="nombre"
  type="text"
  required
  value={formData.nombre}
  onChange={handleChange}
  placeholder="Ingrese el nombre"
/>
```

### FormSelect
Campo de selección con:
- Opciones dinámicas
- Placeholder opcional
- Validación visual

```tsx
import { FormSelect } from '../components/ui';

<FormSelect
  label="Estado"
  name="estado"
  required
  value={formData.estado}
  onChange={handleChange}
  options={[
    { value: 'activo', label: 'Activo' },
    { value: 'inactivo', label: 'Inactivo' }
  ]}
/>
```

### FormActions
Botones de acción del formulario:
- Botón de cancelar (secundario)
- Botón de enviar (primario)
- Estados de loading
- Responsive design

```tsx
import { FormActions } from '../components/ui';

<FormActions
  onCancel={handleCancel}
  onSubmit={() => {}}
  loading={loading}
  submitText="Guardar"
  cancelText="Cancelar"
  className="col-span-full"
/>
```

### PageHeader
Encabezado de página consistente:
- Título y descripción
- Acciones opcionales
- Layout responsive

```tsx
import { PageHeader } from '../components/ui';

<PageHeader
  title="Crear Estudiante"
  description="Complete el formulario para registrar un nuevo estudiante"
>
  <Button>Acción</Button>
</PageHeader>
```

## 🎯 Patrones de Uso

### Estructura de Página Típica

```tsx
const MyPage = () => {
  return (
    <div className="space-y-6">
      <PageHeader
        title="Título de la Página"
        description="Descripción opcional"
      />
      
      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">
            Sección del Formulario
          </h2>
        </CardHeader>
        <CardBody>
          <FormContainer onSubmit={handleSubmit} error={error}>
            <FormField
              label="Campo 1"
              name="campo1"
              required
              value={formData.campo1}
              onChange={handleChange}
            />
            <FormSelect
              label="Campo 2"
              name="campo2"
              value={formData.campo2}
              onChange={handleChange}
              options={options}
            />
            <FormActions
              onCancel={handleCancel}
              onSubmit={() => {}}
              loading={loading}
            />
          </FormContainer>
        </CardBody>
      </Card>
    </div>
  );
};
```

### Manejo de Estados

```tsx
const [loading, setLoading] = useState(false);
const [error, setError] = useState<string | null>(null);
const [formData, setFormData] = useState({
  // datos del formulario
});

const handleSubmit = async (e: React.FormEvent) => {
  e.preventDefault();
  setLoading(true);
  setError(null);

  try {
    // Lógica de envío
    showSuccess('Operación exitosa');
  } catch (err: any) {
    const errorMessage = err.response?.data?.message || 'Error genérico';
    setError(errorMessage);
    showError(errorMessage);
  } finally {
    setLoading(false);
  }
};
```

## 🎨 Clases CSS Clave

### Grid y Layout
- `grid grid-cols-1 gap-6 sm:grid-cols-2` - Grid responsivo para formularios
- `space-y-6` - Espaciado vertical consistente
- `flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3` - Botones de acción

### Campos de Formulario
- `block text-sm font-medium text-gray-700` - Labels
- `mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm` - Inputs
- `text-red-600` - Texto de error

### Botones
- `rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50` - Botón secundario
- `inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700` - Botón primario

### Estados
- `disabled:opacity-50 disabled:cursor-not-allowed` - Estados disabled
- `border-red-300 focus:border-red-500 focus:ring-red-500` - Estados de error

## 📱 Responsive Design

### Breakpoints
- **Mobile**: `< 640px` - Una columna
- **Tablet**: `640px - 1024px` - Dos columnas
- **Desktop**: `> 1024px` - Dos columnas con más espacio

### Adaptaciones
- Botones apilados en móvil, lado a lado en desktop
- Grid de una columna en móvil, dos en desktop
- Texto y espaciado ajustados para cada breakpoint

## 🔧 Implementación

### Importar Componentes
```tsx
import { 
  FormContainer, 
  FormField, 
  FormSelect, 
  FormActions, 
  PageHeader,
  Card,
  CardHeader,
  CardBody 
} from '../components/ui';
```

### Configuración de Tailwind
El sistema utiliza las siguientes configuraciones de Tailwind:
- Fuente: Inter
- Colores primarios: Indigo
- Bordes redondeados: `rounded-md`
- Sombras: `shadow-sm`

## 📋 Checklist de Implementación

Para mantener la consistencia, asegúrate de:

- [ ] Usar `FormContainer` para todos los formularios
- [ ] Implementar `FormField` y `FormSelect` para campos
- [ ] Incluir `FormActions` con estados de loading
- [ ] Usar `PageHeader` para encabezados de página
- [ ] Manejar errores con el sistema de alertas
- [ ] Implementar responsive design
- [ ] Seguir el patrón de estados (loading, error, success)
- [ ] Usar las clases CSS definidas en este documento

## 🎯 Beneficios

- **Consistencia**: Todos los formularios se ven y funcionan igual
- **Mantenibilidad**: Cambios centralizados en componentes
- **Productividad**: Desarrollo más rápido con componentes reutilizables
- **UX**: Experiencia de usuario consistente en toda la aplicación
- **Accesibilidad**: Componentes optimizados para accesibilidad 