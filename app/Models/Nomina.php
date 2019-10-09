<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nomina extends Model
{
    //tabla selecionada
    protected $table = 'v_nominac';

    //campos de la tabla
    protected $fillable = [
        'ns3fecha',
        'ns3cc',
        'ns3horai',
        'ns3horaf',
        'ns3ubicacion'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];
}