<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repuesto extends Model
{
    protected $table = 'repuestos';
    protected $fillable = [
        'cotizacion_id', 'nombre', 'numero_pieza','cantidad', 'check','precio','marca','procedencia'
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function imagenes()
    {
        return $this->hasMany(Imagen::class);
    }
}