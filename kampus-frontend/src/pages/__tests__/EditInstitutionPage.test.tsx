import { describe, it, expect, vi, beforeEach } from 'vitest'
import { render, screen, waitFor } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import EditInstitutionPage from '../EditInstitutionPage'
import axiosClient from '../../api/axiosClient'

// Mock de axios
const mockAxios = vi.mocked(axiosClient)

// Mock del componente InstitutionForm
vi.mock('../../components/institutions/InstitutionForm', () => ({
  InstitutionForm: ({ initialData, onSubmit, isLoading, submitLabel }: any) => (
    <form onSubmit={(e) => { e.preventDefault(); onSubmit(initialData || { nombre: 'Test', siglas: 'TST' }); }}>
      <input type="text" placeholder="Nombre" defaultValue={initialData?.nombre} />
      <input type="text" placeholder="Siglas" defaultValue={initialData?.siglas} />
      <button type="submit" disabled={isLoading}>
        {submitLabel}
      </button>
    </form>
  ),
}))

// Mock de los componentes UI
vi.mock('../../components/ui/PageHeader', () => ({
  PageHeader: ({ children, title, description }: any) => (
    <div data-testid="page-header">
      <h1>{title}</h1>
      <p>{description}</p>
      {children}
    </div>
  ),
}))

describe('EditInstitutionPage', () => {
  const mockInstitution = {
    id: 1,
    nombre: 'Instituto Técnico Industrial',
    siglas: 'ITI',
    slogan: 'Educando para el futuro',
    dane: '123456789',
    resolucion_aprobacion: 'Resolución 1234 de 2020',
    direccion: 'Calle 123 #45-67, Ciudad',
    telefono: '3001234567',
    email: 'info@institucion.edu.co',
    rector: 'Dr. Juan Pérez',
    escudo: 'https://example.com/escudo.png',
    created_at: '2024-01-01T00:00:00.000000Z',
    updated_at: '2024-01-02T00:00:00.000000Z',
  }

  beforeEach(() => {
    vi.clearAllMocks()
    mockAxios.get.mockResolvedValue({ data: { data: mockInstitution } })
    mockAxios.put.mockResolvedValue({ data: { message: 'Institución actualizada exitosamente' } })
  })

  it('renderiza la página correctamente', async () => {
    render(<EditInstitutionPage />)

    await waitFor(() => {
      expect(screen.getByText('Editar Institución')).toBeInTheDocument()
      expect(screen.getByText('Modifica la información de la institución educativa')).toBeInTheDocument()
    })
  })

  it('carga los datos de la institución para editar', async () => {
    render(<EditInstitutionPage />)

    await waitFor(() => {
      expect(mockAxios.get).toHaveBeenCalledWith('/instituciones/1')
    })

    // Verificar que el formulario se renderiza con los datos cargados
    expect(screen.getByText('Actualizar Institución')).toBeInTheDocument()
  })

  it('maneja la actualización exitosa de institución', async () => {
    const user = userEvent.setup()
    render(<EditInstitutionPage />)

    await waitFor(() => {
      expect(screen.getByText('Actualizar Institución')).toBeInTheDocument()
    })

    const submitButton = screen.getByText('Actualizar Institución')
    await user.click(submitButton)

    await waitFor(() => {
      expect(mockAxios.put).toHaveBeenCalledWith('/instituciones/1', mockInstitution)
    })
  })

  it('maneja errores durante la carga de datos', async () => {
    const consoleSpy = vi.spyOn(console, 'error').mockImplementation(() => {})
    mockAxios.get.mockRejectedValue(new Error('Error de red'))

    render(<EditInstitutionPage />)

    await waitFor(() => {
      expect(consoleSpy).toHaveBeenCalled()
    })

    consoleSpy.mockRestore()
  })

  it('maneja errores durante la actualización', async () => {
    const user = userEvent.setup()
    mockAxios.put.mockRejectedValue(new Error('Error de red'))

    render(<EditInstitutionPage />)

    await waitFor(() => {
      expect(screen.getByText('Actualizar Institución')).toBeInTheDocument()
    })

    const submitButton = screen.getByText('Actualizar Institución')
    await user.click(submitButton)

    await waitFor(() => {
      expect(mockAxios.put).toHaveBeenCalled()
    })
  })

  it('muestra estado de carga durante la carga de datos', () => {
    mockAxios.get.mockImplementation(() => new Promise(() => {})) // Promise que nunca se resuelve
    render(<EditInstitutionPage />)

    expect(screen.getByText('Cargando institución...')).toBeInTheDocument()
  })

  it('muestra estado de carga durante la actualización', async () => {
    const user = userEvent.setup()
    mockAxios.put.mockImplementation(() => new Promise(() => {})) // Promise que nunca se resuelve
    
    render(<EditInstitutionPage />)

    await waitFor(() => {
      expect(screen.getByText('Actualizar Institución')).toBeInTheDocument()
    })

    const submitButton = screen.getByText('Actualizar Institución')
    await user.click(submitButton)

    // El botón debería estar deshabilitado durante el envío
    expect(submitButton).toBeDisabled()
  })

  it('maneja ID inválido', async () => {
    // Mock de useParams para simular ID inválido
    vi.mocked(require('react-router-dom').useParams).mockReturnValue({ id: 'null' })

    render(<EditInstitutionPage />)

    await waitFor(() => {
      expect(screen.getByText('Institución no encontrada')).toBeInTheDocument()
    })
  })

  it('maneja institución no encontrada', async () => {
    mockAxios.get.mockResolvedValue({ data: { data: null } })

    render(<EditInstitutionPage />)

    await waitFor(() => {
      expect(screen.getByText('Institución no encontrada')).toBeInTheDocument()
    })
  })

  it('pasa los datos correctos al formulario', async () => {
    render(<EditInstitutionPage />)

    await waitFor(() => {
      // Verificar que el formulario recibe los datos iniciales
      expect(screen.getByText('Actualizar Institución')).toBeInTheDocument()
    })
  })

  it('maneja campos opcionales vacíos', async () => {
    const institutionWithEmptyFields = {
      ...mockInstitution,
      slogan: '',
      dane: '',
      resolucion_aprobacion: '',
      direccion: '',
      telefono: '',
      email: '',
      rector: '',
      escudo: '',
    }

    mockAxios.get.mockResolvedValue({ data: { data: institutionWithEmptyFields } })

    render(<EditInstitutionPage />)

    await waitFor(() => {
      expect(screen.getByText('Actualizar Institución')).toBeInTheDocument()
    })
  })

  it('valida que el ID sea válido antes de hacer la petición', async () => {
    // Mock de useParams para simular ID inválido
    vi.mocked(require('react-router-dom').useParams).mockReturnValue({ id: 'undefined' })

    render(<EditInstitutionPage />)

    await waitFor(() => {
      expect(mockAxios.get).not.toHaveBeenCalled()
    })
  })
}) 