<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngresoVehiculo extends Model
{
    //tabla selecionada
    protected $table='ingresos_vehiculos';

    //campos de la tabla
    protected $fillable = [
        'id',
        'placa',
	    'marca',
	    'tipo',
        'eliminado'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];

    public function tipo()
    {
        return $this -> belongsTo(TipoVehiculo::class, 'tipo');
    }
}