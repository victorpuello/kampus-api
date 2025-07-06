import { useEffect, useState } from 'react';
import { getAssignments } from '../api/assignmentsApi';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { Button } from '../components/ui/Button';

interface Assignment {
  id: number;
  docente: any;
  asignatura: any;
  grupo: any;
  franja_horaria: any;
  dia_semana: string;
  anio_academico: any;
  periodo: any;
  estado: string;
}

export default function AssignmentsListPage() {
  const [assignments, setAssignments] = useState<Assignment[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getAssignments()
      .then(res => setAssignments(res.data.data))
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="max-w-6xl mx-auto py-8">
      <Card>
        <CardHeader>
          <div className="flex justify-between items-center">
            <h1 className="text-2xl font-bold">Asignaciones Académicas</h1>
            <Button>+ Nueva Asignación</Button>
          </div>
        </CardHeader>
        <CardBody>
          {loading ? (
            <div>Cargando...</div>
          ) : (
            <table className="min-w-full text-sm">
              <thead>
                <tr>
                  <th>Docente</th>
                  <th>Asignatura</th>
                  <th>Grupo</th>
                  <th>Franja</th>
                  <th>Día</th>
                  <th>Año</th>
                  <th>Periodo</th>
                  <th>Estado</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                {assignments.map(a => (
                  <tr key={a.id}>
                    <td>{a.docente?.user?.nombre} {a.docente?.user?.apellido}</td>
                    <td>{a.asignatura?.nombre}</td>
                    <td>{a.grupo?.nombre}</td>
                    <td>{a.franja_horaria?.nombre}</td>
                    <td>{a.dia_semana}</td>
                    <td>{a.anio_academico?.nombre}</td>
                    <td>{a.periodo?.nombre || '-'}</td>
                    <td>{a.estado}</td>
                    <td>
                      <Button size="sm" variant="secondary">Editar</Button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          )}
        </CardBody>
      </Card>
    </div>
  );
} 