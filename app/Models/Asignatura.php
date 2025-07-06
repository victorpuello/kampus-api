<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Clase Asignatura
 *
 * Representa una asignatura académica en el sistema.
 *
 * @property int $id
 * @property string $nombre
 * @property float $porcentaje_area
 * @property int $area_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Area $area
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asignatura> $prerequisitos
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asignacion> $asignaciones
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DefinitivaAsignatura> $definitivasAsignatura
 *
 * @method static \Database\Factories\AsignaturaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Asignatura newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Asignatura newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Asignatura onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Asignatura query()
 * @method static \Illuminate\Database\Eloquent\Builder|Asignatura withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Asignatura withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Asignatura extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'asignaturas';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'porcentaje_area',
        'area_id',
    ];

    /**
     * Obtiene el área a la que pertenece la asignatura.
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Obtiene las asignaturas que son prerequisitos de esta asignatura.
     */
    public function prerequisitos()
    {
        return $this->belongsToMany(Asignatura::class, 'asignatura_prerequisitos', 'asignatura_id', 'prerequisito_id');
    }

    /**
     * Obtiene las asignaciones asociadas a esta asignatura.
     */
    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class);
    }

    /**
     * Obtiene las definitivas de asignatura asociadas a esta asignatura.
     */
    public function definitivasAsignatura()
    {
        return $this->hasMany(DefinitivaAsignatura::class);
    }

    /**
     * Obtiene los grados donde se imparte esta asignatura a través de las asignaciones.
     */
    public function grados()
    {
        return $this->belongsToMany(Grado::class, 'asignaciones', 'asignatura_id', 'grupo_id')
            ->join('grupos', 'grupos.id', '=', 'asignaciones.grupo_id')
            ->select('grados.*')
            ->distinct();
    }
}
