import { create } from 'zustand'
import { persist } from 'zustand/middleware'
import axios from 'axios'
import axiosClient from '../api/axiosClient'

interface User {
  id: number
  nombre: string
  apellido: string
  email: string
  username: string
  estado: string
  institucion: {
    id: number
    nombre: string
  }
  roles: Array<{
    id: number
    nombre: string
    permissions: Array<{
      id: number
      nombre: string
    }>
  }>
}

interface AuthState {
  user: User | null
  isAuthenticated: boolean
  login: (email: string, password: string) => Promise<void>
  logout: () => void
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      user: null,
      isAuthenticated: false,

      login: async (email: string, password: string) => {
        console.log('Iniciando login con:', email)
        try {
          // Obtener el CSRF token (sin baseURL de la API)
          await axios.get('http://kampus.test/sanctum/csrf-cookie', {
            withCredentials: true,
          })
          console.log('CSRF token obtenido')

          // Hacer login con credenciales y cookies
          const response = await axiosClient.post('/login', {
            email,
            password,
          }, {
            withCredentials: true,
          })

          console.log('Respuesta del servidor:', response.data)
          const { user } = response.data

          set({
            user,
            isAuthenticated: true,
          })
          console.log('Estado actualizado:', get())
        } catch (error: any) {
          console.error('Error en login:', error.response?.data || error.message)
          throw new Error(error.response?.data?.message || 'Error al iniciar sesión')
        }
      },

      logout: async () => {
        try {
          await axiosClient.post('/logout')
        } catch (error) {
          console.error('Error al cerrar sesión en el backend:', error)
        } finally {
          set({
            user: null,
            isAuthenticated: false,
          })
        }
      },
    }),
    {
      name: 'auth-storage',
    }
  )
)

// Log del estado inicial después de la persistencia
console.log('Estado inicial del store (después de persist):', useAuthStore.getState()) 