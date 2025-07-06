import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../hooks/useAuth';
import { Button } from '../components/ui/Button';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import axiosClient from '../api/axiosClient';
import { useAlertContext } from '../contexts/AlertContext';
import { Chart as ChartJS, ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement, Title } from 'chart.js';
import { Pie, Bar } from 'react-chartjs-2';

// Registrar los componentes de Chart.js
ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement, Title);

interface DashboardStats {
  students: number;
  teachers: number;
  institutions: number;
  groups: number;
}

interface RoleDistribution {
  [key: string]: number;
}

const DashboardPage = () => {
  const navigate = useNavigate();
  const { user, isAuthenticated, logout } = useAuth();
  const { showError } = useAlertContext();
  const [stats, setStats] = useState<DashboardStats>({
    students: 0,
    teachers: 0,
    institutions: 0,
    groups: 0
  });
  const [roleDistribution, setRoleDistribution] = useState<RoleDistribution>({});
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchDashboardData();
  }, []);

  const fetchDashboardData = async () => {
    try {
      setLoading(true);
      
      // Obtener estadísticas
      const [studentsRes, teachersRes, institutionsRes, groupsRes, usersRes] = await Promise.all([
        axiosClient.get('/estudiantes'),
        axiosClient.get('/docentes'),
        axiosClient.get('/instituciones'),
        axiosClient.get('/grupos'),
        axiosClient.get('/users')
      ]);

      // Procesar estadísticas
      const studentsCount = studentsRes.data.data?.length || 0;
      const teachersCount = teachersRes.data.data?.length || 0;
      const institutionsCount = institutionsRes.data.total || institutionsRes.data.data?.length || 0;
      const groupsCount = groupsRes.data.data?.length || 0;

      setStats({
        students: studentsCount,
        teachers: teachersCount,
        institutions: institutionsCount,
        groups: groupsCount
      });

      // Procesar distribución de roles
      const users = usersRes.data.data || usersRes.data || [];
      const roleCount: RoleDistribution = {};
      
      users.forEach((user: any) => {
        if (user.roles && user.roles.length > 0) {
          user.roles.forEach((role: any) => {
            roleCount[role.nombre] = (roleCount[role.nombre] || 0) + 1;
          });
        } else {
          roleCount['Sin rol'] = (roleCount['Sin rol'] || 0) + 1;
        }
      });

      setRoleDistribution(roleCount);

    } catch (err: any) {
      console.error('Error fetching dashboard data:', err);
      showError('Error al cargar los datos del dashboard', 'Error');
    } finally {
      setLoading(false);
    }
  };

  const handleLogout = async () => {
    try {
      await logout();
    } catch (error) {
      console.error('Error al cerrar sesión:', error);
    }
  };

  // Configuración para la gráfica de pastel de roles
  const pieChartData = {
    labels: Object.keys(roleDistribution),
    datasets: [
      {
        data: Object.values(roleDistribution),
        backgroundColor: [
          '#3B82F6', // Blue
          '#10B981', // Green
          '#F59E0B', // Yellow
          '#EF4444', // Red
          '#8B5CF6', // Purple
          '#06B6D4', // Cyan
          '#F97316', // Orange
          '#84CC16', // Lime
        ],
        borderWidth: 2,
        borderColor: '#ffffff',
      },
    ],
  };

  const pieChartOptions = {
    responsive: true,
    plugins: {
      legend: {
        position: 'bottom' as const,
      },
      title: {
        display: true,
        text: 'Distribución de Usuarios por Rol',
        font: {
          size: 16,
          weight: 'bold' as const,
        },
      },
    },
  };

  // Configuración para la gráfica de barras de estadísticas
  const barChartData = {
    labels: ['Estudiantes', 'Docentes', 'Instituciones', 'Grupos'],
    datasets: [
      {
        label: 'Cantidad',
        data: [stats.students, stats.teachers, stats.institutions, stats.groups],
        backgroundColor: [
          'rgba(59, 130, 246, 0.8)', // Blue
          'rgba(16, 185, 129, 0.8)', // Green
          'rgba(245, 158, 11, 0.8)', // Yellow
          'rgba(139, 92, 246, 0.8)', // Purple
        ],
        borderColor: [
          'rgba(59, 130, 246, 1)',
          'rgba(16, 185, 129, 1)',
          'rgba(245, 158, 11, 1)',
          'rgba(139, 92, 246, 1)',
        ],
        borderWidth: 2,
      },
    ],
  };

  const barChartOptions = {
    responsive: true,
    plugins: {
      legend: {
        display: false,
      },
      title: {
        display: true,
        text: 'Estadísticas Generales',
        font: {
          size: 16,
          weight: 'bold' as const,
        },
      },
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          stepSize: 1,
        },
      },
    },
  };

  const quickActions = [
    {
      title: 'Estudiantes',
      description: 'Gestionar estudiantes',
      icon: (
        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
        </svg>
      ),
      color: 'bg-blue-500 hover:bg-blue-600',
      path: '/estudiantes'
    },
    {
      title: 'Docentes',
      description: 'Gestionar docentes',
      icon: (
        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
      ),
      color: 'bg-green-500 hover:bg-green-600',
      path: '/docentes'
    },
    {
      title: 'Grupos',
      description: 'Gestionar grupos',
      icon: (
        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
      ),
      color: 'bg-purple-500 hover:bg-purple-600',
      path: '/grupos'
    },
    {
      title: 'Instituciones',
      description: 'Gestionar instituciones',
      icon: (
        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
      ),
      color: 'bg-yellow-500 hover:bg-yellow-600',
      path: '/instituciones'
    }
  ];

  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-3xl font-bold text-gray-900">Dashboard</h1>
          <p className="text-gray-600 mt-1">
            Bienvenido al sistema de gestión académica
          </p>
        </div>
        <Button
          variant="secondary"
          onClick={handleLogout}
          leftIcon={
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          }
        >
          Cerrar Sesión
        </Button>
      </div>

      {/* Estadísticas rápidas */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <Card>
          <CardBody>
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                  <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                  </svg>
                </div>
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-500">Estudiantes</p>
                <p className="text-2xl font-bold text-gray-900">{stats.students}</p>
              </div>
            </div>
          </CardBody>
        </Card>

        <Card>
          <CardBody>
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                  <svg className="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-500">Docentes</p>
                <p className="text-2xl font-bold text-gray-900">{stats.teachers}</p>
              </div>
            </div>
          </CardBody>
        </Card>

        <Card>
          <CardBody>
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                  <svg className="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-500">Instituciones</p>
                <p className="text-2xl font-bold text-gray-900">{stats.institutions}</p>
              </div>
            </div>
          </CardBody>
        </Card>

        <Card>
          <CardBody>
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                  <svg className="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-500">Grupos</p>
                <p className="text-2xl font-bold text-gray-900">{stats.groups}</p>
              </div>
            </div>
          </CardBody>
        </Card>
      </div>

      {/* Accesos rápidos */}
      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Accesos Rápidos</h2>
        </CardHeader>
        <CardBody>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {quickActions.map((action) => (
              <button
                key={action.title}
                onClick={() => navigate(action.path)}
                className={`p-4 rounded-lg text-white transition-colors duration-200 ${action.color}`}
              >
                <div className="flex items-center space-x-3">
                  {action.icon}
                  <div className="text-left">
                    <h3 className="font-semibold">{action.title}</h3>
                    <p className="text-sm opacity-90">{action.description}</p>
                  </div>
                </div>
              </button>
            ))}
          </div>
        </CardBody>
      </Card>

      {/* Gráficas */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card>
          <CardHeader>
            <h2 className="text-lg font-semibold text-gray-900">Distribución de Roles</h2>
          </CardHeader>
          <CardBody>
            <div className="h-64">
              <Pie data={pieChartData} options={pieChartOptions} />
            </div>
          </CardBody>
        </Card>

        <Card>
          <CardHeader>
            <h2 className="text-lg font-semibold text-gray-900">Estadísticas Generales</h2>
          </CardHeader>
          <CardBody>
            <div className="h-64">
              <Bar data={barChartData} options={barChartOptions} />
            </div>
          </CardBody>
        </Card>
      </div>

      {/* Notificaciones */}
      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Notificaciones del Sistema</h2>
        </CardHeader>
        <CardBody>
          <div className="space-y-4">
            <div className="flex items-start space-x-3 p-4 bg-blue-50 rounded-lg">
              <div className="flex-shrink-0">
                <svg className="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <h3 className="text-sm font-medium text-blue-800">Sistema Operativo</h3>
                <p className="text-sm text-blue-700 mt-1">
                  El sistema de gestión académica está funcionando correctamente. Todos los módulos están disponibles.
                </p>
              </div>
            </div>

            {stats.students === 0 && (
              <div className="flex items-start space-x-3 p-4 bg-yellow-50 rounded-lg">
                <div className="flex-shrink-0">
                  <svg className="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                  </svg>
                </div>
                <div>
                  <h3 className="text-sm font-medium text-yellow-800">Sin Estudiantes</h3>
                  <p className="text-sm text-yellow-700 mt-1">
                    No hay estudiantes registrados en el sistema. Considera agregar algunos estudiantes para comenzar.
                  </p>
                </div>
              </div>
            )}

            {stats.teachers === 0 && (
              <div className="flex items-start space-x-3 p-4 bg-orange-50 rounded-lg">
                <div className="flex-shrink-0">
                  <svg className="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                  </svg>
                </div>
                <div>
                  <h3 className="text-sm font-medium text-orange-800">Sin Docentes</h3>
                  <p className="text-sm text-orange-700 mt-1">
                    No hay docentes registrados en el sistema. Agrega docentes para poder asignarlos a grupos.
                  </p>
                </div>
              </div>
            )}
                     </div>
         </CardBody>
       </Card>

       {/* Información de autenticación */}
       <Card>
         <CardHeader>
           <h2 className="text-lg font-semibold text-gray-900">Estado de Autenticación</h2>
         </CardHeader>
         <CardBody>
           <div className="space-y-4">
             <div className="flex items-center justify-between">
               <span className="text-sm font-medium text-gray-500">Autenticado:</span>
               <span className={`inline-flex rounded-full px-2 text-xs font-semibold leading-5 ${
                 isAuthenticated ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
               }`}>
                 {isAuthenticated ? 'Sí' : 'No'}
               </span>
             </div>
             
             {user && (
               <>
                 <div className="flex items-center justify-between">
                   <span className="text-sm font-medium text-gray-500">Usuario:</span>
                   <span className="text-sm text-gray-900">{user.nombre} {user.apellido}</span>
                 </div>
                 <div className="flex items-center justify-between">
                   <span className="text-sm font-medium text-gray-500">Email:</span>
                   <span className="text-sm text-gray-900">{user.email}</span>
                 </div>
                 <div className="flex items-center justify-between">
                   <span className="text-sm font-medium text-gray-500">Institución:</span>
                   <span className="text-sm text-gray-900">{user.institucion?.nombre || 'No asignada'}</span>
                 </div>
                 <div className="flex items-center justify-between">
                   <span className="text-sm font-medium text-gray-500">Roles:</span>
                   <div className="flex gap-1">
                     {user.roles?.map((role) => (
                       <span
                         key={role.id}
                         className="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-blue-100 text-blue-800"
                       >
                         {role.nombre}
                       </span>
                     ))}
                   </div>
                 </div>
               </>
             )}
           </div>
         </CardBody>
       </Card>
     </div>
   );
 };

export default DashboardPage; 