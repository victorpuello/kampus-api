<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;
use App\Models\Asignatura;
use App\Models\Institucion;

class AreaAsignaturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeder de áreas y asignaturas...');

        $instituciones = Institucion::all();
        $this->command->info("📊 Procesando {$instituciones->count()} instituciones...");

        foreach ($instituciones as $institucion) {
            $this->command->info("\n🏫 Procesando: {$institucion->nombre}");
            
            // Crear áreas
            $areas = $this->crearAreas($institucion);
            
            // Crear asignaturas para cada área
            foreach ($areas as $area) {
                $this->crearAsignaturas($area);
            }
        }

        $this->command->info("\n🎉 ¡Seeder de áreas y asignaturas completado!");
        $this->command->info("📊 Total áreas creadas: " . Area::count());
        $this->command->info("📚 Total asignaturas creadas: " . Asignatura::count());
    }

    private function crearAreas(Institucion $institucion): array
    {
        $areasData = [
            [
                'nombre' => 'Matemáticas',
                'descripcion' => 'Área de matemáticas y ciencias exactas',
                'color' => '#FF6B6B'
            ],
            [
                'nombre' => 'Lenguaje',
                'descripcion' => 'Área de comunicación y lenguaje',
                'color' => '#4ECDC4'
            ],
            [
                'nombre' => 'Ciencias Naturales',
                'descripcion' => 'Área de ciencias naturales y experimentales',
                'color' => '#45B7D1'
            ],
            [
                'nombre' => 'Ciencias Sociales',
                'descripcion' => 'Área de ciencias sociales e historia',
                'color' => '#96CEB4'
            ],
            [
                'nombre' => 'Educación Física',
                'descripcion' => 'Área de educación física y deportes',
                'color' => '#FFEAA7'
            ],
            [
                'nombre' => 'Arte y Cultura',
                'descripcion' => 'Área de artes plásticas y música',
                'color' => '#DDA0DD'
            ],
            [
                'nombre' => 'Tecnología e Informática',
                'descripcion' => 'Área de tecnología e informática',
                'color' => '#98D8C8'
            ],
            [
                'nombre' => 'Inglés',
                'descripcion' => 'Área de idioma extranjero inglés',
                'color' => '#F7DC6F'
            ],
            [
                'nombre' => 'Ética y Valores',
                'descripcion' => 'Área de ética, valores y religión',
                'color' => '#BB8FCE'
            ],
            [
                'nombre' => 'Emprendimiento',
                'descripcion' => 'Área de emprendimiento y proyectos',
                'color' => '#F8C471'
            ]
        ];

        $areas = [];
        foreach ($areasData as $areaData) {
            $area = Area::create([
                'nombre' => $areaData['nombre'],
                'descripcion' => $areaData['descripcion'],
                'color' => $areaData['color'],
                'institucion_id' => $institucion->id
            ]);
            $areas[] = $area;
            $this->command->info("  ✅ Área: {$area->nombre}");
        }

        return $areas;
    }

    private function crearAsignaturas(Area $area): void
    {
        $asignaturasData = $this->getAsignaturasPorArea($area->nombre);
        $total = count($asignaturasData);
        $porcentaje = $total > 0 ? round(100 / $total, 2) : 100;

        foreach ($asignaturasData as $asignaturaData) {
            $asignatura = Asignatura::create([
                'nombre' => $asignaturaData['nombre'],
                'codigo' => $asignaturaData['codigo'],
                'descripcion' => $asignaturaData['descripcion'],
                'area_id' => $area->id,
                'institucion_id' => $area->institucion_id,
                'porcentaje_area' => $porcentaje
            ]);
            $this->command->info("    📚 Asignatura: {$asignatura->nombre} ({$porcentaje}%)");
        }
    }

    private function getAsignaturasPorArea(string $areaNombre): array
    {
        $asignaturas = [
            'Matemáticas' => [
                ['nombre' => 'Matemáticas', 'codigo' => 'MAT', 'descripcion' => 'Matemáticas básicas'],
                ['nombre' => 'Álgebra', 'codigo' => 'ALG', 'descripcion' => 'Álgebra y ecuaciones'],
                ['nombre' => 'Geometría', 'codigo' => 'GEO', 'descripcion' => 'Geometría y trigonometría'],
                ['nombre' => 'Cálculo', 'codigo' => 'CAL', 'descripcion' => 'Cálculo diferencial e integral'],
                ['nombre' => 'Estadística', 'codigo' => 'EST', 'descripcion' => 'Estadística y probabilidad']
            ],
            'Lenguaje' => [
                ['nombre' => 'Lengua Castellana', 'codigo' => 'LEN', 'descripcion' => 'Lengua castellana y literatura'],
                ['nombre' => 'Comprensión Lectora', 'codigo' => 'CL', 'descripcion' => 'Comprensión lectora y análisis'],
                ['nombre' => 'Expresión Oral', 'codigo' => 'EO', 'descripcion' => 'Expresión oral y comunicación'],
                ['nombre' => 'Escritura', 'codigo' => 'ESC', 'descripcion' => 'Escritura y composición'],
                ['nombre' => 'Literatura', 'codigo' => 'LIT', 'descripcion' => 'Literatura universal']
            ],
            'Ciencias Naturales' => [
                ['nombre' => 'Ciencias Naturales', 'codigo' => 'CN', 'descripcion' => 'Ciencias naturales generales'],
                ['nombre' => 'Biología', 'codigo' => 'BIO', 'descripcion' => 'Biología y ciencias de la vida'],
                ['nombre' => 'Química', 'codigo' => 'QUI', 'descripcion' => 'Química y reacciones químicas'],
                ['nombre' => 'Física', 'codigo' => 'FIS', 'descripcion' => 'Física y fenómenos naturales'],
                ['nombre' => 'Ciencias de la Tierra', 'codigo' => 'CT', 'descripcion' => 'Ciencias de la tierra y el espacio']
            ],
            'Ciencias Sociales' => [
                ['nombre' => 'Ciencias Sociales', 'codigo' => 'CS', 'descripcion' => 'Ciencias sociales generales'],
                ['nombre' => 'Historia', 'codigo' => 'HIS', 'descripcion' => 'Historia universal y de Colombia'],
                ['nombre' => 'Geografía', 'codigo' => 'GEO', 'descripcion' => 'Geografía física y política'],
                ['nombre' => 'Constitución Política', 'codigo' => 'CP', 'descripcion' => 'Constitución política y democracia'],
                ['nombre' => 'Economía', 'codigo' => 'ECO', 'descripcion' => 'Economía y finanzas personales']
            ],
            'Educación Física' => [
                ['nombre' => 'Educación Física', 'codigo' => 'EF', 'descripcion' => 'Educación física y deportes'],
                ['nombre' => 'Deportes', 'codigo' => 'DEP', 'descripcion' => 'Deportes y actividades físicas'],
                ['nombre' => 'Recreación', 'codigo' => 'REC', 'descripcion' => 'Recreación y tiempo libre']
            ],
            'Arte y Cultura' => [
                ['nombre' => 'Artes Plásticas', 'codigo' => 'AP', 'descripcion' => 'Artes plásticas y visuales'],
                ['nombre' => 'Música', 'codigo' => 'MUS', 'descripcion' => 'Música y expresión artística'],
                ['nombre' => 'Teatro', 'codigo' => 'TEA', 'descripcion' => 'Teatro y expresión dramática'],
                ['nombre' => 'Danza', 'codigo' => 'DAN', 'descripcion' => 'Danza y expresión corporal']
            ],
            'Tecnología e Informática' => [
                ['nombre' => 'Tecnología', 'codigo' => 'TEC', 'descripcion' => 'Tecnología e innovación'],
                ['nombre' => 'Informática', 'codigo' => 'INF', 'descripcion' => 'Informática y computación'],
                ['nombre' => 'Programación', 'codigo' => 'PRO', 'descripcion' => 'Programación y desarrollo'],
                ['nombre' => 'Diseño Digital', 'codigo' => 'DD', 'descripcion' => 'Diseño digital y multimedia']
            ],
            'Inglés' => [
                ['nombre' => 'Inglés', 'codigo' => 'ING', 'descripcion' => 'Idioma inglés'],
                ['nombre' => 'Conversación en Inglés', 'codigo' => 'CI', 'descripcion' => 'Conversación y comunicación en inglés'],
                ['nombre' => 'Gramática Inglesa', 'codigo' => 'GI', 'descripcion' => 'Gramática y estructura del inglés']
            ],
            'Ética y Valores' => [
                ['nombre' => 'Ética y Valores', 'codigo' => 'EV', 'descripcion' => 'Ética y valores humanos'],
                ['nombre' => 'Religión', 'codigo' => 'REL', 'descripcion' => 'Religión y formación espiritual'],
                ['nombre' => 'Convivencia', 'codigo' => 'CON', 'descripcion' => 'Convivencia y paz']
            ],
            'Emprendimiento' => [
                ['nombre' => 'Emprendimiento', 'codigo' => 'EMP', 'descripcion' => 'Emprendimiento y proyectos'],
                ['nombre' => 'Proyectos', 'codigo' => 'PRO', 'descripcion' => 'Gestión de proyectos'],
                ['nombre' => 'Liderazgo', 'codigo' => 'LID', 'descripcion' => 'Liderazgo y trabajo en equipo']
            ]
        ];

        return $asignaturas[$areaNombre] ?? [];
    }
} 