<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Clase Grado
 *
 * Representa un grado académico en el sistema.
 *
 * @property int $id
 * @property string $nombre
 * @property int $nivel
 * @property int $institucion_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Institucion $institucion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grupo> $grupos
 * @method static \Database\Factories\GradoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Grado newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grado newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grado onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Grado query()
 * @method static \Illuminate\Database\Eloquent\Builder|Grado withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Grado withoutTrashed()
 * @mixin \Eloquent
 */
class Grado extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'grados';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'nivel',
        'institucion_id',
    ];

    /**
     * Obtiene la institución a la que pertenece el grado.
     */
    public function institucion()
    {
        return $this->belongsTo(Institucion::class);
    }

    /**
     * Obtiene los grupos asociados a este grado.
     */
    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }
}
