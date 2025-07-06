import axiosClient from './axiosClient';

export const getAssignments = (params?: any) =>
  axiosClient.get('/asignaciones', { params });

export const getAssignment = (id: number) =>
  axiosClient.get(`/asignaciones/${id}`);

export const createAssignment = (data: any) =>
  axiosClient.post('/asignaciones', data);

export const updateAssignment = (id: number, data: any) =>
  axiosClient.put(`/asignaciones/${id}`, data);

export const deleteAssignment = (id: number) =>
  axiosClient.delete(`/asignaciones/${id}`);

export const getAssignmentsByGroup = (grupoId: number) =>
  axiosClient.get(`/asignaciones/grupo/${grupoId}`);

export const getAssignmentsByTeacher = (docenteId: number) =>
  axiosClient.get(`/asignaciones/docente/${docenteId}`);

export const getAssignmentConflicts = () =>
  axiosClient.get('/asignaciones/conflictos'); 