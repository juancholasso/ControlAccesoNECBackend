<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Controladora extends Model
{
    //tabla selecionada
    protected $table='controladoras';

    //campos de la tabla
    protected $fillable = [
        'id',
        'ip',
        'mac',
        'command_code',
        'parameters',
        'parameters_salida',
        'puerta',
        'fecha',
        'eliminado'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];

    //Llaves foraneas
    public function puerta()
    {
        return $this -> belongsTo(Puerta::class, 'puerta');
    }
}