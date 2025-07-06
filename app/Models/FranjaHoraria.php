<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Clase FranjaHoraria
 *
 * Representa una franja horaria en el sistema.
 *
 * @property int $id
 * @property int $institucion_id
 * @property string $hora_inicio
 * @property string $hora_fin
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Institucion $institucion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Horario> $horarios
 *
 * @method static \Database\Factories\FranjaHorariaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|FranjaHoraria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FranjaHoraria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FranjaHoraria onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FranjaHoraria query()
 * @method static \Illuminate\Database\Eloquent\Builder|FranjaHoraria withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FranjaHoraria withoutTrashed()
 *
 * @mixin \Eloquent
 */
class FranjaHoraria extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'franjas_horarias';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'institucion_id',
        'nombre',
        'descripcion',
        'hora_inicio',
        'hora_fin',
        'duracion_minutos',
        'estado',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hora_inicio' => 'string',
        'hora_fin' => 'string',
        'duracion_minutos' => 'integer',
    ];

    /**
     * Obtiene la institución a la que pertenece la franja horaria.
     */
    public function institucion()
    {
        return $this->belongsTo(Institucion::class);
    }

    /**
     * Obtiene los horarios asociados a esta franja horaria.
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class, 'franja_id');
    }
}
