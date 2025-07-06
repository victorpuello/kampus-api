import axiosClient from './axiosClient';

export const institucionesApi = {
  getById: (id: number) => axiosClient.get(`/instituciones/${id}`),
  // Puedes agregar más métodos aquí según lo necesites
}; 