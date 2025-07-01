import { useParams } from 'react-router-dom';
import TeacherForm from '../components/teachers/TeacherForm';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { PageHeader } from '../components/ui';

const EditTeacherPage = () => {
  const { id } = useParams<{ id: string }>();

  return (
    <div className="space-y-6">
      <PageHeader
        title="Editar Docente"
        description="Modifique la información del docente según sea necesario."
      />

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Docente</h2>
        </CardHeader>
        <CardBody>
          <TeacherForm teacherId={Number(id)} />
        </CardBody>
      </Card>
    </div>
  );
};

export default EditTeacherPage; 