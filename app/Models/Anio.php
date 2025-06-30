<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Clase Anio
 *
 * Representa un año académico en el sistema.
 *
 * @property int $id
 * @property string $nombre
 * @property string $fecha_inicio
 * @property string $fecha_fin
 * @property int $institucion_id
 * @property string $estado
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Institucion $institucion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Periodo> $periodos
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grupo> $grupos
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asignacion> $asignaciones
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Horario> $horarios
 * @property-read \App\Models\CriterioPromocion|null $criteriosPromocion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DefinitivaFinal> $definitivasFinales
 * @method static \Database\Factories\AnioFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Anio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Anio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Anio onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Anio query()
 * @method static \Illuminate\Database\Eloquent\Builder|Anio withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Anio withoutTrashed()
 * @mixin \Eloquent
 */
class Anio extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'anios';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'institucion_id',
        'estado',
    ];

    /**
     * Obtiene la institución a la que pertenece el año.
     */
    public function institucion()
    {
        return $this->belongsTo(Institucion::class);
    }

    /**
     * Obtiene los periodos asociados a este año.
     */
    public function periodos()
    {
        return $this->hasMany(Periodo::class);
    }

    /**
     * Obtiene los grupos asociados a este año.
     */
    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }

    /**
     * Obtiene las asignaciones asociadas a este año.
     */
    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class);
    }

    /**
     * Obtiene los horarios asociados a este año.
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    /**
     * Obtiene los criterios de promoción asociados a este año.
     */
    public function criteriosPromocion()
    {
        return $this->hasOne(CriterioPromocion::class);
    }

    /**
     * Obtiene las definitivas finales asociadas a este año.
     */
    public function definitivasFinales()
    {
        return $this->hasMany(DefinitivaFinal::class);
    }
}
