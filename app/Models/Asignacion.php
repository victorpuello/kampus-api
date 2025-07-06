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
        'franja_horaria_id',
        'dia_semana',
        'anio_academico_id',
        'periodo_id',
        'estado',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'estado' => 'string',
        'dia_semana' => 'string',
    ];

    /**
     * Los valores permitidos para el campo dia_semana.
     *
     * @var array<string>
     */
    public const DIAS_SEMANA = [
        'lunes',
        'martes', 
        'miercoles',
        'jueves',
        'viernes',
        'sabado'
    ];

    /**
     * Los valores permitidos para el campo estado.
     *
     * @var array<string>
     */
    public const ESTADOS = [
        'activo',
        'inactivo'
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
     * Obtiene la franja horaria asociada a la asignación.
     */
    public function franjaHoraria()
    {
        return $this->belongsTo(FranjaHoraria::class);
    }

    /**
     * Obtiene el año académico asociado a la asignación.
     */
    public function anioAcademico()
    {
        return $this->belongsTo(Anio::class, 'anio_academico_id');
    }

    /**
     * Obtiene el período asociado a la asignación.
     */
    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
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

    /**
     * Scope para filtrar por estado activo.
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para filtrar por año académico.
     */
    public function scopePorAnio($query, $anioId)
    {
        return $query->where('anio_academico_id', $anioId);
    }

    /**
     * Scope para filtrar por grupo.
     */
    public function scopePorGrupo($query, $grupoId)
    {
        return $query->where('grupo_id', $grupoId);
    }

    /**
     * Scope para filtrar por docente.
     */
    public function scopePorDocente($query, $docenteId)
    {
        return $query->where('docente_id', $docenteId);
    }

    /**
     * Scope para filtrar por institución.
     */
    public function scopePorInstitucion($query, $institucionId)
    {
        return $query->whereHas('grupo.sede.institucion', function ($q) use ($institucionId) {
            $q->where('id', $institucionId);
        });
    }

    /**
     * Verifica si hay conflicto de horario para el docente.
     */
    public function tieneConflictoDocente()
    {
        return static::where('docente_id', $this->docente_id)
            ->where('dia_semana', $this->dia_semana)
            ->where('franja_horaria_id', $this->franja_horaria_id)
            ->where('anio_academico_id', $this->anio_academico_id)
            ->where('id', '!=', $this->id)
            ->where('estado', 'activo')
            ->exists();
    }

    /**
     * Verifica si hay conflicto de horario para el grupo.
     */
    public function tieneConflictoGrupo()
    {
        return static::where('grupo_id', $this->grupo_id)
            ->where('dia_semana', $this->dia_semana)
            ->where('franja_horaria_id', $this->franja_horaria_id)
            ->where('anio_academico_id', $this->anio_academico_id)
            ->where('id', '!=', $this->id)
            ->where('estado', 'activo')
            ->exists();
    }

    /**
     * Obtiene el nombre completo del docente.
     */
    public function getNombreDocenteAttribute()
    {
        return $this->docente ? $this->docente->user->nombre . ' ' . $this->docente->user->apellido : 'Sin docente';
    }

    /**
     * Obtiene el nombre de la asignatura.
     */
    public function getNombreAsignaturaAttribute()
    {
        return $this->asignatura ? $this->asignatura->nombre : 'Sin asignatura';
    }

    /**
     * Obtiene el nombre del grupo.
     */
    public function getNombreGrupoAttribute()
    {
        return $this->grupo ? $this->grupo->nombre : 'Sin grupo';
    }
}
