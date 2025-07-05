import type { ReactNode } from 'react';
import { Navigate } from 'react-router-dom';
import { usePermission, useAnyPermission, useAllPermissions } from '../hooks/usePermission';

interface PermissionRouteProps {
  children: ReactNode;
  permission?: string;
  permissions?: string[];
  requireAll?: boolean;
  fallbackPath?: string;
  fallbackComponent?: ReactNode;
}

/**
 * Componente para proteger rutas según los permisos del usuario
 * 
 * @param permission - Permiso único requerido
 * @param permissions - Array de permisos requeridos
 * @param requireAll - Si es true, requiere todos los permisos. Si es false, requiere al menos uno
 * @param fallbackPath - Ruta de redirección si no tiene permisos
 * @param fallbackComponent - Componente a mostrar si no tiene permisos
 */
const PermissionRoute = ({
  children,
  permission,
  permissions,
  requireAll = false,
  fallbackPath = '/no-autorizado',
  fallbackComponent
}: PermissionRouteProps) => {
  let hasPermission = false;

  if (permission) {
    hasPermission = usePermission(permission);
  } else if (permissions && permissions.length > 0) {
    if (requireAll) {
      hasPermission = useAllPermissions(permissions);
    } else {
      hasPermission = useAnyPermission(permissions);
    }
  } else {
    // Si no se especifican permisos, permitir acceso
    hasPermission = true;
  }

  if (!hasPermission) {
    if (fallbackComponent) {
      return <>{fallbackComponent}</>;
    }
    return <Navigate to={fallbackPath} replace />;
  }

  return <>{children}</>;
};

export default PermissionRoute; 