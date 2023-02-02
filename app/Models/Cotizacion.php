<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';
    protected $fillable = [
        'codigo', 'nombre', 'telefono', 'email', 'vin', 'estado','ejecutivo', 'marca', 'modelo', 'año', 'obs'
    ];

    public function repuestos()
    {
        return $this->hasMany(Repuesto::class);
    }
}
