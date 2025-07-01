import GuardianForm from '../components/guardians/GuardianForm';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { PageHeader } from '../components/ui';

const CreateGuardianPage = () => {
  return (
    <div className="space-y-6">
      <PageHeader
        title="Crear Acudiente"
        description="Complete el formulario para registrar un nuevo acudiente en el sistema."
      />

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Acudiente</h2>
        </CardHeader>
        <CardBody>
          <GuardianForm />
        </CardBody>
      </Card>
    </div>
  );
};

export default CreateGuardianPage; 