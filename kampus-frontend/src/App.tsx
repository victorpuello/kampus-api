import { RouterProvider } from 'react-router-dom'
import { router } from './router'
import { AuthProvider } from './components/AuthProvider'
import { AlertProvider } from './contexts/AlertContext'
import './App.css'

function App() {
  return (
    <AlertProvider position="top-right" maxAlerts={5}>
      <AuthProvider>
        <RouterProvider router={router} />
      </AuthProvider>
    </AlertProvider>
  )
}

export default App
