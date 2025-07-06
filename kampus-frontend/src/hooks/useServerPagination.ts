import { useState, useEffect, useCallback, useRef } from 'react';
import axiosClient from '../api/axiosClient';
import { useAuthStore } from '../store/authStore';

interface PaginationMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
}

interface PaginationLinks {
  first: string;
  last: string;
  prev: string | null;
  next: string | null;
}

interface ServerResponse<T> {
  data: T[];
  meta: PaginationMeta;
  links: PaginationLinks;
}

interface UseServerPaginationOptions<T> {
  endpoint: string;
  initialPage?: number;
  initialPerPage?: number;
  initialSearch?: string;
  initialSortColumn?: string | null;
  initialSortDirection?: 'asc' | 'desc';
  searchKeys?: string[];
  additionalParams?: Record<string, string | number>;
}

interface UseServerPaginationReturn<T> {
  data: T[];
  loading: boolean;
  error: string | null;
  currentPage: number;
  itemsPerPage: number;
  totalItems: number;
  totalPages: number;
  searchTerm: string;
  sortColumn: string | null;
  sortDirection: 'asc' | 'desc';
  handlePageChange: (page: number) => void;
  handleItemsPerPageChange: (perPage: number) => void;
  handleSearch: (search: string) => void;
  handleSort: (columnKey: string, direction: 'asc' | 'desc') => void;
  refreshData: () => void;
  clearError: () => void;
}

export function useServerPagination<T>({
  endpoint,
  initialPage = 1,
  initialPerPage = 10,
  initialSearch = '',
  initialSortColumn = null,
  initialSortDirection = 'asc',
  searchKeys = [],
  additionalParams = {}
}: UseServerPaginationOptions<T>): UseServerPaginationReturn<T> {
  const [data, setData] = useState<T[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [currentPage, setCurrentPage] = useState(initialPage);
  const [itemsPerPage, setItemsPerPage] = useState(initialPerPage);
  const [totalItems, setTotalItems] = useState(0);
  const [totalPages, setTotalPages] = useState(0);
  const [searchTerm, setSearchTerm] = useState(initialSearch);
  const [pendingSearchTerm, setPendingSearchTerm] = useState(initialSearch);
  const [sortColumn, setSortColumn] = useState<string | null>(initialSortColumn);
  const [sortDirection, setSortDirection] = useState<'asc' | 'desc'>(initialSortDirection);

  // Obtener el token del store de autenticación
  const token = useAuthStore((state) => state.token);
  const isAuthenticated = useAuthStore((state) => state.isAuthenticated);

  // Ref para evitar peticiones duplicadas
  const isFetchingRef = useRef(false);
  const lastRequestRef = useRef<string>('');

  const fetchData = useCallback(async (
    page: number,
    perPage: number,
    search: string,
    sortBy?: string,
    sortDir?: 'asc' | 'desc'
  ) => {
    // Verificar si hay autenticación antes de hacer la petición
    if (!token || !isAuthenticated) {
      console.log('🔒 No hay token válido, esperando autenticación...');
      setLoading(false);
      return;
    }

    // Crear una clave única para esta petición
    const requestKey = `${page}-${perPage}-${search}-${sortBy}-${sortDir}`;
    
    // Evitar peticiones duplicadas
    if (isFetchingRef.current || lastRequestRef.current === requestKey) {
      console.log('🔄 Petición duplicada evitada:', requestKey);
      return;
    }

    try {
      isFetchingRef.current = true;
      lastRequestRef.current = requestKey;
      setLoading(true);
      
      const params = new URLSearchParams({
        page: page.toString(),
        per_page: perPage.toString(),
      });

      // Agregar parámetros adicionales
      Object.entries(additionalParams).forEach(([key, value]) => {
        params.append(key, value.toString());
      });

      if (search) {
        params.append('search', search);
      }

      if (sortBy) {
        params.append('sort_by', sortBy);
        params.append('sort_direction', sortDir || 'asc');
      }

      // LOGS DE DEPURACIÓN
      console.log('🔑 Token actual:', token);
      console.log('🟢 isAuthenticated:', isAuthenticated);
      console.log('🔢 currentPage:', page, 'perPage:', perPage, 'search:', search, 'sortBy:', sortBy, 'sortDir:', sortDir);
      console.log(`🔍 Haciendo petición a: ${endpoint}?${params.toString()}`);
      
      const response = await axiosClient.get(`${endpoint}?${params.toString()}`);
      const responseData: ServerResponse<T> = response.data;

      setData(responseData.data);
      setTotalItems(responseData.meta.total);
      setTotalPages(responseData.meta.last_page);
      setCurrentPage(responseData.meta.current_page);
      setItemsPerPage(responseData.meta.per_page);
      setError(null);
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Error al cargar los datos';
      setError(errorMessage);
      console.error('❌ Error en fetchData:', errorMessage);
    } finally {
      setLoading(false);
      isFetchingRef.current = false;
    }
  }, [endpoint, additionalParams, token, isAuthenticated]);

  // Efecto para cargar datos iniciales
  useEffect(() => {
    if (token && isAuthenticated) {
      fetchData(currentPage, itemsPerPage, searchTerm, sortColumn || undefined, sortDirection);
    } else {
      setData([]);
      setLoading(false);
      setError(null);
    }
  }, [token, isAuthenticated]); // Solo dependencias de autenticación

  // Efecto separado para cambios en paginación, búsqueda y ordenamiento
  useEffect(() => {
    if (token && isAuthenticated && !isFetchingRef.current) {
      fetchData(currentPage, itemsPerPage, searchTerm, sortColumn || undefined, sortDirection);
    }
  }, [currentPage, itemsPerPage, searchTerm, sortColumn, sortDirection, fetchData]);

  // Efecto para aplicar debounce a la búsqueda
  useEffect(() => {
    const handler = setTimeout(() => {
      setSearchTerm(pendingSearchTerm);
      setCurrentPage(1);
    }, 400); // 400ms debounce
    return () => clearTimeout(handler);
  }, [pendingSearchTerm]);

  const handlePageChange = useCallback((page: number) => {
    setCurrentPage(page);
  }, []);

  const handleItemsPerPageChange = useCallback((perPage: number) => {
    setItemsPerPage(perPage);
    setCurrentPage(1);
  }, []);

  const handleSearch = useCallback((search: string) => {
    setPendingSearchTerm(search);
  }, []);

  const handleSort = useCallback((columnKey: string, direction: 'asc' | 'desc') => {
    setSortColumn(columnKey);
    setSortDirection(direction);
    setCurrentPage(1);
  }, []);

  const refreshData = useCallback(() => {
    if (token && isAuthenticated) {
      // Limpiar la última petición para forzar una nueva
      lastRequestRef.current = '';
      fetchData(currentPage, itemsPerPage, searchTerm, sortColumn || undefined, sortDirection);
    }
  }, [fetchData, currentPage, itemsPerPage, searchTerm, sortColumn, sortDirection, token, isAuthenticated]);

  const clearError = useCallback(() => {
    setError(null);
  }, []);

  return {
    data,
    loading,
    error,
    currentPage,
    itemsPerPage,
    totalItems,
    totalPages,
    searchTerm,
    sortColumn,
    sortDirection,
    handlePageChange,
    handleItemsPerPageChange,
    handleSearch,
    handleSort,
    refreshData,
    clearError,
  };
} 