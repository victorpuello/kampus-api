<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Clase Docente
 *
 * Representa un docente en el sistema.
 *
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asignacion> $asignaciones
 * @method static \Database\Factories\DocenteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Docente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Docente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Docente onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Docente query()
 * @method static \Illuminate\Database\Eloquent\Builder|Docente withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Docente withoutTrashed()
 * @mixin \Eloquent
 */
class Docente extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'docentes';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
    ];

    /**
     * Obtiene el usuario asociado al docente.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene las asignaciones del docente.
     */
    public function asignaciones()
    {
        return $this->hasMany(\App\Models\Asignacion::class);
    }
}