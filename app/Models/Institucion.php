<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Clase Institucion
 *
 * Representa una institución educativa en el sistema.
 *
 * @property int $id
 * @property string $nombre
 * @property string $siglas
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Anio> $anios
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Area> $areas
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grado> $grados
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Aula> $aulas
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FranjaHoraria> $franjasHorarias
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Configuracion> $configuraciones
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comunicado> $comunicados
 * @method static \Database\Factories\InstitucionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Institucion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Institucion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Institucion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Institucion query()
 * @method static \Illuminate\Database\Eloquent\Builder|Institucion withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Institucion withoutTrashed()
 * @mixin \Eloquent
 */
class Institucion extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'instituciones';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'siglas',
    ];

    /**
     * Obtiene los usuarios asociados a esta institución.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Obtiene los años académicos asociados a esta institución.
     */
    public function anios()
    {
        return $this->hasMany(Anio::class);
    }

    /**
     * Obtiene las áreas académicas asociadas a esta institución.
     */
    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    /**
     * Obtiene los grados académicos asociados a esta institución.
     */
    public function grados()
    {
        return $this->hasMany(Grado::class);
    }

    /**
     * Obtiene las aulas asociadas a esta institución.
     */
    public function aulas()
    {
        return $this->hasMany(Aula::class);
    }

    /**
     * Obtiene las franjas horarias asociadas a esta institución.
     */
    public function franjasHorarias()
    {
        return $this->hasMany(FranjaHoraria::class);
    }

    /**
     * Obtiene las configuraciones asociadas a esta institución.
     */
    public function configuraciones()
    {
        return $this->hasMany(Configuracion::class);
    }

    /**
     * Obtiene los comunicados asociados a esta institución.
     */
    public function comunicados()
    {
        return $this->hasMany(Comunicado::class);
    }
}
