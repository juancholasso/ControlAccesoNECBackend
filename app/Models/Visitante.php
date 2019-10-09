<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitante extends Model
{
    //tabla selecionada
    protected $table='visitantes';

    //campos de la tabla
    protected $fillable = [
        'id',
        'descripcion',
        'responsable',
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
        return $this->belongsTo(Usuario::class, 'usuario');
      }
}