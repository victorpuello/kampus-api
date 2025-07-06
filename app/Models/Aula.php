<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Clase Aula
 *
 * Representa un aula o salón de clases en el sistema.
 *
 * @property int $id
 * @property string $nombre
 * @property int $capacidad
 * @property int $institucion_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Institucion $institucion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Horario> $horarios
 *
 * @method static \Database\Factories\AulaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Aula newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Aula newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Aula onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Aula query()
 * @method static \Illuminate\Database\Eloquent\Builder|Aula withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Aula withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Aula extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'aulas';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'tipo',
        'capacidad',
        'institucion_id',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'capacidad' => 'integer',
    ];

    /**
     * Obtiene la institución a la que pertenece el aula.
     */
    public function institucion()
    {
        return $this->belongsTo(Institucion::class);
    }

    /**
     * Obtiene los horarios asociados a esta aula.
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}
