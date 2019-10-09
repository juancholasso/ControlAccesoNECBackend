<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model 
{
	//Tabla seleccionada
	protected $table = 'ingresos';

	//Campos de la tabla
    protected $fillable = [
        'id', 
        'ingreso',
        'salida',
        'puerta',
        'usuarios',
        'eliminado'
    ];

    //Llave primaria
    protected $primaryKey = 'id';

    public $timestamps = false;

    //Elementos ocultos
    protected $hidden = [
    ];


    //Llaves foraneas
    public function puerta()
    {
        return $this -> belongsTo(Puerta::class, 'puerta');
    }

    public function usuario()
    {
        return $this -> belongsTo(Usuario::class, 'usuario');
    }
    
    /*
    public function genero()
    {
        return $this -> belongsTo(GeneroUsuario::class, 'genero');
    }

    public function herramientas_asignadas()
    {
        return $this -> hasMany(HerramientaAsignada::class, 'usuario', 'id');
    }
    */

}
