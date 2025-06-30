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
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
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
     * Obtiene los acudientes asociados al estudiante.
     */
    public function acudientes()
    {
        return $this->belongsToMany(Acudiente::class, 'estudiante_acudiente');
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

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }
}