import { useParams } from 'react-router-dom';
import StudentForm from '../components/students/StudentForm';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { PageHeader } from '../components/ui';

const EditStudentPage = () => {
  const { id } = useParams<{ id: string }>();

  return (
    <div className="space-y-6">
      <PageHeader
        title="Editar Estudiante"
        description="Modifique la información del estudiante según sea necesario."
      />

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