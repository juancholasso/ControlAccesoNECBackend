<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoUsuario extends Model 
{
	//Tabla seleccionada
	protected $table = 'grupos_usuarios';

	//Campos de la tabla
    protected $fillable = [
        'id', 
        'guid',
        'nombre',
        'foto',
        'guidfoto',
        'eliminado'
    ];

    //Llave primaria
    protected $primaryKey = 'id';

    public $timestamps = false;

    //Elementos ocultos
    protected $hidden = [
    ];

}