<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Periodo extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'periodos';

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'anio_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function anio()
    {
        return $this->belongsTo(Anio::class);
    }

    public function notas()
    {
        return $this->hasMany(Nota::class);
    }

    public function definitivasAsignatura()
    {
        return $this->hasMany(DefinitivaAsignatura::class);
    }

    public function definitivasPeriodo()
    {
        return $this->hasMany(DefinitivaPeriodo::class);
    }
}
