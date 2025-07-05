<?php

namespace Database\Seeders;

use App\Models\Institucion;
use App\Models\Sede;
use Illuminate\Database\Seeder;

class InstitucionesConSedesSeeder extends Seeder
{
    public function run(): void
    {
        // Institución 1: Institución Educativa San José
        $institucion1 = Institucion::create([
            'nombre' => 'Institución Educativa San José',
            'siglas' => 'IESJ',
            'slogan' => 'Educando con valores para el futuro',
            'dane' => '123456789',
            'resolucion_aprobacion' => 'Resolución 1234 de 2020',
            'direccion' => 'Calle 15 #23-45, Centro',
            'telefono' => '3001234567',
            'email' => 'info@iesj.edu.co',
            'rector' => 'Dr. Carlos Mendoza',
            'escudo' => 'escudos/iesj.png',
        ]);

        // Sedes para IESJ
        Sede::create([
            'institucion_id' => $institucion1->id,
            'nombre' => 'Sede Principal - San José',
            'direccion' => 'Calle 15 #23-45, Centro',
            'telefono' => '3001234567',
        ]);

        Sede::create([
            'institucion_id' => $institucion1->id,
            'nombre' => 'Sede Norte - La Esperanza',
            'direccion' => 'Carrera 8 #12-34, Barrio La Esperanza',
            'telefono' => '3001234568',
        ]);

        Sede::create([
            'institucion_id' => $institucion1->id,
            'nombre' => 'Sede Sur - El Progreso',
            'direccion' => 'Calle 25 #45-67, Barrio El Progreso',
            'telefono' => '3001234569',
        ]);

        // Institución 2: Colegio Santa María
        $institucion2 = Institucion::create([
            'nombre' => 'Colegio Santa María',
            'siglas' => 'CSM',
            'slogan' => 'Formando líderes con excelencia académica',
            'dane' => '987654321',
            'resolucion_aprobacion' => 'Resolución 5678 de 2019',
            'direccion' => 'Carrera 12 #34-56, Zona Norte',
            'telefono' => '3009876543',
            'email' => 'contacto@csm.edu.co',
            'rector' => 'Lic. Ana María Rodríguez',
            'escudo' => 'escudos/csm.png',
        ]);

        // Sedes para CSM
        Sede::create([
            'institucion_id' => $institucion2->id,
            'nombre' => 'Sede Principal - Santa María',
            'direccion' => 'Carrera 12 #34-56, Zona Norte',
            'telefono' => '3009876543',
        ]);

        Sede::create([
            'institucion_id' => $institucion2->id,
            'nombre' => 'Sede Secundaria - Los Pinos',
            'direccion' => 'Calle 45 #67-89, Barrio Los Pinos',
            'telefono' => '3009876544',
        ]);

        // Institución 3: Liceo Moderno
        $institucion3 = Institucion::create([
            'nombre' => 'Liceo Moderno',
            'siglas' => 'LM',
            'slogan' => 'Innovación y tecnología al servicio de la educación',
            'dane' => '456789123',
            'resolucion_aprobacion' => 'Resolución 9012 de 2021',
            'direccion' => 'Avenida Principal #78-90, Zona Industrial',
            'telefono' => '3004567890',
            'email' => 'info@licemoderno.edu.co',
            'rector' => 'Ing. Roberto Silva',
            'escudo' => 'escudos/lm.png',
        ]);

        // Sedes para LM
        Sede::create([
            'institucion_id' => $institucion3->id,
            'nombre' => 'Sede Principal - Liceo Moderno',
            'direccion' => 'Avenida Principal #78-90, Zona Industrial',
            'telefono' => '3004567890',
        ]);

        Sede::create([
            'institucion_id' => $institucion3->id,
            'nombre' => 'Sede Tecnológica - Parque Industrial',
            'direccion' => 'Calle 90 #12-34, Parque Industrial',
            'telefono' => '3004567891',
        ]);

        Sede::create([
            'institucion_id' => $institucion3->id,
            'nombre' => 'Sede Virtual - Centro de Innovación',
            'direccion' => 'Carrera 45 #67-89, Centro de Innovación',
            'telefono' => '3004567892',
        ]);

        // Institución 4: Instituto Técnico Comercial
        $institucion4 = Institucion::create([
            'nombre' => 'Instituto Técnico Comercial',
            'siglas' => 'ITC',
            'slogan' => 'Preparando profesionales para el mundo empresarial',
            'dane' => '789123456',
            'resolucion_aprobacion' => 'Resolución 3456 de 2018',
            'direccion' => 'Calle 30 #45-67, Zona Comercial',
            'telefono' => '3007891234',
            'email' => 'administracion@itc.edu.co',
            'rector' => 'Lic. Patricia Gómez',
            'escudo' => 'escudos/itc.png',
        ]);

        // Sedes para ITC
        Sede::create([
            'institucion_id' => $institucion4->id,
            'nombre' => 'Sede Principal - ITC',
            'direccion' => 'Calle 30 #45-67, Zona Comercial',
            'telefono' => '3007891234',
        ]);

        Sede::create([
            'institucion_id' => $institucion4->id,
            'nombre' => 'Sede Comercial - Centro Empresarial',
            'direccion' => 'Carrera 20 #30-45, Centro Empresarial',
            'telefono' => '3007891235',
        ]);

        Sede::create([
            'institucion_id' => $institucion4->id,
            'nombre' => 'Sede Técnica - Zona Industrial',
            'direccion' => 'Avenida Industrial #15-30, Zona Industrial',
            'telefono' => '3007891236',
        ]);

        // Institución 5: Colegio Bilingüe Internacional
        $institucion5 = Institucion::create([
            'nombre' => 'Colegio Bilingüe Internacional',
            'siglas' => 'CBI',
            'slogan' => 'Educación bilingüe para un mundo global',
            'dane' => '321654987',
            'resolucion_aprobacion' => 'Resolución 7890 de 2022',
            'direccion' => 'Carrera 50 #70-90, Zona Residencial',
            'telefono' => '3003216549',
            'email' => 'info@cbi.edu.co',
            'rector' => 'Dr. Michael Johnson',
            'escudo' => 'escudos/cbi.png',
        ]);

        // Sedes para CBI
        Sede::create([
            'institucion_id' => $institucion5->id,
            'nombre' => 'Sede Principal - CBI',
            'direccion' => 'Carrera 50 #70-90, Zona Residencial',
            'telefono' => '3003216549',
        ]);

        Sede::create([
            'institucion_id' => $institucion5->id,
            'nombre' => 'Sede Internacional - Campus Norte',
            'direccion' => 'Calle 80 #100-120, Campus Norte',
            'telefono' => '3003216550',
        ]);

        Sede::create([
            'institucion_id' => $institucion5->id,
            'nombre' => 'Sede Deportiva - Centro Recreacional',
            'direccion' => 'Avenida Deportiva #25-40, Centro Recreacional',
            'telefono' => '3003216551',
        ]);

        Sede::create([
            'institucion_id' => $institucion5->id,
            'nombre' => 'Sede Cultural - Teatro Municipal',
            'direccion' => 'Calle 60 #80-100, Teatro Municipal',
            'telefono' => '3003216552',
        ]);

        $this->command->info('✅ Se han creado 5 instituciones con sus respectivas sedes:');
        $this->command->info('   • Institución Educativa San José (IESJ) - 3 sedes');
        $this->command->info('   • Colegio Santa María (CSM) - 2 sedes');
        $this->command->info('   • Liceo Moderno (LM) - 3 sedes');
        $this->command->info('   • Instituto Técnico Comercial (ITC) - 3 sedes');
        $this->command->info('   • Colegio Bilingüe Internacional (CBI) - 4 sedes');
        $this->command->info('   Total: 5 instituciones y 15 sedes creadas');
    }
} 