// Script para debuggear el login desde la consola del navegador
// Copiar y pegar este código en la consola del navegador en http://localhost:5173

async function debugLogin() {
  console.log('=== DEBUG LOGIN ===');
  
  // Verificar si el store está disponible
  if (typeof window !== 'undefined' && window.useAuthStore) {
    console.log('Store disponible:', window.useAuthStore);
    console.log('Estado actual:', window.useAuthStore.getState());
  } else {
    console.log('Store no disponible en window');
  }
  
  // Probar conexión directa con axios
  try {
    console.log('Probando conexión con axios...');
    const response = await fetch('http://kampus.test/api/v1/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        email: 'admin@example.com',
        password: 'password'
      })
    });
    
    console.log('Status:', response.status);
    console.log('Headers:', response.headers);
    
    const data = await response.json();
    console.log('Data:', data);
    
    if (response.ok) {
      console.log('✅ Login exitoso');
      return data;
    } else {
      console.log('❌ Error en login:', data);
      return null;
    }
  } catch (error) {
    console.error('❌ Error de conexión:', error);
    return null;
  }
}

// Función para probar el store de Zustand
async function testZustandStore() {
  console.log('=== TEST ZUSTAND STORE ===');
  
  // Simular el store
  let store = {
    token: null,
    user: null,
    isAuthenticated: false
  };
  
  console.log('Estado inicial:', store);
  
  // Probar login
  const loginResult = await debugLogin();
  
  if (loginResult) {
    store = {
      token: loginResult.token,
      user: loginResult.user,
      isAuthenticated: true
    };
    
    console.log('Estado después del login:', store);
    return store;
  }
  
  return null;
}

// Ejecutar el debug
debugLogin().then(result => {
  if (result) {
    console.log('🎉 Login funcionando correctamente');
  } else {
    console.log('💥 Login falló');
  }
});

// También probar el store
testZustandStore(); 