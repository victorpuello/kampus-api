<?php

namespace App\Models;

use App\Traits\HasFileUploads;
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
 * @property string|null $slogan
 * @property string|null $dane
 * @property string|null $resolucion_aprobacion
 * @property string|null $direccion
 * @property string|null $telefono
 * @property string|null $email
 * @property string|null $rector
 * @property string|null $escudo
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sede> $sedes
 *
 * @method static \Database\Factories\InstitucionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Institucion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Institucion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Institucion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Institucion query()
 * @method static \Illuminate\Database\Eloquent\Builder|Institucion withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Institucion withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Institucion extends Model
{
    use HasFactory;
    use HasFileUploads;
    use SoftDeletes;

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
        'slogan',
        'dane',
        'resolucion_aprobacion',
        'direccion',
        'telefono',
        'email',
        'rector',
        'escudo',
    ];

    /**
     * Constructor del modelo
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->initializeHasFileUploads();
    }

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->initializeHasFileUploads();
            $model->setFileFields(['escudo']);
            $model->setFilePaths(['escudo' => 'instituciones/escudos']);
        });

        static::updating(function ($model) {
            $model->initializeHasFileUploads();
            $model->setFileFields(['escudo']);
            $model->setFilePaths(['escudo' => 'instituciones/escudos']);
        });

        static::retrieved(function ($model) {
            $model->initializeHasFileUploads();
            $model->setFileFields(['escudo']);
            $model->setFilePaths(['escudo' => 'instituciones/escudos']);
        });
    }

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

    /**
     * Obtiene las sedes asociadas a esta institución.
     */
    public function sedes()
    {
        return $this->hasMany(Sede::class);
    }

    /**
     * Obtiene los grupos asociados a esta institución a través de sus grados.
     */
    public function grupos()
    {
        return $this->hasManyThrough(Grupo::class, Grado::class);
    }

    /**
     * Obtiene los estudiantes asociados a esta institución a través de los grupos y grados.
     */
    public function estudiantes()
    {
        return $this->hasManyThrough(Estudiante::class, Grupo::class, 'grado_id', 'grupo_id', 'id', 'id');
    }

    /**
     * Obtiene los grupos de esta institución para un año académico específico.
     *
     * @param  int  $anioId
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function gruposPorAnio($anioId)
    {
        return $this->grupos()->where('anio_id', $anioId);
    }

    /**
     * Obtiene los grados con sus grupos para un año académico específico.
     *
     * @param  int|null  $anioId
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gradosConGrupos($anioId = null)
    {
        $query = $this->grados()->with('grupos');
        if ($anioId) {
            $query->whereHas('grupos', function ($q) use ($anioId) {
                $q->where('anio_id', $anioId);
            });
        }

        return $query;
    }

    /**
     * Obtiene los grados con grupos activos para un año académico específico.
     *
     * @param  int  $anioId
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gradosConGruposActivos($anioId)
    {
        return $this->grados()->whereHas('grupos', function ($q) use ($anioId) {
            $q->where('anio_id', $anioId);
        })->with(['grupos' => function ($q) use ($anioId) {
            $q->where('anio_id', $anioId);
        }]);
    }

    /**
     * Obtiene estadísticas de grupos por nivel educativo para un año específico.
     *
     * @param  int  $anioId
     * @return array
     */
    public function estadisticasGruposPorNivel($anioId)
    {
        $estadisticas = [];
        $niveles = Grado::getNivelesDisponibles();

        foreach ($niveles as $nivel) {
            $count = $this->grupos()
                ->whereHas('grado', function ($q) use ($nivel) {
                    $q->where('nivel', $nivel);
                })
                ->where('anio_id', $anioId)
                ->count();

            $estadisticas[$nivel] = $count;
        }

        return $estadisticas;
    }

    /**
     * Mutador para el campo escudo - asigna imagen por defecto si está vacío
     */
    public function setEscudoAttribute($value)
    {
        // Si el valor está vacío o es null, asignar imagen por defecto
        if (empty($value)) {
            $this->attributes['escudo'] = 'instituciones/escudos/default.png';
        } else {
            $this->attributes['escudo'] = $value;
        }
    }

    /**
     * Accesor para el campo escudo - siempre retorna una URL válida
     */
    public function getEscudoAttribute($value)
    {
        // Si no hay valor o el archivo no existe, retornar imagen por defecto
        if (empty($value) || ! \Storage::disk('public')->exists($value)) {
            return 'instituciones/escudos/default.png';
        }

        return $value;
    }

    /**
     * Obtiene el nombre de la clave de ruta para el modelo.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id';
    }
}
