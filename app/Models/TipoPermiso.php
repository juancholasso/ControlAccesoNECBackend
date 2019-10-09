<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPermiso extends Model 
{
	//Tabla seleccionada
	protected $table = 'tipos_permisos';
	
	//Campos de la tabla
    protected $fillable = [
        'descripcion',
        'eliminado'
    ];

    //Llave primaria
    protected $primaryKey = 'id';

    //Elementos ocultos
    protected $hidden = [
    ];

    public $timestamps = false;

}
