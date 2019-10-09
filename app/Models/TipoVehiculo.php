<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoVehiculo extends Model
{
    //tabla selecionada
    protected $table='tipos_vehiculos';

    //campos de la tabla
    protected $fillable = [
        'id',
        'descripcion',
        'eliminado'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];
}