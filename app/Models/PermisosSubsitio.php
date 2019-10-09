<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermisosSubsitio extends Model
{
    //tabla selecionada
    protected $table='permisos_subsitio';

    //campos de la tabla
    protected $fillable = [
        'id',
        'permiso',
        'subsitio',
        'eliminado'
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];
      
      public function permiso()
      {
          return $this -> belongsTo(Permiso::class, 'permiso');
      }
      
      public function usuario()
      {
        return $this -> belongsTo(Usuario::class, 'usuario');
      }
  
}