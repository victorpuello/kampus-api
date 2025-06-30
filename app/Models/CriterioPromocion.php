<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CriterioPromocion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'criterios_promocion';

    protected $fillable = [
        'anio_id',
        'nota_minima_aprobacion',
        'max_areas_reprobadas',
        'asistencia_minima',
    ];

    protected $casts = [
        'nota_minima_aprobacion' => 'decimal:2',
        'max_areas_reprobadas' => 'integer',
        'asistencia_minima' => 'decimal:2',
    ];

    public function anio()
    {
        return $this->belongsTo(Anio::class);
    }
} 