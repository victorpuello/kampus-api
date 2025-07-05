# 🗄️ Guía de Base de Datos - Kampus

Documentación completa de la base de datos, modelos y relaciones del sistema Kampus.

## 📋 Tabla de Contenidos

1. [Visión General](#visión-general)
2. [Estructura de la Base de Datos](#estructura-de-la-base-de-datos)
3. [Modelos y Relaciones](#modelos-y-relaciones)
4. [Migraciones](#migraciones)
5. [Seeders](#seeders)
6. [Factories](#factories)
7. [Consultas Comunes](#consultas-comunes)
8. [Optimización](#optimización)

## 🎯 Visión General

La base de datos de Kampus está diseñada para manejar un sistema de gestión académica completo con las siguientes características:

- **MySQL 8.0+** como motor de base de datos
- **Eloquent ORM** para el mapeo objeto-relacional
- **Migraciones** para control de versiones
- **Seeders** para datos de prueba
- **Factories** para generación de datos

## 🏗️ Estructura de la Base de Datos

### Diagrama de Entidades

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Instituciones │    │      Sedes      │    │      Aulas      │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ id              │    │ id              │    │ id              │
│ nombre          │    │ nombre          │    │ nombre          │
│ nit             │    │ direccion       │    │ capacidad       │
│ direccion       │    │ telefono        │    │ ubicacion       │
│ telefono        │    │ email           │    │ sede_id         │
│ email           │    │ institucion_id  │    └─────────────────┘
│ sitio_web       │    └─────────────────┘
│ rector          │
│ escudo          │
└─────────────────┘
        │
        │ 1:N
        ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│      Años       │    │     Grados      │    │     Grupos      │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ id              │    │ id              │    │ id              │
│ nombre          │    │ nombre          │    │ nombre          │
│ fecha_inicio    │    │ descripcion     │    │ descripcion     │
│ fecha_fin       │    │ nivel           │    │ capacidad       │
│ estado          │    │ institucion_id  │    │ grado_id        │
│ institucion_id  │    └─────────────────┘    │ docente_id      │
└─────────────────┘           │               └─────────────────┘
        │                     │                       │
        │ 1:N                 │ 1:N                   │ 1:N
        ▼                     ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│    Períodos     │    │     Áreas       │    │   Estudiantes   │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ id              │    │ id              │    │ id              │
│ nombre          │    │ nombre          │    │ nombre          │
│ fecha_inicio    │    │ descripcion     │    │ apellido        │
│ fecha_fin       │    │ color           │    │ tipo_documento  │
│ anio_id         │    │ institucion_id  │    │ numero_documento│
└─────────────────┘    └─────────────────┘    │ fecha_nacimiento│
        │                       │              │ genero          │
        │ 1:N                   │ 1:N          │ direccion       │
        ▼                       ▼              │ telefono        │
┌─────────────────┐    ┌─────────────────┐    │ email           │
│      Notas      │    │   Asignaturas   │    │ grado_id        │
├─────────────────┤    ├─────────────────┤    │ grupo_id        │
│ id              │    │ id              │    │ acudiente_id    │
│ valor           │    │ nombre          │    └─────────────────┘
│ tipo            │    │ descripcion     │
│ estudiante_id   │    │ area_id         │
│ asignatura_id   │    │ grado_id        │
│ periodo_id      │    │ horas_semanales │
└─────────────────┘    └─────────────────┘
```

## 🧩 Modelos y Relaciones

### Modelo User

```php
// app/Models/User.php
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relaciones
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasPermission($permission)
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('name', $permission);
        })->exists();
    }
}
```

### Modelo Institucion

```php
// app/Models/Institucion.php
class Institucion extends Model
{
    use HasFactory, HasFileUploads;

    protected $fillable = [
        'nombre',
        'nit',
        'direccion',
        'telefono',
        'email',
        'sitio_web',
        'rector',
        'escudo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Configuración de archivos
    protected $fileFields = [
        'escudo' => [
            'disk' => 'public',
            'path' => 'instituciones/escudos',
            'maxSize' => 2048, // 2MB
            'allowedTypes' => ['jpg', 'jpeg', 'png', 'gif'],
        ],
    ];

    // Relaciones
    public function sedes()
    {
        return $this->hasMany(Sede::class);
    }

    public function anios()
    {
        return $this->hasMany(Anio::class);
    }

    public function grados()
    {
        return $this->hasMany(Grado::class);
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function aulas()
    {
        return $this->hasManyThrough(Aula::class, Sede::class);
    }

    public function franjasHorarias()
    {
        return $this->hasMany(FranjaHoraria::class);
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    // Accessors
    public function getEscudoUrlAttribute()
    {
        return $this->escudo ? Storage::disk('public')->url($this->escudo) : null;
    }

    // Mutators
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = ucwords(strtolower($value));
    }
}
```

### Modelo Estudiante

```php
// app/Models/Estudiante.php
class Estudiante extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'tipo_documento',
        'numero_documento',
        'fecha_nacimiento',
        'genero',
        'direccion',
        'telefono',
        'email',
        'grado_id',
        'grupo_id',
        'acudiente_id',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function acudiente()
    {
        return $this->belongsTo(Acudiente::class);
    }

    public function notas()
    {
        return $this->hasMany(Nota::class);
    }

    public function inasistencias()
    {
        return $this->hasMany(Inasistencia::class);
    }

    // Scopes
    public function scopePorGrado($query, $gradoId)
    {
        return $query->where('grado_id', $gradoId);
    }

    public function scopePorGrupo($query, $grupoId)
    {
        return $query->where('grupo_id', $grupoId);
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }

    public function getEdadAttribute()
    {
        return $this->fecha_nacimiento ? $this->fecha_nacimiento->age : null;
    }

    // Mutators
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = ucwords(strtolower($value));
    }

    public function setApellidoAttribute($value)
    {
        $this->attributes['apellido'] = ucwords(strtolower($value));
    }
}
```

### Modelo Docente

```php
// app/Models/Docente.php
class Docente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'tipo_documento',
        'numero_documento',
        'fecha_nacimiento',
        'genero',
        'direccion',
        'telefono',
        'email',
        'especialidad',
        'titulo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }

    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class);
    }

    public function asignaturas()
    {
        return $this->belongsToMany(Asignatura::class, 'asignaciones');
    }

    // Scopes
    public function scopePorEspecialidad($query, $especialidad)
    {
        return $query->where('especialidad', $especialidad);
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }
}
```

## 📊 Migraciones

### Migración de Usuarios

```php
// database/migrations/2025_06_09_010957_create_users_table.php
class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
```

### Migración de Instituciones

```php
// database/migrations/2025_06_09_010934_create_instituciones_table.php
class CreateInstitucionesTable extends Migration
{
    public function up()
    {
        Schema::create('instituciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('nit')->unique();
            $table->text('direccion');
            $table->string('telefono');
            $table->string('email')->unique();
            $table->string('sitio_web')->nullable();
            $table->string('rector');
            $table->string('escudo')->nullable();
            $table->enum('estado', ['activa', 'inactiva'])->default('activa');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('instituciones');
    }
}
```

### Migración de Estudiantes

```php
// database/migrations/2025_06_09_011219_create_estudiantes_table.php
class CreateEstudiantesTable extends Migration
{
    public function up()
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->enum('tipo_documento', ['CC', 'TI', 'CE', 'PA']);
            $table->string('numero_documento')->unique();
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['M', 'F']);
            $table->text('direccion');
            $table->string('telefono');
            $table->string('email')->nullable();
            $table->foreignId('grado_id')->constrained()->onDelete('cascade');
            $table->foreignId('grupo_id')->constrained()->onDelete('cascade');
            $table->foreignId('acudiente_id')->constrained()->onDelete('cascade');
            $table->enum('estado', ['activo', 'inactivo', 'retirado'])->default('activo');
            $table->timestamps();

            $table->index(['grado_id', 'grupo_id']);
            $table->index('estado');
        });
    }

    public function down()
    {
        Schema::dropIfExists('estudiantes');
    }
}
```

## 🌱 Seeders

### DatabaseSeeder Principal

```php
// database/seeders/DatabaseSeeder.php
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Crear roles y permisos
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        // Crear usuario admin
        $this->call(UserSeeder::class);

        // Crear datos de prueba
        $this->call([
            InstitucionSeeder::class,
            SedeSeeder::class,
            AnioSeeder::class,
            GradoSeeder::class,
            AreaSeeder::class,
            AsignaturaSeeder::class,
            DocenteSeeder::class,
            AcudienteSeeder::class,
            GrupoSeeder::class,
            EstudianteSeeder::class,
        ]);
    }
}
```

### InstitucionSeeder

```php
// database/seeders/InstitucionSeeder.php
class InstitucionSeeder extends Seeder
{
    public function run()
    {
        Institucion::create([
            'nombre' => 'Colegio San José',
            'nit' => '900123456-7',
            'direccion' => 'Calle 15 #23-45, Bogotá',
            'telefono' => '6012345678',
            'email' => 'info@colegiosanjose.edu.co',
            'sitio_web' => 'https://colegiosanjose.edu.co',
            'rector' => 'Dr. Carlos Mendoza',
            'estado' => 'activa',
        ]);

        Institucion::create([
            'nombre' => 'Instituto Técnico Nacional',
            'nit' => '800987654-3',
            'direccion' => 'Carrera 78 #90-12, Medellín',
            'telefono' => '6045678901',
            'email' => 'contacto@itn.edu.co',
            'sitio_web' => 'https://itn.edu.co',
            'rector' => 'Ing. María González',
            'estado' => 'activa',
        ]);
    }
}
```

### EstudianteSeeder

```php
// database/seeders/EstudianteSeeder.php
class EstudianteSeeder extends Seeder
{
    public function run()
    {
        $grados = Grado::all();
        $grupos = Grupo::all();
        $acudientes = Acudiente::all();

        foreach ($grados as $grado) {
            foreach ($grupos->where('grado_id', $grado->id) as $grupo) {
                Estudiante::factory()
                    ->count(rand(20, 30))
                    ->create([
                        'grado_id' => $grado->id,
                        'grupo_id' => $grupo->id,
                        'acudiente_id' => $acudientes->random()->id,
                    ]);
            }
        }
    }
}
```

## 🏭 Factories

### EstudianteFactory

```php
// database/factories/EstudianteFactory.php
class EstudianteFactory extends Factory
{
    protected $model = Estudiante::class;

    public function definition()
    {
        $genero = $this->faker->randomElement(['M', 'F']);
        $tipoDocumento = $this->faker->randomElement(['CC', 'TI', 'CE']);

        return [
            'nombre' => $genero === 'M' ? $this->faker->firstNameMale() : $this->faker->firstNameFemale(),
            'apellido' => $this->faker->lastName(),
            'tipo_documento' => $tipoDocumento,
            'numero_documento' => $this->faker->unique()->numerify('##########'),
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-18 years', '-5 years'),
            'genero' => $genero,
            'direccion' => $this->faker->address(),
            'telefono' => $this->faker->numerify('3##########'),
            'email' => $this->faker->optional()->safeEmail(),
            'estado' => $this->faker->randomElement(['activo', 'activo', 'activo', 'inactivo']),
        ];
    }

    public function activo()
    {
        return $this->state(function (array $attributes) {
            return [
                'estado' => 'activo',
            ];
        });
    }

    public function inactivo()
    {
        return $this->state(function (array $attributes) {
            return [
                'estado' => 'inactivo',
            ];
        });
    }
}
```

### DocenteFactory

```php
// database/factories/DocenteFactory.php
class DocenteFactory extends Factory
{
    protected $model = Docente::class;

    public function definition()
    {
        $genero = $this->faker->randomElement(['M', 'F']);
        $especialidades = [
            'Matemáticas', 'Lenguaje', 'Ciencias Naturales', 'Ciencias Sociales',
            'Inglés', 'Educación Física', 'Arte', 'Tecnología', 'Filosofía'
        ];

        return [
            'nombre' => $genero === 'M' ? $this->faker->firstNameMale() : $this->faker->firstNameFemale(),
            'apellido' => $this->faker->lastName(),
            'tipo_documento' => 'CC',
            'numero_documento' => $this->faker->unique()->numerify('##########'),
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-60 years', '-25 years'),
            'genero' => $genero,
            'direccion' => $this->faker->address(),
            'telefono' => $this->faker->numerify('3##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'especialidad' => $this->faker->randomElement($especialidades),
            'titulo' => $this->faker->randomElement([
                'Licenciado en Educación',
                'Magíster en Educación',
                'Doctor en Educación',
                'Técnico en Educación'
            ]),
            'estado' => 'activo',
        ];
    }
}
```

## 🔍 Consultas Comunes

### Estudiantes por Grado y Grupo

```php
// Obtener estudiantes de un grado específico
$estudiantes = Estudiante::with(['grado', 'grupo', 'acudiente'])
    ->where('grado_id', $gradoId)
    ->where('estado', 'activo')
    ->orderBy('apellido')
    ->orderBy('nombre')
    ->get();

// Obtener estudiantes de un grupo específico
$estudiantes = Estudiante::with(['grado', 'grupo', 'acudiente'])
    ->where('grupo_id', $grupoId)
    ->where('estado', 'activo')
    ->orderBy('apellido')
    ->orderBy('nombre')
    ->get();
```

### Notas de Estudiantes

```php
// Obtener notas de un estudiante en un período
$notas = Nota::with(['asignatura', 'periodo'])
    ->where('estudiante_id', $estudianteId)
    ->where('periodo_id', $periodoId)
    ->get();

// Obtener promedio de un estudiante
$promedio = Nota::where('estudiante_id', $estudianteId)
    ->where('periodo_id', $periodoId)
    ->avg('valor');
```

### Asignaciones de Docentes

```php
// Obtener asignaciones de un docente
$asignaciones = Asignacion::with(['asignatura', 'grupo', 'aula', 'franjaHoraria'])
    ->where('docente_id', $docenteId)
    ->orderBy('dia_semana')
    ->orderBy('franja_horaria_id')
    ->get();

// Obtener horario de un docente
$horario = Asignacion::with(['asignatura', 'grupo', 'aula', 'franjaHoraria'])
    ->where('docente_id', $docenteId)
    ->where('dia_semana', $dia)
    ->orderBy('franja_horaria_id')
    ->get();
```

### Estadísticas de Instituciones

```php
// Obtener estadísticas de una institución
$estadisticas = [
    'total_estudiantes' => Estudiante::whereHas('grado.institucion', function ($query) use ($institucionId) {
        $query->where('id', $institucionId);
    })->where('estado', 'activo')->count(),
    
    'total_docentes' => Docente::where('estado', 'activo')->count(),
    
    'total_grados' => Grado::where('institucion_id', $institucionId)->count(),
    
    'total_grupos' => Grupo::whereHas('grado', function ($query) use ($institucionId) {
        $query->where('institucion_id', $institucionId);
    })->count(),
];
```

## ⚡ Optimización

### Índices Recomendados

```sql
-- Índices para estudiantes
CREATE INDEX idx_estudiantes_grado_grupo ON estudiantes(grado_id, grupo_id);
CREATE INDEX idx_estudiantes_estado ON estudiantes(estado);
CREATE INDEX idx_estudiantes_documento ON estudiantes(tipo_documento, numero_documento);

-- Índices para notas
CREATE INDEX idx_notas_estudiante_periodo ON notas(estudiante_id, periodo_id);
CREATE INDEX idx_notas_asignatura ON notas(asignatura_id);

-- Índices para asignaciones
CREATE INDEX idx_asignaciones_docente ON asignaciones(docente_id);
CREATE INDEX idx_asignaciones_grupo ON asignaciones(grupo_id);
CREATE INDEX idx_asignaciones_dia ON asignaciones(dia_semana, franja_horaria_id);
```

### Consultas Optimizadas

```php
// Usar eager loading para evitar N+1 queries
$estudiantes = Estudiante::with([
    'grado.institucion',
    'grupo.docente',
    'acudiente',
    'notas.asignatura',
    'notas.periodo'
])->get();

// Usar select para obtener solo campos necesarios
$estudiantes = Estudiante::select('id', 'nombre', 'apellido', 'grado_id', 'grupo_id')
    ->with(['grado:id,nombre', 'grupo:id,nombre'])
    ->get();

// Usar chunk para procesar grandes volúmenes de datos
Estudiante::chunk(1000, function ($estudiantes) {
    foreach ($estudiantes as $estudiante) {
        // Procesar estudiante
    }
});
```

### Caché de Consultas

```php
// Cachear consultas frecuentes
$estudiantes = Cache::remember("estudiantes_grado_{$gradoId}", 3600, function () use ($gradoId) {
    return Estudiante::with(['grado', 'grupo'])
        ->where('grado_id', $gradoId)
        ->where('estado', 'activo')
        ->get();
});

// Cachear estadísticas
$estadisticas = Cache::remember("estadisticas_institucion_{$institucionId}", 1800, function () use ($institucionId) {
    return [
        'total_estudiantes' => Estudiante::whereHas('grado.institucion', function ($query) use ($institucionId) {
            $query->where('id', $institucionId);
        })->where('estado', 'activo')->count(),
        // ... más estadísticas
    ];
});
```

---

**¡Base de datos optimizada y lista para producción! 🗄️** 