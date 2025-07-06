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
 * @property string $nivel
 * @property int $institucion_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Institucion $institucion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grupo> $grupos
 *
 * @method static \Database\Factories\GradoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Grado newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grado newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grado onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Grado query()
 * @method static \Illuminate\Database\Eloquent\Builder|Grado withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Grado withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Grado extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Constantes para los niveles educativos
     */
    const NIVEL_PREESCOLAR = 'Preescolar';

    const NIVEL_BASICA_PRIMARIA = 'Básica Primaria';

    const NIVEL_BASICA_SECUNDARIA = 'Básica Secundaria';

    const NIVEL_EDUCACION_MEDIA = 'Educación Media';

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
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'nivel' => 'string',
    ];

    /**
     * Obtiene todos los niveles disponibles.
     *
     * @return array<string>
     */
    public static function getNivelesDisponibles(): array
    {
        return [
            self::NIVEL_PREESCOLAR,
            self::NIVEL_BASICA_PRIMARIA,
            self::NIVEL_BASICA_SECUNDARIA,
            self::NIVEL_EDUCACION_MEDIA,
        ];
    }

    /**
     * Verifica si el nivel es válido.
     */
    public static function isNivelValido(?string $nivel): bool
    {
        if ($nivel === null) {
            return false;
        }

        return in_array($nivel, self::getNivelesDisponibles());
    }

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

    /**
     * Obtiene los estudiantes asociados a este grado a través de los grupos.
     */
    public function estudiantes()
    {
        return $this->hasManyThrough(Estudiante::class, Grupo::class);
    }

    /**
     * Obtiene los grupos de este grado para un año académico específico.
     *
     * @param  int  $anioId
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gruposPorAnio($anioId)
    {
        return $this->grupos()->where('anio_id', $anioId);
    }

    /**
     * Obtiene los grupos activos de este grado para un año académico específico.
     *
     * @param  int  $anioId
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gruposActivosPorAnio($anioId)
    {
        return $this->grupos()->where('anio_id', $anioId);
    }

    /**
     * Obtiene el total de estudiantes en este grado para un año específico.
     *
     * @param  int  $anioId
     * @return int
     */
    public function totalEstudiantesPorAnio($anioId)
    {
        return $this->grupos()
            ->where('anio_id', $anioId)
            ->withCount('estudiantes')
            ->get()
            ->sum('estudiantes_count');
    }

    /**
     * Obtiene estadísticas del grado para un año académico específico.
     *
     * @param  int  $anioId
     * @return array
     */
    public function estadisticasPorAnio($anioId)
    {
        $grupos = $this->gruposPorAnio($anioId)->withCount('estudiantes')->get();

        return [
            'total_grupos' => $grupos->count(),
            'total_estudiantes' => $grupos->sum('estudiantes_count'),
            'promedio_estudiantes_por_grupo' => $grupos->count() > 0 ? round($grupos->sum('estudiantes_count') / $grupos->count(), 2) : 0,
            'grupos_con_estudiantes' => $grupos->where('estudiantes_count', '>', 0)->count(),
            'grupos_sin_estudiantes' => $grupos->where('estudiantes_count', 0)->count(),
        ];
    }
}
