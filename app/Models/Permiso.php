<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model 
{
	//Tabla seleccionada
	protected $table = 'permisos';

	//Campos de la tabla
    protected $fillable = [
        'id', 
        'fecha_inicial',
        'fecha_final',
        'tipo_permiso',
        'usuario',
        'entrada',
        'eliminado'
    ];

    //Llave primaria
    protected $primaryKey = 'id';

    public $timestamps = false;

    //Elementos ocultos
    protected $hidden = [
    ];


    //Llaves foraneas
    public function tipo_permiso()
    {
        return $this -> belongsTo(TipoPermiso::class, 'tipo_permiso');
    }

    public function usuario()
    {
        return $this -> belongsTo(Usuario::class, 'usuario');
    }
    
    // LLaves foraneas
    public function turnos()
    {
        return $this->hasMany(Turno::class, 'permiso', 'id');
    }

    // LLaves foraneas
    public function excepciones()
    {
        return $this->hasMany(ExcepcionPermiso::class, 'permiso', 'id');
    }


    public function subsitios()
    {
        return $this->belongsToMany(Subsitio::class, 'permisos_subsitio', 'permiso','subsitio');
    }

}
