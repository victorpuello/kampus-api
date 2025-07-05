import React from 'react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MemoryRouter, Route, Routes } from 'react-router-dom';
import AnioPeriodoDetailPage from '../AnioPeriodoDetailPage';

// Mock de dependencias
vi.mock('../../api/axiosClient', () => ({
  __esModule: true,
  default: {
    get: vi.fn(async () => ({ data: { id: 2, nombre: 'Periodo de Prueba', fecha_inicio: '2024-01-01', fecha_fin: '2024-03-31', anio_id: 1, anio: { id: 1, nombre: 'Año 2024', estado: 'activo', institucion: { id: 1, nombre: 'Colegio Central', siglas: 'CC' } } } })),
    delete: vi.fn(async () => ({})),
  },
}));

vi.mock('../../contexts/AlertContext', async () => {
  const actual = await vi.importActual<any>('../../contexts/AlertContext');
  return {
    ...actual,
    useAlertContext: () => ({
      showSuccess: vi.fn(),
      showError: vi.fn(),
    }),
  };
});

vi.mock('../../hooks/useConfirm', async () => {
  const actual = await vi.importActual<any>('../../hooks/useConfirm');
  return {
    ...actual,
    useConfirm: () => ({
      confirm: vi.fn(() => Promise.resolve(true)), // Simula confirmación positiva
    }),
  };
});

describe('AnioPeriodoDetailPage', () => {
  const setup = () => {
    return render(
      <MemoryRouter initialEntries={["/anios/1/periodos/2"]}>
        <Routes>
          <Route path="/anios/:anioId/periodos/:periodoId" element={<AnioPeriodoDetailPage />} />
          <Route path="/anios/:anioId/periodos" element={<div>Lista de periodos</div>} />
        </Routes>
      </MemoryRouter>
    );
  };

  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('simula la eliminación de un periodo y navega a la lista', async () => {
    setup();
    const user = userEvent.setup();
    // Esperar a que cargue el periodo
    expect(await screen.findByText('Periodo de Prueba')).toBeDefined();

    // Clic en el botón Eliminar Periodo
    const btnEliminar = screen.getByRole('button', { name: /eliminar periodo/i });
    await user.click(btnEliminar);

    // El confirm está mockeado para devolver true, así que debe llamar a DELETE y navegar
    await waitFor(() => {
      // Verifica que se navega a la lista
      expect(screen.getByText('Lista de periodos')).toBeDefined();
    });
  });

  it('no elimina si el usuario cancela la confirmación', async () => {
    // Mockear confirmación negativa
    vi.doMock('../../hooks/useConfirm', () => ({
      useConfirm: () => ({
        confirm: vi.fn(() => Promise.resolve(false))
      })
    }));
    setup();
    const user = userEvent.setup();
    expect(await screen.findByText('Periodo de Prueba')).toBeDefined();
    const btnEliminar = screen.getByRole('button', { name: /eliminar periodo/i });
    await user.click(btnEliminar);
    // No debe navegar
    await waitFor(() => {
      expect(screen.queryByText('Lista de periodos')).toBeNull();
    });
  });
}); 