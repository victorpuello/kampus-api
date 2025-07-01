// Script para generar 50 usuarios de prueba
// Ejecutar en el navegador en la consola de desarrollador

async function generateTestUsers() {
  console.log('🚀 Iniciando generación de 50 usuarios de prueba...');
  
  const axiosClient = window.axiosClient || {
    post: async (url, data) => {
      const response = await fetch('http://kampus.test/api/v1' + url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('auth-storage') ? JSON.parse(localStorage.getItem('auth-storage')).state.token : ''}`
        },
        body: JSON.stringify(data)
      });
      return { data: await response.json(), status: response.status };
    }
  };

  // Datos de prueba
  const nombres = [
    'Ana', 'Carlos', 'María', 'Juan', 'Laura', 'Pedro', 'Sofia', 'Diego', 'Carmen', 'Luis',
    'Isabella', 'Andrés', 'Valentina', 'Miguel', 'Camila', 'Javier', 'Daniela', 'Roberto', 'Natalia', 'Fernando',
    'Gabriela', 'Ricardo', 'Paula', 'Alejandro', 'Monica', 'Eduardo', 'Patricia', 'Hector', 'Adriana', 'Manuel',
    'Claudia', 'Francisco', 'Elena', 'Rafael', 'Beatriz', 'Alberto', 'Lucia', 'Jorge', 'Rosa', 'Victor',
    'Teresa', 'Guillermo', 'Silvia', 'Mario', 'Angela', 'Oscar', 'Martha', 'Raul', 'Diana', 'Enrique'
  ];

  const apellidos = [
    'García', 'Rodríguez', 'González', 'Fernández', 'López', 'Martínez', 'Sánchez', 'Pérez', 'Gómez', 'Martin',
    'Jiménez', 'Ruiz', 'Hernández', 'Díaz', 'Moreno', 'Muñoz', 'Álvarez', 'Romero', 'Alonso', 'Gutiérrez',
    'Navarro', 'Torres', 'Domínguez', 'Vázquez', 'Ramos', 'Gil', 'Ramírez', 'Serrano', 'Blanco', 'Suárez',
    'Molina', 'Morales', 'Ortega', 'Delgado', 'Castro', 'Ortiz', 'Rubio', 'Marín', 'Sanz', 'Iglesias',
    'Medina', 'Cortés', 'Garrido', 'Castillo', 'Santos', 'Lozano', 'Guerrero', 'Cano', 'Prieto', 'Méndez'
  ];

  const especialidades = [
    'Matemáticas', 'Física', 'Química', 'Biología', 'Historia', 'Geografía', 'Literatura', 'Inglés', 'Arte', 'Música',
    'Educación Física', 'Informática', 'Economía', 'Filosofía', 'Psicología', 'Sociología', 'Derecho', 'Medicina', 'Ingeniería', 'Arquitectura'
  ];

  const instituciones = [
    { id: 1, nombre: 'Instituto Técnico Industrial' },
    { id: 2, nombre: 'Colegio San José' },
    { id: 3, nombre: 'Liceo Moderno' },
    { id: 4, nombre: 'Escuela Normal Superior' },
    { id: 5, nombre: 'Centro Educativo Integral' }
  ];

  const roles = [
    { id: 1, nombre: 'Administrador' },
    { id: 2, nombre: 'Docente' },
    { id: 3, nombre: 'Coordinador' },
    { id: 4, nombre: 'Secretario' },
    { id: 5, nombre: 'Auxiliar' }
  ];

  let successCount = 0;
  let errorCount = 0;

  for (let i = 0; i < 50; i++) {
    const nombre = nombres[i];
    const apellido = apellidos[i];
    const email = `${nombre.toLowerCase()}.${apellido.toLowerCase()}${i + 1}@institucion.edu.co`;
    const username = `${nombre.toLowerCase()}${apellido.toLowerCase()}${i + 1}`;
    const password = 'password123';
    const tipoDocumento = ['CC', 'TI', 'CE', 'PP'][Math.floor(Math.random() * 4)];
    const numeroDocumento = Math.floor(Math.random() * 99999999) + 10000000;
    const estado = Math.random() > 0.1 ? 'activo' : 'inactivo'; // 90% activos, 10% inactivos
    const institucion = instituciones[Math.floor(Math.random() * instituciones.length)];
    const rolesUsuario = [roles[Math.floor(Math.random() * roles.length)].id]; // 1 rol por usuario

    const userData = {
      nombre,
      apellido,
      email,
      username,
      password,
      tipo_documento: tipoDocumento,
      numero_documento: numeroDocumento.toString(),
      estado,
      institucion_id: institucion.id,
      roles: rolesUsuario
    };

    try {
      console.log(`📝 Creando usuario ${i + 1}/50: ${nombre} ${apellido}`);
      const response = await axiosClient.post('/users', userData);
      
      if (response.status === 201 || response.status === 200) {
        successCount++;
        console.log(`✅ Usuario creado exitosamente: ${nombre} ${apellido}`);
      } else {
        errorCount++;
        console.log(`❌ Error al crear usuario: ${nombre} ${apellido}`, response.data);
      }
    } catch (error) {
      errorCount++;
      console.log(`❌ Error al crear usuario: ${nombre} ${apellido}`, error.message);
    }

    // Pequeña pausa para no sobrecargar el servidor
    await new Promise(resolve => setTimeout(resolve, 100));
  }

  console.log('\n🎉 Generación completada!');
  console.log(`✅ Usuarios creados exitosamente: ${successCount}`);
  console.log(`❌ Errores: ${errorCount}`);
  console.log(`📊 Total procesados: ${successCount + errorCount}`);
  
  return { successCount, errorCount };
}

// Función para verificar la cantidad de usuarios existentes
async function checkUserCount() {
  try {
    const response = await fetch('http://kampus.test/api/v1/users', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('auth-storage') ? JSON.parse(localStorage.getItem('auth-storage')).state.token : ''}`
      }
    });
    const data = await response.json();
    console.log(`📊 Usuarios existentes en el sistema: ${data.data?.length || 0}`);
    return data.data?.length || 0;
  } catch (error) {
    console.log('❌ Error al verificar usuarios existentes:', error.message);
    return 0;
  }
}

// Función principal
async function main() {
  console.log('🔍 Verificando usuarios existentes...');
  const existingCount = await checkUserCount();
  
  if (existingCount > 0) {
    console.log(`⚠️  Ya existen ${existingCount} usuarios en el sistema`);
    const confirm = window.confirm(`¿Desea continuar y agregar 50 usuarios más? (Total: ${existingCount + 50})`);
    if (!confirm) {
      console.log('❌ Operación cancelada por el usuario');
      return;
    }
  }
  
  await generateTestUsers();
}

// Ejecutar el script
main().catch(console.error);

// Exportar funciones para uso manual
window.generateTestUsers = generateTestUsers;
window.checkUserCount = checkUserCount; 