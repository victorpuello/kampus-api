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
 * @property int $grado_id
 * @property int $anio_id
 * @property int|null $director_docente_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Docente|null $directorDocente
 * @property-read \App\Models\Grado $grado
 * @property-read \App\Models\Anio $anio
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Estudiante> $estudiantes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asignacion> $asignaciones
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Horario> $horarios
 * @method static \Database\Factories\GrupoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo query()
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Grupo withoutTrashed()
 * @mixin \Eloquent
 */
class Grupo extends Model
{
    use HasFactory, SoftDeletes;

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
        'grado_id',
        'anio_id',
        'director_docente_id',
    ];

    /**
     * Obtiene el docente director de grupo.
     */
    public function directorDocente()
    {
        return $this->belongsTo(Docente::class, 'director_docente_id');
    }

    /**
     * Obtiene el grado al que pertenece el grupo.
     */
    public function grado()
    {
        return $this->belongsTo(Grado::class);
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
}
