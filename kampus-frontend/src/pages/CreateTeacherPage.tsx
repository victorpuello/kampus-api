import TeacherForm from '../components/teachers/TeacherForm';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { PageHeader } from '../components/ui';

const CreateTeacherPage = () => {
  return (
    <div className="space-y-6">
      <PageHeader
        title="Crear Docente"
        description="Complete el formulario para registrar un nuevo docente en el sistema."
      />

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Docente</h2>
        </CardHeader>
        <CardBody>
          <TeacherForm />
        </CardBody>
      </Card>
    </div>
  );
};

export default CreateTeacherPage; 