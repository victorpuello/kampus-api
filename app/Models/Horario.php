<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Horario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'horarios';

    protected $fillable = [
        'asignacion_id',
        'aula_id',
        'franja_id',
        'dia_semana',
    ];

    public function asignacion()
    {
        return $this->belongsTo(Asignacion::class);
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    public function franja()
    {
        return $this->belongsTo(FranjaHoraria::class, 'franja_id');
    }

    public function inasistencias()
    {
        return $this->hasMany(Inasistencia::class);
    }
} 