import UserForm from '../components/users/UserForm';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { PageHeader } from '../components/ui';

const CreateUserPage = () => {
  return (
    <div className="space-y-6">
      <PageHeader
        title="Crear Usuario"
        description="Complete el formulario para registrar un nuevo usuario en el sistema."
      />

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Usuario</h2>
        </CardHeader>
        <CardBody>
          <UserForm />
        </CardBody>
      </Card>
    </div>
  );
};

export default CreateUserPage; 