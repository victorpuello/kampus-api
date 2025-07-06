<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Clase Grupo
 *
 * Representa un grupo académico en el sistema.
 *
 * @property int $id
 * @property string $nombre
 * @property int $sede_id
 * @property int $grado_id
 * @property int $anio_id
 * @property int|null $director_docente_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Sede $sede
 * @property-read \App\Models\Grado $grado
 * @property-read \App\Models\Anio $anio
 * @property-read \App\Models\Docente|null $directorDocente
 * @property-read \App\Models\Institucion $institucion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Estudiante> $estudiantes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asignacion> $asignaciones
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Horario> $horarios
 *
 * @method static \Database\Factories\GrupoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo query()
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Grupo extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'grupos';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'sede_id',
        'grado_id',
        'anio_id',
        'director_docente_id',
    ];

    /**
     * Boot del modelo para validaciones
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($grupo) {
            // Validar que el grado pertenezca a la misma institución de la sede
            if ($grupo->sede && $grupo->grado) {
                if ($grupo->sede->institucion_id !== $grupo->grado->institucion_id) {
                    throw new \Exception('El grado debe pertenecer a la misma institución de la sede');
                }
            }

            // Validar que el docente no sea director de otro grupo
            if ($grupo->director_docente_id) {
                $docenteYaEsDirector = static::where('director_docente_id', $grupo->director_docente_id)->exists();
                if ($docenteYaEsDirector) {
                    throw new \Exception('El docente seleccionado ya es director de otro grupo');
                }
            }
        });

        static::updating(function ($grupo) {
            // Validar que el grado pertenezca a la misma institución de la sede
            if ($grupo->sede && $grupo->grado) {
                if ($grupo->sede->institucion_id !== $grupo->grado->institucion_id) {
                    throw new \Exception('El grado debe pertenecer a la misma institución de la sede');
                }
            }

            // Validar que el docente no sea director de otro grupo (excluyendo el grupo actual)
            if ($grupo->director_docente_id) {
                $docenteYaEsDirector = static::where('director_docente_id', $grupo->director_docente_id)
                    ->where('id', '!=', $grupo->id)
                    ->exists();
                if ($docenteYaEsDirector) {
                    throw new \Exception('El docente seleccionado ya es director de otro grupo');
                }
            }
        });
    }

    /**
     * Obtiene la sede a la que pertenece el grupo.
     */
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    /**
     * Obtiene el grado al que pertenece el grupo.
     */
    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }

    /**
     * Obtiene la institución a través de la sede.
     */
    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'sede_id', 'id')
            ->join('sedes', 'sedes.institucion_id', '=', 'instituciones.id')
            ->where('sedes.id', $this->sede_id);
    }

    /**
     * Obtiene la institución de forma más directa a través de la sede.
     */
    public function getInstitucionAttribute()
    {
        return $this->sede->institucion;
    }

    /**
     * Obtiene el docente director de grupo.
     */
    public function directorDocente()
    {
        return $this->belongsTo(Docente::class, 'director_docente_id');
    }

    /**
     * Obtiene el año académico al que pertenece el grupo.
     */
    public function anio()
    {
        return $this->belongsTo(Anio::class);
    }

    /**
     * Obtiene los estudiantes asociados a este grupo.
     */
    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class);
    }

    /**
     * Obtiene las asignaciones asociadas a este grupo.
     */
    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class);
    }

    /**
     * Obtiene los horarios asociados a este grupo.
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    /**
     * Obtiene el nombre completo del grupo (Sede - Grado - Nombre).
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->sede->nombre} - {$this->grado->nombre} - {$this->nombre}";
    }

    /**
     * Scope para filtrar grupos por sede.
     */
    public function scopePorSede($query, $sedeId)
    {
        return $query->where('sede_id', $sedeId);
    }

    /**
     * Scope para filtrar grupos por grado.
     */
    public function scopePorGrado($query, $gradoId)
    {
        return $query->where('grado_id', $gradoId);
    }

    /**
     * Scope para filtrar grupos por año académico.
     */
    public function scopePorAnio($query, $anioId)
    {
        return $query->where('anio_id', $anioId);
    }

    /**
     * Scope para filtrar grupos por institución.
     */
    public function scopePorInstitucion($query, $institucionId)
    {
        return $query->whereHas('sede', function ($q) use ($institucionId) {
            $q->where('institucion_id', $institucionId);
        });
    }
}
