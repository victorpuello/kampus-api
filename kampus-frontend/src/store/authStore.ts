import { create } from 'zustand'
import { persist } from 'zustand/middleware'
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
  token: string | null
  user: User | null
  isAuthenticated: boolean
  login: (email: string, password: string) => Promise<void>
  logout: () => void
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set) => ({
      token: null,
      user: null,
      isAuthenticated: false,

      login: async (email: string, password: string) => {
        try {
          const response = await axiosClient.post('/login', {
            email,
            password,
          })

          const { token, user } = response.data

          set({
            token,
            user,
            isAuthenticated: true,
          })
        } catch (error: any) {
          console.error('Error en login:', error.response?.data || error.message)
          throw new Error(error.response?.data?.message || 'Error al iniciar sesión')
        }
      },

      logout: () => {
        set({
          token: null,
          user: null,
          isAuthenticated: false,
        })
      },
    }),
    {
      name: 'auth-storage',
    }
  )
) 