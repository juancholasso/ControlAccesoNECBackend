<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    //tabla selecionada
    protected $table='eventos';

    //campos de la tabla
    protected $fillable = [
        'id',
        'nombre',
        'fecha_inicial',
        'fecha_final',
        'salon',
        'responsable',
        'observaciones',
        'eliminado'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];

    public function salon()
    {
        return $this -> belongsTo(Salon::class, 'salon');
    }

    public function responsable()
    {
        return $this -> belongsTo(Usuario::class, 'responsable');
    }

}