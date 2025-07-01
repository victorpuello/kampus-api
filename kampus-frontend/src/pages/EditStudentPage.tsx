import { useParams } from 'react-router-dom';
import StudentForm from '../components/students/StudentForm';
import { Card, CardHeader, CardBody } from '../components/ui/Card';

const EditStudentPage = () => {
  const { id } = useParams<{ id: string }>();

  return (
    <div className="space-y-6">
      <div className="sm:flex sm:items-center">
        <div className="sm:flex-auto">
          <h1 className="text-xl font-semibold text-gray-900">Editar Estudiante</h1>
          <p className="mt-2 text-sm text-gray-700">
            Modifique la información del estudiante según sea necesario.
          </p>
        </div>
      </div>

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Estudiante</h2>
        </CardHeader>
        <CardBody>
          <StudentForm studentId={Number(id)} />
        </CardBody>
      </Card>
    </div>
  );
};

export default EditStudentPage; 