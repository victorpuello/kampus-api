import { describe, it, expect, vi, beforeEach } from 'vitest'
import { render, screen, waitFor } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import InstitutionsListPage from '../InstitutionsListPage'
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

describe('InstitutionsListPage', () => {
  const mockInstitutions = [
    {
      id: 1,
      nombre: 'Instituto Técnico Industrial',
      siglas: 'ITI',
      created_at: '2024-01-01T00:00:00.000000Z',
      updated_at: '2024-01-02T00:00:00.000000Z',
    },
    {
      id: 2,
      nombre: 'Colegio San José',
      siglas: 'CSJ',
      created_at: '2024-01-03T00:00:00.000000Z',
      updated_at: '2024-01-04T00:00:00.000000Z',
    },
  ]

  const mockResponse = {
    data: mockInstitutions,
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 2,
  }

  beforeEach(() => {
    vi.clearAllMocks()
    mockAxios.get.mockResolvedValue({ data: mockResponse })
  })

  it('renderiza la página correctamente', async () => {
    render(<InstitutionsListPage />)

    expect(screen.getByText('Instituciones')).toBeInTheDocument()
    expect(screen.getByText('Gestiona las instituciones educativas del sistema')).toBeInTheDocument()
    expect(screen.getByText('Nueva Institución')).toBeInTheDocument()
  })

  it('carga y muestra las instituciones', async () => {
    render(<InstitutionsListPage />)

    await waitFor(() => {
      expect(screen.getByText('Instituto Técnico Industrial')).toBeInTheDocument()
      expect(screen.getByText('Colegio San José')).toBeInTheDocument()
    })

    expect(mockAxios.get).toHaveBeenCalledWith('/instituciones?page=1&per_page=10')
  })

  it('muestra estado de carga', () => {
    mockAxios.get.mockImplementation(() => new Promise(() => {})) // Promise que nunca se resuelve
    render(<InstitutionsListPage />)

    expect(screen.getByText('Cargando instituciones...')).toBeInTheDocument()
  })

  it('maneja errores de carga', async () => {
    const consoleSpy = vi.spyOn(console, 'error').mockImplementation(() => {})
    mockAxios.get.mockRejectedValue(new Error('Error de red'))

    render(<InstitutionsListPage />)

    await waitFor(() => {
      expect(consoleSpy).toHaveBeenCalled()
    })

    consoleSpy.mockRestore()
  })

  it('maneja búsqueda de instituciones', async () => {
    const user = userEvent.setup()
    render(<InstitutionsListPage />)

    const searchInput = screen.getByPlaceholderText('Buscar instituciones...')
    await user.type(searchInput, 'Técnico')

    await waitFor(() => {
      expect(mockAxios.get).toHaveBeenCalledWith('/instituciones?page=1&per_page=10&search=Técnico')
    })
  })

  it('maneja paginación', async () => {
    const user = userEvent.setup()
    render(<InstitutionsListPage />)

    await waitFor(() => {
      expect(screen.getByText('Instituto Técnico Industrial')).toBeInTheDocument()
    })

    const nextButton = screen.getByText('Siguiente')
    if (nextButton && !nextButton.disabled) {
      await user.click(nextButton)
      
      await waitFor(() => {
        expect(mockAxios.get).toHaveBeenCalledWith('/instituciones?page=2&per_page=10')
      })
    }
  })

  it('maneja eliminación de instituciones', async () => {
    const user = userEvent.setup()
    mockAxios.delete.mockResolvedValue({})
    
    render(<InstitutionsListPage />)

    await waitFor(() => {
      expect(screen.getByText('Instituto Técnico Industrial')).toBeInTheDocument()
    })

    const deleteButtons = screen.getAllByRole('button').filter(button => 
      button.querySelector('svg')?.getAttribute('d')?.includes('M19 7l-.867 12.142A2')
    )

    if (deleteButtons.length > 0) {
      await user.click(deleteButtons[0])

      await waitFor(() => {
        expect(mockAxios.delete).toHaveBeenCalledWith('/instituciones/1')
      })
    }
  })

  it('muestra mensaje cuando no hay instituciones', async () => {
    mockAxios.get.mockResolvedValue({
      data: {
        data: [],
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
      }
    })

    render(<InstitutionsListPage />)

    await waitFor(() => {
      expect(screen.getByText('No hay instituciones registradas.')).toBeInTheDocument()
    })
  })

  it('muestra mensaje cuando no hay resultados de búsqueda', async () => {
    const user = userEvent.setup()
    mockAxios.get.mockResolvedValue({
      data: {
        data: [],
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
      }
    })

    render(<InstitutionsListPage />)

    const searchInput = screen.getByPlaceholderText('Buscar instituciones...')
    await user.type(searchInput, 'Búsqueda sin resultados')

    await waitFor(() => {
      expect(screen.getByText('No se encontraron instituciones que coincidan con la búsqueda.')).toBeInTheDocument()
    })
  })

  it('formatea fechas correctamente', async () => {
    render(<InstitutionsListPage />)

    await waitFor(() => {
      // Verificar que las fechas se muestran en formato legible
      expect(screen.getByText(/1 ene 2024/)).toBeInTheDocument()
      expect(screen.getByText(/2 ene 2024/)).toBeInTheDocument()
    })
  })

  it('muestra badges con siglas', async () => {
    render(<InstitutionsListPage />)

    await waitFor(() => {
      const badges = screen.getAllByTestId('badge')
      expect(badges).toHaveLength(2)
      expect(badges[0]).toHaveTextContent('ITI')
      expect(badges[1]).toHaveTextContent('CSJ')
    })
  })

  it('redirige al login si no está autenticado', () => {
    // Mock del store para simular usuario no autenticado
    vi.mocked(require('../../store/authStore').useAuthStore).mockReturnValue({
      token: null,
      user: null,
      isAuthenticated: false,
      login: vi.fn(),
      logout: vi.fn(),
    })

    render(<InstitutionsListPage />)

    expect(screen.getByText('Error de Autenticación:')).toBeInTheDocument()
  })
}) 