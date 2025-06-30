<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Clase Area
 *
 * Representa un área académica en el sistema.
 *
 * @property int $id
 * @property string $nombre
 * @property int $institucion_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Institucion $institucion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Competencia> $competencias
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asignatura> $asignaturas
 * @method static \Database\Factories\AreaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Area newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Area newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Area onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Area query()
 * @method static \Illuminate\Database\Eloquent\Builder|Area withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Area withoutTrashed()
 * @mixin \Eloquent
 */
class Area extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'areas';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'institucion_id',
    ];

    /**
     * Obtiene la institución a la que pertenece el área.
     */
    public function institucion()
    {
        return $this->belongsTo(Institucion::class);
    }

    /**
     * Obtiene las competencias asociadas a esta área.
     */
    public function competencias()
    {
        return $this->hasMany(Competencia::class);
    }

    /**
     * Obtiene las asignaturas asociadas a esta área.
     */
    public function asignaturas()
    {
        return $this->hasMany(Asignatura::class);
    }
}
