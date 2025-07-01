import { useParams } from 'react-router-dom';
import GuardianForm from '../components/guardians/GuardianForm';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { PageHeader } from '../components/ui';

const EditGuardianPage = () => {
  const { id } = useParams<{ id: string }>();

  return (
    <div className="space-y-6">
      <PageHeader
        title="Editar Acudiente"
        description="Modifique la información del acudiente según sea necesario."
      />

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Acudiente</h2>
        </CardHeader>
        <CardBody>
          <GuardianForm guardianId={Number(id)} />
        </CardBody>
      </Card>
    </div>
  );
};

export default EditGuardianPage; 