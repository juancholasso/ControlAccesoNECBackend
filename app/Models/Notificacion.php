<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    //tabla selecionada
    protected $table = 'notificacion';

    //campos de la tabla
    protected $fillable = [
        'id',
        'horas_sin_salir',
        'usuario',
        'eliminado'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];


      public function usuario()
      {
          return $this -> belongsTo(Usuario::class, 'usuario');
      }
}
