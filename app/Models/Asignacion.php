<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Clase Asignacion
 *
 * Representa la asignación de un docente a una asignatura y un grupo en un año académico específico.
 *
 * @property int $id
 * @property int $docente_id
 * @property int $asignatura_id
 * @property int $grupo_id
 * @property int $anio_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Docente $docente
 * @property-read \App\Models\Asignatura $asignatura
 * @property-read \App\Models\Grupo $grupo
 * @property-read \App\Models\Anio $anio
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Horario> $horarios
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Nota> $notas
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inasistencia> $inasistencias
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Actividad> $actividades
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DefinitivaAsignatura> $definitivasAsignatura
 * @method static \Database\Factories\AsignacionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Asignacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Asignacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Asignacion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Asignacion query()
 * @method static \Illuminate\Database\Eloquent\Builder|Asignacion withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Asignacion withoutTrashed()
 * @mixin \Eloquent
 */
class Asignacion extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'asignaciones';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'docente_id',
        'asignatura_id',
        'grupo_id',
        'anio_id',
    ];

    /**
     * Obtiene el docente asociado a la asignación.
     */
    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }

    /**
     * Obtiene la asignatura asociada a la asignación.
     */
    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class);
    }

    /**
     * Obtiene el grupo asociado a la asignación.
     */
    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    /**
     * Obtiene el año académico asociado a la asignación.
     */
    public function anio()
    {
        return $this->belongsTo(Anio::class);
    }

    /**
     * Obtiene los horarios asociados a esta asignación.
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    /**
     * Obtiene las notas asociadas a esta asignación.
     */
    public function notas()
    {
        return $this->hasMany(Nota::class);
    }

    /**
     * Obtiene las inasistencias asociadas a esta asignación.
     */
    public function inasistencias()
    {
        return $this->hasMany(Inasistencia::class);
    }

    /**
     * Obtiene las actividades asociadas a esta asignación.
     */
    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }

    /**
     * Obtiene las definitivas de asignatura asociadas a esta asignación.
     */
    public function definitivasAsignatura()
    {
        return $this->hasMany(DefinitivaAsignatura::class);
    }
}
