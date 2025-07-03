import { describe, it, expect, vi, beforeEach } from 'vitest'
import { render, screen, waitFor } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import InstitutionDetailPage from '../InstitutionDetailPage'
import axiosClient from '../../api/axiosClient'

// Mock de axios
const mockAxios = vi.mocked(axiosClient)

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

vi.mock('../../components/ui/Button', () => ({
  Button: ({ children, onClick, variant, size }: any) => (
    <button onClick={onClick} data-variant={variant} data-size={size}>
      {children}
    </button>
  ),
}))

vi.mock('../../components/ui/Badge', () => ({
  Badge: ({ children, variant }: any) => (
    <span data-testid="badge" data-variant={variant}>
      {children}
    </span>
  ),
}))

vi.mock('../../components/ui/Card', () => ({
  Card: ({ children }: any) => <div data-testid="card">{children}</div>,
  CardHeader: ({ children }: any) => <div data-testid="card-header">{children}</div>,
  CardBody: ({ children }: any) => <div data-testid="card-body">{children}</div>,
}))

vi.mock('../../components/ui/ValidateParams', () => ({
  ValidateParams: ({ children }: any) => <div data-testid="validate-params">{children}</div>,
}))

vi.mock('../../components/ui/Alert', () => ({
  Alert: ({ message, variant }: any) => (
    <div data-testid="alert" data-variant={variant}>
      {message}
    </div>
  ),
}))

describe('InstitutionDetailPage', () => {
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
    sedes: [
      {
        id: 1,
        nombre: 'Sede Principal',
        direccion: 'Calle 123 #45-67',
        telefono: '3001234567',
        created_at: '2024-01-01T00:00:00.000000Z',
        updated_at: '2024-01-02T00:00:00.000000Z',
      },
      {
        id: 2,
        nombre: 'Sede Norte',
        direccion: 'Calle 456 #78-90',
        telefono: '3009876543',
        created_at: '2024-01-03T00:00:00.000000Z',
        updated_at: '2024-01-04T00:00:00.000000Z',
      },
    ],
  }

  beforeEach(() => {
    vi.clearAllMocks()
    mockAxios.get.mockResolvedValue({ data: { data: mockInstitution } })
  })

  it('renderiza la página correctamente', async () => {
    render(<InstitutionDetailPage />)

    expect(screen.getByText('Detalles de la Institución')).toBeInTheDocument()
    expect(screen.getByText('Información completa de la institución educativa')).toBeInTheDocument()
  })

  it('carga y muestra los datos de la institución', async () => {
    render(<InstitutionDetailPage />)

    await waitFor(() => {
      expect(screen.getByText('Instituto Técnico Industrial')).toBeInTheDocument()
      expect(screen.getByText('ITI')).toBeInTheDocument()
      expect(screen.getByText('Educando para el futuro')).toBeInTheDocument()
      expect(screen.getByText('123456789')).toBeInTheDocument()
      expect(screen.getByText('Resolución 1234 de 2020')).toBeInTheDocument()
      expect(screen.getByText('Calle 123 #45-67, Ciudad')).toBeInTheDocument()
      expect(screen.getByText('3001234567')).toBeInTheDocument()
      expect(screen.getByText('info@institucion.edu.co')).toBeInTheDocument()
      expect(screen.getByText('Dr. Juan Pérez')).toBeInTheDocument()
    })

    expect(mockAxios.get).toHaveBeenCalledWith('/instituciones/1?include=sedes')
  })

  it('muestra estado de carga', () => {
    mockAxios.get.mockImplementation(() => new Promise(() => {})) // Promise que nunca se resuelve
    render(<InstitutionDetailPage />)

    expect(screen.getByText('Cargando institución...')).toBeInTheDocument()
  })

  it('maneja errores de carga', async () => {
    const consoleSpy = vi.spyOn(console, 'error').mockImplementation(() => {})
    mockAxios.get.mockRejectedValue(new Error('Error de red'))

    render(<InstitutionDetailPage />)

    await waitFor(() => {
      expect(consoleSpy).toHaveBeenCalled()
    })

    consoleSpy.mockRestore()
  })

  it('muestra las sedes de la institución', async () => {
    render(<InstitutionDetailPage />)

    await waitFor(() => {
      expect(screen.getByText('Sedes de la Institución')).toBeInTheDocument()
      expect(screen.getByText('Sede Principal')).toBeInTheDocument()
      expect(screen.getByText('Sede Norte')).toBeInTheDocument()
      expect(screen.getByText('Calle 123 #45-67')).toBeInTheDocument()
      expect(screen.getByText('Calle 456 #78-90')).toBeInTheDocument()
    })
  })

  it('muestra mensaje cuando no hay sedes', async () => {
    const institutionWithoutSedes = { ...mockInstitution, sedes: [] }
    mockAxios.get.mockResolvedValue({ data: { data: institutionWithoutSedes } })

    render(<InstitutionDetailPage />)

    await waitFor(() => {
      expect(screen.getByText('No hay sedes registradas')).toBeInTheDocument()
      expect(screen.getByText('Esta institución aún no tiene sedes configuradas.')).toBeInTheDocument()
    })
  })

  it('maneja eliminación de institución', async () => {
    const user = userEvent.setup()
    mockAxios.delete.mockResolvedValue({})

    render(<InstitutionDetailPage />)

    await waitFor(() => {
      expect(screen.getByText('Instituto Técnico Industrial')).toBeInTheDocument()
    })

    const deleteButton = screen.getByText('Eliminar')
    await user.click(deleteButton)

    await waitFor(() => {
      expect(mockAxios.delete).toHaveBeenCalledWith('/instituciones/1')
    })
  })

  it('muestra información de auditoría', async () => {
    render(<InstitutionDetailPage />)

    await waitFor(() => {
      expect(screen.getByText('Información de Auditoría')).toBeInTheDocument()
      expect(screen.getByText(/1 ene 2024/)).toBeInTheDocument()
      expect(screen.getByText(/2 ene 2024/)).toBeInTheDocument()
    })
  })

  it('muestra acciones rápidas', async () => {
    render(<InstitutionDetailPage />)

    await waitFor(() => {
      expect(screen.getByText('Acciones Rápidas')).toBeInTheDocument()
      expect(screen.getByText('Editar Institución')).toBeInTheDocument()
      expect(screen.getByText('Nueva Sede')).toBeInTheDocument()
      expect(screen.getByText('Ver Todas')).toBeInTheDocument()
    })
  })

  it('maneja ID inválido', async () => {
    // Mock de useParams para simular ID inválido
    vi.mocked(require('react-router-dom').useParams).mockReturnValue({ id: 'null' })

    render(<InstitutionDetailPage />)

    await waitFor(() => {
      expect(screen.getByText('El parámetro id es inválido')).toBeInTheDocument()
    })
  })

  it('maneja institución no encontrada', async () => {
    mockAxios.get.mockResolvedValue({ data: { data: null } })

    render(<InstitutionDetailPage />)

    await waitFor(() => {
      expect(screen.getByText('Institución no encontrada')).toBeInTheDocument()
    })
  })

  it('muestra el escudo de la institución', async () => {
    render(<InstitutionDetailPage />)

    await waitFor(() => {
      const escudoImage = screen.getByAltText('Escudo de la institución')
      expect(escudoImage).toBeInTheDocument()
      expect(escudoImage).toHaveAttribute('src', 'https://example.com/escudo.png')
    })
  })

  it('formatea el email como enlace', async () => {
    render(<InstitutionDetailPage />)

    await waitFor(() => {
      const emailLink = screen.getByText('info@institucion.edu.co')
      expect(emailLink).toBeInTheDocument()
      expect(emailLink.closest('a')).toHaveAttribute('href', 'mailto:info@institucion.edu.co')
    })
  })

  it('muestra badges con siglas', async () => {
    render(<InstitutionDetailPage />)

    await waitFor(() => {
      const badge = screen.getByTestId('badge')
      expect(badge).toHaveTextContent('ITI')
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

    render(<InstitutionDetailPage />)

    await waitFor(() => {
      expect(screen.getByText('Instituto Técnico Industrial')).toBeInTheDocument()
      // Los campos vacíos no deberían mostrarse
      expect(screen.queryByText('Slogan')).not.toBeInTheDocument()
      expect(screen.queryByText('Código DANE')).not.toBeInTheDocument()
    })
  })
}) 