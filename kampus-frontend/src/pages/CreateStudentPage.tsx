import StudentForm from '../components/students/StudentForm';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { PageHeader } from '../components/ui';

const CreateStudentPage = () => {
  return (
    <div className="space-y-6">
      <PageHeader
        title="Crear Estudiante"
        description="Complete el formulario para registrar un nuevo estudiante en el sistema."
      />

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Estudiante</h2>
        </CardHeader>
        <CardBody>
          <StudentForm />
        </CardBody>
      </Card>
    </div>
  );
};

export default CreateStudentPage; 