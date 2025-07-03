import { describe, it, expect, vi, beforeEach } from 'vitest'
import { render, screen, fireEvent, waitFor } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { InstitutionForm } from '../InstitutionForm'

// Mock de los componentes UI
vi.mock('../../ui/Button', () => ({
  Button: ({ children, onClick, disabled, type }: any) => (
    <button onClick={onClick} disabled={disabled} type={type}>
      {children}
    </button>
  ),
}))

vi.mock('../../ui/Input', () => ({
  Input: ({ value, onChange, placeholder, error, id }: any) => (
    <input
      id={id}
      value={value}
      onChange={onChange}
      placeholder={placeholder}
      className={error ? 'error' : ''}
    />
  ),
}))

vi.mock('../../ui/Card', () => ({
  Card: ({ children }: any) => <div data-testid="card">{children}</div>,
  CardHeader: ({ children }: any) => <div data-testid="card-header">{children}</div>,
  CardBody: ({ children }: any) => <div data-testid="card-body">{children}</div>,
}))

vi.mock('../../ui/Alert', () => ({
  Alert: ({ message }: any) => <div data-testid="alert">{message}</div>,
}))

describe('InstitutionForm', () => {
  const mockOnSubmit = vi.fn()
  const defaultProps = {
    onSubmit: mockOnSubmit,
    isLoading: false,
    submitLabel: 'Guardar Institución',
  }

  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('renderiza el formulario correctamente', () => {
    render(<InstitutionForm {...defaultProps} />)

    expect(screen.getByText('Información de la Institución')).toBeInTheDocument()
    expect(screen.getByLabelText(/Nombre de la Institución/)).toBeInTheDocument()
    expect(screen.getByLabelText(/Siglas/)).toBeInTheDocument()
    expect(screen.getByLabelText(/Slogan/)).toBeInTheDocument()
    expect(screen.getByLabelText(/Código DANE/)).toBeInTheDocument()
    expect(screen.getByLabelText(/Resolución de Aprobación/)).toBeInTheDocument()
    expect(screen.getByLabelText(/Dirección/)).toBeInTheDocument()
    expect(screen.getByLabelText(/Teléfono/)).toBeInTheDocument()
    expect(screen.getByLabelText(/Email/)).toBeInTheDocument()
    expect(screen.getByLabelText(/Rector/)).toBeInTheDocument()
    expect(screen.getByLabelText(/Escudo de la Institución/)).toBeInTheDocument()
  })

  it('maneja cambios en los campos del formulario', async () => {
    const user = userEvent.setup()
    render(<InstitutionForm {...defaultProps} />)

    const nombreInput = screen.getByLabelText(/Nombre de la Institución/)
    const siglasInput = screen.getByLabelText(/Siglas/)

    await user.type(nombreInput, 'Instituto Test')
    await user.type(siglasInput, 'IT')

    expect(nombreInput).toHaveValue('Instituto Test')
    expect(siglasInput).toHaveValue('IT')
  })

  it('convierte siglas a mayúsculas automáticamente', async () => {
    const user = userEvent.setup()
    render(<InstitutionForm {...defaultProps} />)

    const siglasInput = screen.getByLabelText(/Siglas/)
    await user.type(siglasInput, 'it')

    expect(siglasInput).toHaveValue('IT')
  })

  it('valida campos requeridos al enviar', async () => {
    const user = userEvent.setup()
    render(<InstitutionForm {...defaultProps} />)

    const submitButton = screen.getByText('Guardar Institución')
    await user.click(submitButton)

    // El formulario no debería enviarse sin los campos requeridos
    expect(mockOnSubmit).not.toHaveBeenCalled()
  })

  it('envía el formulario con datos válidos', async () => {
    const user = userEvent.setup()
    render(<InstitutionForm {...defaultProps} />)

    // Llenar campos requeridos
    await user.type(screen.getByLabelText(/Nombre de la Institución/), 'Instituto Test')
    await user.type(screen.getByLabelText(/Siglas/), 'IT')

    // Llenar campos opcionales
    await user.type(screen.getByLabelText(/Slogan/), 'Educando para el futuro')
    await user.type(screen.getByLabelText(/Código DANE/), '123456789')
    await user.type(screen.getByLabelText(/Resolución de Aprobación/), 'Resolución 1234 de 2020')
    await user.type(screen.getByLabelText(/Dirección/), 'Calle 123 #45-67')
    await user.type(screen.getByLabelText(/Teléfono/), '3001234567')
    await user.type(screen.getByLabelText(/Email/), 'info@institucion.edu.co')
    await user.type(screen.getByLabelText(/Rector/), 'Dr. Juan Pérez')

    const submitButton = screen.getByText('Guardar Institución')
    await user.click(submitButton)

    expect(mockOnSubmit).toHaveBeenCalledWith({
      nombre: 'Instituto Test',
      siglas: 'IT',
      slogan: 'Educando para el futuro',
      dane: '123456789',
      resolucion_aprobacion: 'Resolución 1234 de 2020',
      direccion: 'Calle 123 #45-67',
      telefono: '3001234567',
      email: 'info@institucion.edu.co',
      rector: 'Dr. Juan Pérez',
      escudo: '',
    })
  })

  it('valida formato de email', async () => {
    const user = userEvent.setup()
    render(<InstitutionForm {...defaultProps} />)

    const emailInput = screen.getByLabelText(/Email/)
    await user.type(emailInput, 'email-invalido')

    const submitButton = screen.getByText('Guardar Institución')
    await user.click(submitButton)

    expect(mockOnSubmit).not.toHaveBeenCalled()
  })

  it('maneja datos iniciales correctamente', () => {
    const initialData = {
      nombre: 'Instituto Existente',
      siglas: 'IE',
      slogan: 'Slogan existente',
      dane: '987654321',
      resolucion_aprobacion: 'Resolución existente',
      direccion: 'Dirección existente',
      telefono: '3009876543',
      email: 'existente@institucion.edu.co',
      rector: 'Dr. Existente',
      escudo: 'escudo.png',
    }

    render(<InstitutionForm {...defaultProps} initialData={initialData} />)

    expect(screen.getByLabelText(/Nombre de la Institución/)).toHaveValue('Instituto Existente')
    expect(screen.getByLabelText(/Siglas/)).toHaveValue('IE')
    expect(screen.getByLabelText(/Slogan/)).toHaveValue('Slogan existente')
    expect(screen.getByLabelText(/Código DANE/)).toHaveValue('987654321')
    expect(screen.getByLabelText(/Resolución de Aprobación/)).toHaveValue('Resolución existente')
    expect(screen.getByLabelText(/Dirección/)).toHaveValue('Dirección existente')
    expect(screen.getByLabelText(/Teléfono/)).toHaveValue('3009876543')
    expect(screen.getByLabelText(/Email/)).toHaveValue('existente@institucion.edu.co')
    expect(screen.getByLabelText(/Rector/)).toHaveValue('Dr. Existente')
  })

  it('convierte valores null/undefined a strings vacíos', () => {
    const initialData = {
      nombre: null,
      siglas: undefined,
      slogan: null,
      dane: undefined,
      resolucion_aprobacion: null,
      direccion: undefined,
      telefono: null,
      email: undefined,
      rector: null,
      escudo: undefined,
    }

    render(<InstitutionForm {...defaultProps} initialData={initialData} />)

    expect(screen.getByLabelText(/Nombre de la Institución/)).toHaveValue('')
    expect(screen.getByLabelText(/Siglas/)).toHaveValue('')
    expect(screen.getByLabelText(/Slogan/)).toHaveValue('')
    expect(screen.getByLabelText(/Código DANE/)).toHaveValue('')
    expect(screen.getByLabelText(/Resolución de Aprobación/)).toHaveValue('')
    expect(screen.getByLabelText(/Dirección/)).toHaveValue('')
    expect(screen.getByLabelText(/Teléfono/)).toHaveValue('')
    expect(screen.getByLabelText(/Email/)).toHaveValue('')
    expect(screen.getByLabelText(/Rector/)).toHaveValue('')
  })

  it('muestra estado de carga', () => {
    render(<InstitutionForm {...defaultProps} isLoading={true} />)

    expect(screen.getByText('Guardando...')).toBeInTheDocument()
  })

  it('maneja archivos de imagen', async () => {
    const user = userEvent.setup()
    render(<InstitutionForm {...defaultProps} />)

    const fileInput = screen.getByLabelText(/Escudo de la Institución/)
    const file = new File(['test'], 'test.png', { type: 'image/png' })

    await user.upload(fileInput, file)

    expect(fileInput.files?.[0]).toBe(file)
  })

  it('valida tipo de archivo de imagen', async () => {
    const user = userEvent.setup()
    render(<InstitutionForm {...defaultProps} />)

    const fileInput = screen.getByLabelText(/Escudo de la Institución/)
    const invalidFile = new File(['test'], 'test.jpg', { type: 'image/jpeg' })

    await user.upload(fileInput, invalidFile)

    // Debería mostrar un error por tipo de archivo inválido
    expect(screen.getByText('Solo se permiten archivos PNG')).toBeInTheDocument()
  })

  it('valida tamaño de archivo', async () => {
    const user = userEvent.setup()
    render(<InstitutionForm {...defaultProps} />)

    const fileInput = screen.getByLabelText(/Escudo de la Institución/)
    // Crear un archivo de más de 2MB
    const largeFile = new File(['x'.repeat(3 * 1024 * 1024)], 'large.png', { type: 'image/png' })

    await user.upload(fileInput, largeFile)

    // Debería mostrar un error por tamaño de archivo
    expect(screen.getByText('El archivo no puede exceder 2MB')).toBeInTheDocument()
  })
}) 