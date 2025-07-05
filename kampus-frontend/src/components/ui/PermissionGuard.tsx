import type { ReactNode } from 'react';
import { usePermission, useAnyPermission, useAllPermissions } from '../../hooks/usePermission';

interface PermissionGuardProps {
  children: ReactNode;
  permission?: string;
  permissions?: string[];
  requireAll?: boolean;
  fallback?: ReactNode;
}

/**
 * Componente para mostrar/ocultar elementos según los permisos del usuario
 * 
 * @param permission - Permiso único requerido
 * @param permissions - Array de permisos requeridos
 * @param requireAll - Si es true, requiere todos los permisos. Si es false, requiere al menos uno
 * @param fallback - Elemento a mostrar si no tiene permisos (opcional)
 */
const PermissionGuard = ({
  children,
  permission,
  permissions,
  requireAll = false,
  fallback = null
}: PermissionGuardProps) => {
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
    // Si no se especifican permisos, mostrar el contenido
    hasPermission = true;
  }

  if (!hasPermission) {
    return <>{fallback}</>;
  }

  return <>{children}</>;
};

export default PermissionGuard; 