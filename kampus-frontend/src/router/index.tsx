import { createBrowserRouter, Outlet, Navigate } from 'react-router-dom'
import DashboardLayout from '../components/layouts/DashboardLayout'
import LoginPage from '../pages/LoginPage'
import ProtectedRoute from './ProtectedRoute'
import PermissionRoute from './PermissionRoute'
import NoAutorizadoPage from '../pages/NoAutorizadoPage'
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
import AcademicYearsListPage from '../pages/AcademicYearsListPage'
import CreateAcademicYearPage from '../pages/CreateAcademicYearPage'
import EditAcademicYearPage from '../pages/EditAcademicYearPage'
import AcademicYearDetailPage from '../pages/AcademicYearDetailPage'
import AnioPeriodosListPage from '../pages/AnioPeriodosListPage'
import AnioPeriodoDetailPage from '../pages/AnioPeriodoDetailPage'
import AnioCreatePeriodoPage from '../pages/AnioCreatePeriodoPage'
import AnioEditPeriodoPage from '../pages/AnioEditPeriodoPage'
import AsignacionesListPage from '../pages/AsignacionesListPage'
import InstitutionFranjasHorariasPage from '../pages/InstitutionFranjasHorariasPage'
import InstitutionFranjaHorariaDetailPage from '../pages/InstitutionFranjaHorariaDetailPage'
import InstitutionFranjaHorariaCreatePage from '../pages/InstitutionFranjaHorariaCreatePage'
import InstitutionFranjaHorariaEditPage from '../pages/InstitutionFranjaHorariaEditPage'


export const router = createBrowserRouter([
  {
    path: '/login',
    element: <LoginPage />,
  },
  {
    path: '/no-autorizado',
    element: <NoAutorizadoPage />,
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
        element: (
          <PermissionRoute permission="ver_estudiantes">
            <DashboardLayout><StudentsListPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'estudiantes/crear',
        element: (
          <PermissionRoute permission="crear_estudiantes">
            <DashboardLayout><CreateStudentPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'estudiantes/:id',
        element: (
          <PermissionRoute permission="ver_estudiantes">
            <DashboardLayout><StudentDetailPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'estudiantes/:id/editar',
        element: (
          <PermissionRoute permission="editar_estudiantes">
            <DashboardLayout><EditStudentPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'docentes',
        element: (
          <PermissionRoute permission="ver_docentes">
            <DashboardLayout><TeachersListPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'docentes/crear',
        element: (
          <PermissionRoute permission="crear_docentes">
            <DashboardLayout><CreateTeacherPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'docentes/:id',
        element: (
          <PermissionRoute permission="ver_docentes">
            <DashboardLayout><TeacherDetailPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'docentes/:id/editar',
        element: (
          <PermissionRoute permission="editar_docentes">
            <DashboardLayout><EditTeacherPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'acudientes',
        element: (
          <PermissionRoute permission="ver_acudientes">
            <DashboardLayout><GuardiansListPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'acudientes/crear',
        element: (
          <PermissionRoute permission="crear_acudientes">
            <DashboardLayout><CreateGuardianPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'acudientes/:id',
        element: (
          <PermissionRoute permission="ver_acudientes">
            <DashboardLayout><GuardianDetailPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'acudientes/:id/editar',
        element: (
          <PermissionRoute permission="editar_acudientes">
            <DashboardLayout><EditGuardianPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'usuarios',
        element: (
          <PermissionRoute permission="ver_usuarios">
            <DashboardLayout><UsersListPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'usuarios/crear',
        element: (
          <PermissionRoute permission="crear_usuarios">
            <DashboardLayout><CreateUserPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'usuarios/:id',
        element: (
          <PermissionRoute permission="ver_usuarios">
            <DashboardLayout><UserDetailPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'usuarios/:id/editar',
        element: (
          <PermissionRoute permission="editar_usuarios">
            <DashboardLayout><EditUserPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'grados',
        element: (
          <PermissionRoute permission="ver_grados">
            <DashboardLayout><GradesListPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'grados/crear',
        element: (
          <PermissionRoute permission="crear_grados">
            <DashboardLayout><CreateGradePage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'grados/:id',
        element: (
          <PermissionRoute permission="ver_grados">
            <DashboardLayout><GradeDetailPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'grados/:id/editar',
        element: (
          <PermissionRoute permission="editar_grados">
            <DashboardLayout><EditGradePage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'grupos',
        element: (
          <PermissionRoute permission="ver_grupos">
            <DashboardLayout><GroupsListPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'grupos/crear',
        element: (
          <PermissionRoute permission="crear_grupos">
            <DashboardLayout><CreateGroupPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'grupos/:id',
        element: (
          <PermissionRoute permission="ver_grupos">
            <DashboardLayout><GroupDetailPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'grupos/:id/editar',
        element: (
          <PermissionRoute permission="editar_grupos">
            <DashboardLayout><EditGroupPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'areas',
        element: (
          <PermissionRoute permission="ver_areas">
            <DashboardLayout><AreasListPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'areas/crear',
        element: (
          <PermissionRoute permission="crear_areas">
            <DashboardLayout><CreateAreaPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'areas/:id',
        element: (
          <PermissionRoute permission="ver_areas">
            <DashboardLayout><AreaDetailPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'areas/:id/editar',
        element: (
          <PermissionRoute permission="editar_areas">
            <DashboardLayout><EditAreaPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'asignaturas',
        element: (
          <PermissionRoute permission="ver_asignaturas">
            <DashboardLayout><AsignaturasListPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'asignaturas/crear',
        element: (
          <PermissionRoute permission="crear_asignaturas">
            <DashboardLayout><CreateAsignaturaPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'asignaturas/:id',
        element: (
          <PermissionRoute permission="ver_asignaturas">
            <DashboardLayout><AsignaturaDetailPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'asignaturas/:id/editar',
        element: (
          <PermissionRoute permission="editar_asignaturas">
            <DashboardLayout><EditAsignaturaPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'instituciones',
        element: (
          <PermissionRoute permission="ver_instituciones">
            <DashboardLayout><InstitutionsListPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'instituciones/crear',
        element: (
          <PermissionRoute permission="crear_instituciones">
            <DashboardLayout><CreateInstitutionPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'instituciones/:id',
        element: (
          <PermissionRoute permission="ver_instituciones">
            <DashboardLayout><InstitutionDetailPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'instituciones/:id/editar',
        element: (
          <PermissionRoute permission="editar_instituciones">
            <DashboardLayout><EditInstitutionPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'instituciones/:id/sedes',
        element: <DashboardLayout><InstitutionSedesPage /></DashboardLayout>
      },
      {
        path: 'instituciones/:id/sedes/crear',
        element: <DashboardLayout><InstitutionSedeCreatePage /></DashboardLayout>
      },
      {
        path: 'instituciones/:id/sedes/:sedeId',
        element: <DashboardLayout><InstitutionSedeDetailPage /></DashboardLayout>
      },
      {
        path: 'instituciones/:id/sedes/:sedeId/editar',
        element: <DashboardLayout><InstitutionSedeEditPage /></DashboardLayout>
      },
      {
        path: 'instituciones/:institutionId/franjas-horarias',
        element: (
          <PermissionRoute permission="ver_franjas_horarias">
            <DashboardLayout><InstitutionFranjasHorariasPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'instituciones/:institutionId/franjas-horarias/crear',
        element: (
          <PermissionRoute permission="crear_franjas_horarias">
            <DashboardLayout><InstitutionFranjaHorariaCreatePage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'instituciones/:institutionId/franjas-horarias/:id',
        element: (
          <PermissionRoute permission="ver_franjas_horarias">
            <DashboardLayout><InstitutionFranjaHorariaDetailPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'instituciones/:institutionId/franjas-horarias/:id/editar',
        element: (
          <PermissionRoute permission="editar_franjas_horarias">
            <DashboardLayout><InstitutionFranjaHorariaEditPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'sedes',
        element: (
          <PermissionRoute permission="ver_sedes">
            <DashboardLayout><SedesListPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'sedes/crear',
        element: (
          <PermissionRoute permission="crear_sedes">
            <DashboardLayout><CreateSedePage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'sedes/:id',
        element: (
          <PermissionRoute permission="ver_sedes">
            <DashboardLayout><SedeDetailPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'sedes/:id/editar',
        element: (
          <PermissionRoute permission="editar_sedes">
            <DashboardLayout><EditSedePage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'anios',
        element: (
          <PermissionRoute permission="ver_anios">
            <DashboardLayout><AcademicYearsListPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'anios/crear',
        element: (
          <PermissionRoute permission="crear_anios">
            <DashboardLayout><CreateAcademicYearPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'anios/:id',
        element: (
          <PermissionRoute permission="ver_anios">
            <DashboardLayout><AcademicYearDetailPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'anios/:id/editar',
        element: (
          <PermissionRoute permission="editar_anios">
            <DashboardLayout><EditAcademicYearPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'anios/:anioId/periodos',
        element: (
          <PermissionRoute permission="ver_periodos">
            <DashboardLayout><AnioPeriodosListPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'anios/:anioId/periodos/crear',
        element: (
          <PermissionRoute permission="crear_periodos">
            <DashboardLayout><AnioCreatePeriodoPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'anios/:anioId/periodos/:periodoId',
        element: (
          <PermissionRoute permission="ver_periodos">
            <DashboardLayout><AnioPeriodoDetailPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'anios/:anioId/periodos/:periodoId/editar',
        element: (
          <PermissionRoute permission="editar_periodos">
            <DashboardLayout><AnioEditPeriodoPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
      {
        path: 'asignaciones',
        element: (
          <PermissionRoute permission="ver_asignaciones">
            <DashboardLayout><AsignacionesListPage /></DashboardLayout>
          </PermissionRoute>
        )
      },
    ],
  }
]) 