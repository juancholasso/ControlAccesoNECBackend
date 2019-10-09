<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoUsuario extends Model 
{
	//Tabla seleccionada
	protected $table = 'tipos_usuarios';

	//Campos de la tabla
    protected $fillable = [
        'id', 
        'descripcion',
        'eliminado'
    ];

    //Llave primaria
    protected $primaryKey = 'id';

    public $timestamps = false;

    //Elementos ocultos
    protected $hidden = [
    ];

}