import { useParams } from 'react-router-dom';
import UserForm from '../components/users/UserForm';
import { Card, CardHeader, CardBody } from '../components/ui/Card';
import { PageHeader } from '../components/ui';

const EditUserPage = () => {
  const { id } = useParams<{ id: string }>();

  return (
    <div className="space-y-6">
      <PageHeader
        title="Editar Usuario"
        description="Modifique la información del usuario según sea necesario."
      />

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-gray-900">Información del Usuario</h2>
        </CardHeader>
        <CardBody>
          <UserForm userId={Number(id)} />
        </CardBody>
      </Card>
    </div>
  );
};

export default EditUserPage; 