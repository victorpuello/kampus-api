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
import GuardiansListPage from '../pages/GuardiansListPage'
import GuardianDetailPage from '../pages/GuardianDetailPage'
import CreateGuardianPage from '../pages/CreateGuardianPage'
import EditGuardianPage from '../pages/EditGuardianPage'
import UsersListPage from '../pages/UsersListPage'
import UserDetailPage from '../pages/UserDetailPage'
import CreateUserPage from '../pages/CreateUserPage'
import EditUserPage from '../pages/EditUserPage'
import DashboardPage from '../pages/DashboardPage'
import GradesListPage from '../pages/GradesListPage'
import GradeDetailPage from '../pages/GradeDetailPage'
import CreateGradePage from '../pages/CreateGradePage'
import EditGradePage from '../pages/EditGradePage'
import GroupsListPage from '../pages/GroupsListPage'
import GroupDetailPage from '../pages/GroupDetailPage'
import CreateGroupPage from '../pages/CreateGroupPage'
import EditGroupPage from '../pages/EditGroupPage'
import AreasListPage from '../pages/AreasListPage'
import AreaDetailPage from '../pages/AreaDetailPage'
import CreateAreaPage from '../pages/CreateAreaPage'
import EditAreaPage from '../pages/EditAreaPage'
import AsignaturasListPage from '../pages/AsignaturasListPage'
import AsignaturaDetailPage from '../pages/AsignaturaDetailPage'
import CreateAsignaturaPage from '../pages/CreateAsignaturaPage'
import EditAsignaturaPage from '../pages/EditAsignaturaPage'
import InstitutionsListPage from '../pages/InstitutionsListPage'
import InstitutionDetailPage from '../pages/InstitutionDetailPage'
import CreateInstitutionPage from '../pages/CreateInstitutionPage'
import EditInstitutionPage from '../pages/EditInstitutionPage'
import SedesListPage from '../pages/SedesListPage'
import SedeDetailPage from '../pages/SedeDetailPage'
import CreateSedePage from '../pages/CreateSedePage'
import EditSedePage from '../pages/EditSedePage'
import InstitutionSedesPage from '../pages/InstitutionSedesPage'
import InstitutionSedeDetailPage from '../pages/InstitutionSedeDetailPage'
import InstitutionSedeEditPage from '../pages/InstitutionSedeEditPage'
import InstitutionSedeCreatePage from '../pages/InstitutionSedeCreatePage'

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
      },
      {
        path: 'acudientes',
        element: <DashboardLayout><GuardiansListPage /></DashboardLayout>
      },
      {
        path: 'acudientes/crear',
        element: <DashboardLayout><CreateGuardianPage /></DashboardLayout>
      },
      {
        path: 'acudientes/:id',
        element: <DashboardLayout><GuardianDetailPage /></DashboardLayout>
      },
      {
        path: 'acudientes/:id/editar',
        element: <DashboardLayout><EditGuardianPage /></DashboardLayout>
      },
      {
        path: 'usuarios',
        element: <DashboardLayout><UsersListPage /></DashboardLayout>
      },
      {
        path: 'usuarios/crear',
        element: <DashboardLayout><CreateUserPage /></DashboardLayout>
      },
      {
        path: 'usuarios/:id',
        element: <DashboardLayout><UserDetailPage /></DashboardLayout>
      },
      {
        path: 'usuarios/:id/editar',
        element: <DashboardLayout><EditUserPage /></DashboardLayout>
      },
      {
        path: 'grados',
        element: <DashboardLayout><GradesListPage /></DashboardLayout>
      },
      {
        path: 'grados/crear',
        element: <DashboardLayout><CreateGradePage /></DashboardLayout>
      },
      {
        path: 'grados/:id',
        element: <DashboardLayout><GradeDetailPage /></DashboardLayout>
      },
      {
        path: 'grados/:id/editar',
        element: <DashboardLayout><EditGradePage /></DashboardLayout>
      },
      {
        path: 'grupos',
        element: <DashboardLayout><GroupsListPage /></DashboardLayout>
      },
      {
        path: 'grupos/crear',
        element: <DashboardLayout><CreateGroupPage /></DashboardLayout>
      },
      {
        path: 'grupos/:id',
        element: <DashboardLayout><GroupDetailPage /></DashboardLayout>
      },
      {
        path: 'grupos/:id/editar',
        element: <DashboardLayout><EditGroupPage /></DashboardLayout>
      },
      {
        path: 'areas',
        element: <DashboardLayout><AreasListPage /></DashboardLayout>
      },
      {
        path: 'areas/crear',
        element: <DashboardLayout><CreateAreaPage /></DashboardLayout>
      },
      {
        path: 'areas/:id',
        element: <DashboardLayout><AreaDetailPage /></DashboardLayout>
      },
      {
        path: 'areas/:id/editar',
        element: <DashboardLayout><EditAreaPage /></DashboardLayout>
      },
      {
        path: 'asignaturas',
        element: <DashboardLayout><AsignaturasListPage /></DashboardLayout>
      },
      {
        path: 'asignaturas/crear',
        element: <DashboardLayout><CreateAsignaturaPage /></DashboardLayout>
      },
      {
        path: 'asignaturas/:id',
        element: <DashboardLayout><AsignaturaDetailPage /></DashboardLayout>
      },
      {
        path: 'asignaturas/:id/editar',
        element: <DashboardLayout><EditAsignaturaPage /></DashboardLayout>
      },
      {
        path: 'instituciones',
        element: <DashboardLayout><InstitutionsListPage /></DashboardLayout>
      },
      {
        path: 'instituciones/crear',
        element: <DashboardLayout><CreateInstitutionPage /></DashboardLayout>
      },
      {
        path: 'instituciones/:id',
        element: <DashboardLayout><InstitutionDetailPage /></DashboardLayout>
      },
      {
        path: 'instituciones/:id/editar',
        element: <DashboardLayout><EditInstitutionPage /></DashboardLayout>
      },
      {
        path: 'instituciones/:id/sedes',
        element: <DashboardLayout><InstitutionSedesPage /></DashboardLayout>
      },
      {
        path: 'instituciones/:institutionId/sedes/crear',
        element: <DashboardLayout><InstitutionSedeCreatePage /></DashboardLayout>
      },
      {
        path: 'instituciones/:institutionId/sedes/:id',
        element: <DashboardLayout><InstitutionSedeDetailPage /></DashboardLayout>
      },
      {
        path: 'instituciones/:institutionId/sedes/:id/editar',
        element: <DashboardLayout><InstitutionSedeEditPage /></DashboardLayout>
      },
      {
        path: 'sedes',
        element: <DashboardLayout><SedesListPage /></DashboardLayout>
      },
      {
        path: 'sedes/crear',
        element: <DashboardLayout><CreateSedePage /></DashboardLayout>
      },
      {
        path: 'sedes/:id',
        element: <DashboardLayout><SedeDetailPage /></DashboardLayout>
      },
      {
        path: 'sedes/:id/editar',
        element: <DashboardLayout><EditSedePage /></DashboardLayout>
      },
    ],
  }
]) 