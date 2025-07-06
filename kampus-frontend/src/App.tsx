import { RouterProvider } from 'react-router-dom'
import { router } from './router'
import { AlertProvider } from './contexts/AlertContext'
import './App.css'

function App() {
  console.log('🔄 App.tsx renderizando...')
  
  return (
    <AlertProvider position="top-right" maxAlerts={5}>
      <RouterProvider router={router} />
    </AlertProvider>
  )
}

export default App
