import '@testing-library/jest-dom'
import { vi } from 'vitest'
import React from 'react'

// Mock de react-router-dom
vi.mock('react-router-dom', async (importOriginal) => {
  const actual = await importOriginal();
  return {
    ...actual,
    useNavigate: () => vi.fn(),
    useParams: () => ({}),
    useLocation: () => ({ pathname: '/', search: '', hash: '', state: null }),
  };
})

// Mock de axios
vi.mock('../api/axiosClient', () => ({
  default: {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
  },
}))

// Mock del store de autenticación
vi.mock('../store/authStore', () => ({
  useAuthStore: () => ({
    token: 'mock-token',
    user: {
      id: 1,
      nombre: 'Admin',
      apellido: 'User',
      email: 'admin@example.com',
      username: 'admin',
      estado: 'activo',
      institucion: { id: 1, nombre: 'Test Institution' },
      roles: [{ id: 1, nombre: 'Administrador' }],
    },
    isAuthenticated: true,
    login: vi.fn(),
    logout: vi.fn(),
  }),
}))

// Mock del contexto de alertas
vi.mock('../contexts/AlertContext', () => ({
  useAlertContext: () => ({
    showSuccess: vi.fn(),
    showError: vi.fn(),
    showWarning: vi.fn(),
    showInfo: vi.fn(),
    removeAlert: vi.fn(),
    clearAlerts: vi.fn(),
  }),
}))

// Mock del hook de confirmación
vi.mock('../hooks/useConfirm', () => ({
  useConfirm: () => ({
    confirm: vi.fn(),
  }),
}))

// Configuración global para las pruebas
Object.defineProperty(window, 'matchMedia', {
  writable: true,
  value: vi.fn().mockImplementation(query => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: vi.fn(), // deprecated
    removeListener: vi.fn(), // deprecated
    addEventListener: vi.fn(),
    removeEventListener: vi.fn(),
    dispatchEvent: vi.fn(),
  })),
}) 