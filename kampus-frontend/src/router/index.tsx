import { createBrowserRouter, Outlet, Navigate } from 'react-router-dom'
import DashboardLayout from '../components/layouts/DashboardLayout'
import LoginPage from '../pages/LoginPage'
import ProtectedRoute from './ProtectedRoute'
import StudentsListPage from '../pages/StudentsListPage'
import StudentDetailPage from '../pages/StudentDetailPage'
import CreateStudentPage from '../pages/CreateStudentPage'
import EditStudentPage from '../pages/EditStudentPage'

// Página temporal del dashboard
const DashboardPage = () => (
  <div className="p-4">
    <h1 className="text-2xl font-bold">Dashboard</h1>
  </div>
)

export const router = createBrowserRouter([
  {
    path: '/login',
    element: <LoginPage />,
  },
  {
    path: '/',
    element: <DashboardLayout><Outlet /></DashboardLayout>,
    children: [
      {
        index: true,
        element: <Navigate to="/estudiantes" replace />
      },
      {
        path: 'dashboard',
        element: <Navigate to="/estudiantes" replace />
      },
      {
        path: 'estudiantes',
        element: <StudentsListPage />
      },
      {
        path: 'estudiantes/crear',
        element: <CreateStudentPage />
      },
      {
        path: 'estudiantes/:id',
        element: <StudentDetailPage />
      },
      {
        path: 'estudiantes/:id/editar',
        element: <EditStudentPage />
      }
    ],
  },
  {
    path: '/',
    element: <LoginPage />,
  },
]) 