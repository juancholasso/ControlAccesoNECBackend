<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salon extends Model
{
    //tabla selecionada
    protected $table = 'salones';

    //campos de la tabla
    protected $fillable = [
        'id',
        'descripcion',
        'sitio',
        'eliminado'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];

      //Llaves foraneas
    
    public function sitio()
    {
        return $this -> belongsTo(Sitio::class, 'sitio');
    }
}