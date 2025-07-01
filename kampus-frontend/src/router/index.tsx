import { createBrowserRouter, Outlet, Navigate } from 'react-router-dom'
import DashboardLayout from '../components/layouts/DashboardLayout'
import LoginPage from '../pages/LoginPage'
import ProtectedRoute from './ProtectedRoute'
import StudentsListPage from '../pages/StudentsListPage'
import StudentDetailPage from '../pages/StudentDetailPage'
import CreateStudentPage from '../pages/CreateStudentPage'
import EditStudentPage from '../pages/EditStudentPage'
import TeachersListPage from '../pages/TeachersListPage'
import TeacherDetailPage from '../pages/TeacherDetailPage'
import CreateTeacherPage from '../pages/CreateTeacherPage'
import EditTeacherPage from '../pages/EditTeacherPage'
import DashboardPage from '../pages/DashboardPage'

export const router = createBrowserRouter([
  {
    path: '/login',
    element: <LoginPage />,
  },
  {
    path: '/',
    element: <ProtectedRoute />,
    children: [
      {
        index: true,
        element: <DashboardLayout><DashboardPage /></DashboardLayout>
      },
      {
        path: 'dashboard',
        element: <DashboardLayout><DashboardPage /></DashboardLayout>
      },
      {
        path: 'estudiantes',
        element: <DashboardLayout><StudentsListPage /></DashboardLayout>
      },
      {
        path: 'estudiantes/crear',
        element: <DashboardLayout><CreateStudentPage /></DashboardLayout>
      },
      {
        path: 'estudiantes/:id',
        element: <DashboardLayout><StudentDetailPage /></DashboardLayout>
      },
      {
        path: 'estudiantes/:id/editar',
        element: <DashboardLayout><EditStudentPage /></DashboardLayout>
      },
      {
        path: 'docentes',
        element: <DashboardLayout><TeachersListPage /></DashboardLayout>
      },
      {
        path: 'docentes/crear',
        element: <DashboardLayout><CreateTeacherPage /></DashboardLayout>
      },
      {
        path: 'docentes/:id',
        element: <DashboardLayout><TeacherDetailPage /></DashboardLayout>
      },
      {
        path: 'docentes/:id/editar',
        element: <DashboardLayout><EditTeacherPage /></DashboardLayout>
      }
    ],
  }
]) 