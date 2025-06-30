import StudentForm from '../components/students/StudentForm';

const CreateStudentPage = () => {
  return (
    <div>
      <div className="sm:flex sm:items-center">
        <div className="sm:flex-auto">
          <h1 className="text-xl font-semibold text-gray-900">Crear Estudiante</h1>
          <p className="mt-2 text-sm text-gray-700">
            Complete el formulario para registrar un nuevo estudiante.
          </p>
        </div>
      </div>

      <div className="mt-8">
        <StudentForm />
      </div>
    </div>
  );
};

export default CreateStudentPage; 