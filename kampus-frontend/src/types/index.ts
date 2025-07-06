// Tipos para Sede
export interface Sede {
  id: number;
  nombre: string;
  direccion: string;
  telefono?: string;
  institucion_id: number;
  created_at: string;
  updated_at: string;
}

// Tipos para Institucion
export interface Institucion {
  id: number;
  nombre: string;
  siglas: string;
  slogan?: string;
  dane?: string;
  resolucion_aprobacion?: string;
  direccion?: string;
  telefono?: string;
  email?: string;
  rector?: string;
  escudo?: string;
  created_at: string;
  updated_at: string;
  sedes?: Sede[];
}

// Tipos para FranjaHoraria
export interface FranjaHoraria {
  id: number;
  nombre: string;
  descripcion?: string;
  hora_inicio: string;
  hora_fin: string;
  duracion_minutos: number;
  estado: string;
  institucion_id: number;
  created_at: string;
  updated_at: string;
  institucion?: Institucion;
}

// Exportar todos los tipos
// export * from './asignacion'; 