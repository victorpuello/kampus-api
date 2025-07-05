import { useAuthStore } from '../store/authStore';

/**
 * Hook para verificar si el usuario tiene un permiso específico
 * @param permission - Nombre del permiso a verificar
 * @returns boolean - true si el usuario tiene el permiso, false en caso contrario
 */
export function usePermission(permission: string): boolean {
  const user = useAuthStore((state) => state.user);
  
  if (!user || !user.roles) {
    return false;
  }

  // Verificar si el usuario tiene el permiso a través de sus roles
  return user.roles.some(role =>
    role.permissions?.some(perm => perm.nombre === permission)
  );
}

/**
 * Hook para verificar si el usuario tiene al menos uno de los permisos especificados
 * @param permissions - Array de nombres de permisos a verificar
 * @returns boolean - true si el usuario tiene al menos uno de los permisos
 */
export function useAnyPermission(permissions: string[]): boolean {
  const user = useAuthStore((state) => state.user);
  
  if (!user || !user.roles) {
    return false;
  }

  return permissions.some(permission =>
    user.roles.some(role =>
      role.permissions?.some(perm => perm.nombre === permission)
    )
  );
}

/**
 * Hook para verificar si el usuario tiene todos los permisos especificados
 * @param permissions - Array de nombres de permisos a verificar
 * @returns boolean - true si el usuario tiene todos los permisos
 */
export function useAllPermissions(permissions: string[]): boolean {
  const user = useAuthStore((state) => state.user);
  
  if (!user || !user.roles) {
    return false;
  }

  return permissions.every(permission =>
    user.roles.some(role =>
      role.permissions?.some(perm => perm.nombre === permission)
    )
  );
}

/**
 * Hook para verificar si el usuario tiene un rol específico
 * @param roleName - Nombre del rol a verificar
 * @returns boolean - true si el usuario tiene el rol
 */
export function useRole(roleName: string): boolean {
  const user = useAuthStore((state) => state.user);
  
  if (!user || !user.roles) {
    return false;
  }

  return user.roles.some(role => role.nombre === roleName);
}

/**
 * Hook para obtener todos los permisos del usuario
 * @returns string[] - Array con todos los nombres de permisos del usuario
 */
export function useUserPermissions(): string[] {
  const user = useAuthStore((state) => state.user);
  
  if (!user || !user.roles) {
    return [];
  }

  const permissions = new Set<string>();
  
  user.roles.forEach(role => {
    role.permissions?.forEach(permission => {
      permissions.add(permission.nombre);
    });
  });

  return Array.from(permissions);
} 