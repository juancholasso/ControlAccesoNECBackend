<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subsitio extends Model 
{
	//Tabla seleccionada
	protected $table = 'subsitios';

	//Campos de la tabla
    protected $fillable = [
        'id',
        'nombre',
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

    public function permisos()
    
    {
        return $this->belongsToMany(Permiso::class, 'permisos_subsitio', 'subsitio','permiso');
    }


    public function puertas()
    
    {
        return $this->hasMany(Puerta::class, 'subsitio','id');
    }

    
}