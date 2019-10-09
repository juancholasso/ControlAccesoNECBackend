<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model 
{
	//Tabla seleccionada
	protected $table = 'cuentas';

	//Campos de la tabla
    protected $fillable = [
        'id', 
        'email',
        'clave',
        'usuario',
        'rol',
        'token',
        'eliminado'
    ];

    //Llave primaria
    protected $primaryKey = 'id';

    public $timestamps = false;

    //Elementos ocultos
    protected $hidden = [
    ];

    // LLaves foraneas
    public function usuario()
    {
        return $this -> belongsTo(Usuario::class, 'usuario');
    }

    public function rol()
    {
        return $this -> belongsTo(RolCuenta::class, 'rol');
    }

}