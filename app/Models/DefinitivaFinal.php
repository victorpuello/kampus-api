<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefinitivaFinal extends Model
{
    use HasFactory;

    protected $table = 'definitivas_finales';

    protected $fillable = [
        'calificacion',
        'estudiante_id',
        'anio_id',
    ];

    protected $casts = [
        'calificacion' => 'decimal:2',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function anio()
    {
        return $this->belongsTo(Anio::class);
    }
}
