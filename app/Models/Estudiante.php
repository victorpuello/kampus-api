<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Clase Estudiante
 *
 * Representa un estudiante en el sistema.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $codigo_estudiantil
 * @property int|null $grupo_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\Grupo|null $grupo
 * @property-read \App\Models\Grado|null $grado
 * @property-read \App\Models\Sede|null $sede
 * @property-read \App\Models\Institucion|null $institucion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Acudiente> $acudientes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HistorialGrupo> $historialGrupos
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Observador> $observador
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Nota> $notas
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inasistencia> $inasistencias
 * @method static \Database\Factories\EstudianteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Estudiante newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Estudiante newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Estudiante onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Estudiante query()
 * @method static \Illuminate\Database\Eloquent\Builder|Estudiante withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Estudiante withoutTrashed()
 * @mixin \Eloquent
 */
class Estudiante extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'estudiantes';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'codigo_estudiantil',
        'grupo_id',
        'fecha_nacimiento',
        'genero',
        'direccion',
        'telefono',
        'estado',
        'acudiente_id',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    /**
     * Obtiene el usuario asociado al estudiante.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el grupo al que pertenece el estudiante.
     */
    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    /**
     * Obtiene el grado de forma más directa a través del grupo.
     */
    public function getGradoAttribute()
    {
        return $this->grupo ? $this->grupo->grado : null;
    }

    /**
     * Obtiene la sede de forma más directa a través del grupo.
     */
    public function getSedeAttribute()
    {
        return $this->grupo ? $this->grupo->sede : null;
    }

    /**
     * Obtiene la institución de forma más directa a través del grupo.
     */
    public function getInstitucionAttribute()
    {
        return $this->grupo ? $this->grupo->institucion : null;
    }

    /**
     * Obtiene los acudientes asociados al estudiante.
     */
    public function acudientes()
    {
        return $this->belongsToMany(Acudiente::class, 'estudiante_acudiente');
    }

    /**
     * Obtiene el primer acudiente asociado al estudiante.
     */
    public function acudiente()
    {
        return $this->belongsToMany(Acudiente::class, 'estudiante_acudiente')->limit(1);
    }

    /**
     * Obtiene el historial de grupos del estudiante.
     */
    public function historialGrupos()
    {
        return $this->hasMany(HistorialGrupo::class);
    }

    /**
     * Obtiene los registros del observador del estudiante.
     */
    public function observador()
    {
        return $this->hasMany(Observador::class);
    }

    /**
     * Obtiene las notas del estudiante.
     */
    public function notas()
    {
        return $this->hasMany(Nota::class);
    }

    /**
     * Obtiene las inasistencias del estudiante.
     */
    public function inasistencias()
    {
        return $this->hasMany(Inasistencia::class);
    }

    /**
     * Obtiene el nombre completo del estudiante con su ubicación académica.
     */
    public function getUbicacionAcademicaAttribute()
    {
        if (!$this->grupo) {
            return "Sin asignar";
        }
        
        return "{$this->grupo->sede->nombre} - {$this->grupo->grado->nombre} - {$this->grupo->nombre}";
    }

    /**
     * Scope para filtrar estudiantes por grupo.
     */
    public function scopePorGrupo($query, $grupoId)
    {
        return $query->where('grupo_id', $grupoId);
    }

    /**
     * Scope para filtrar estudiantes por grado.
     */
    public function scopePorGrado($query, $gradoId)
    {
        return $query->whereHas('grupo', function ($q) use ($gradoId) {
            $q->where('grado_id', $gradoId);
        });
    }

    /**
     * Scope para filtrar estudiantes por sede.
     */
    public function scopePorSede($query, $sedeId)
    {
        return $query->whereHas('grupo', function ($q) use ($sedeId) {
            $q->where('sede_id', $sedeId);
        });
    }

    /**
     * Scope para filtrar estudiantes por institución.
     */
    public function scopePorInstitucion($query, $institucionId)
    {
        return $query->whereHas('grupo.sede', function ($q) use ($institucionId) {
            $q->where('institucion_id', $institucionId);
        });
    }

    /**
     * Scope para filtrar estudiantes por año académico.
     */
    public function scopePorAnio($query, $anioId)
    {
        return $query->whereHas('grupo', function ($q) use ($anioId) {
            $q->where('anio_id', $anioId);
        });
    }
}