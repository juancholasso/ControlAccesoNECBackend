<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    //tabla selecionada
    protected $table = 'logs';

    //campos de la tabla
    protected $fillable = [
        'id',
        'actividad',
        'componente',
        'usuario',
        'rol',
        'fecha'
        
    ];
      //Llave primaria
      protected $primaryKey = 'id';

      public $timestamps = false;
  
      //Elementos ocultos
      protected $hidden = [
      ];
      
      public function actividad()
    {
        return $this -> belongsTo(Actividad::class, 'actividad');
    }
    
    public function componente()
    {
        return $this -> belongsTo(Componente::class, 'componente');
    }
    
    public function usuario()
    {
        return $this -> belongsTo(Cuenta::class, 'usuario');
    }

    public function rol()
    {
        return $this -> belongsTo(RolCuenta::class, 'rol');
    }
}