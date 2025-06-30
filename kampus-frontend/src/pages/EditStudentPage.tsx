import { useParams } from 'react-router-dom';
import StudentForm from '../components/students/StudentForm';

const EditStudentPage = () => {
  const { id } = useParams<{ id: string }>();

  return (
    <div>
      <div className="sm:flex sm:items-center">
        <div className="sm:flex-auto">
          <h1 className="text-xl font-semibold text-gray-900">Editar Estudiante</h1>
          <p className="mt-2 text-sm text-gray-700">
            Modifique la información del estudiante según sea necesario.
          </p>
        </div>
      </div>

      <div className="mt-8">
        <StudentForm studentId={Number(id)} />
      </div>
    </div>
  );
};

export default EditStudentPage; 