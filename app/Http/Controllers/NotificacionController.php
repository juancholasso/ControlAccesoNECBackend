<?php

namespace App\Http\Controllers;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PhpAes\Aes;

class NotificacionController extends Controller
{

    public function __construct() { }

    /**
     *  Listar todos los Cuentas del sistema
     */
    public function listar()
    {   
        $result = Notificacion::all();
        if (!empty($result)) {
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.3.message')),
                config('constants.messages.3.code')
            );
        }else{
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.4.message')),
                config('constants.messages.4.code')
            );
        }
    }
    /**
     *  Consultar las marcas de los equipos
     */
    public function consultar($id)
    {
        try{
            $result = Notificacion::all()->first();
            return response()-> json(
                array('data' => $result, 'message' => config('constants.messages.3.message')),
                config('constants.messages.3.code'));
        }catch (Exception $ex){
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.4.message')),
                config('constants.messages.4.code')
            );
        }
    }

    /**
     *  Agregar una nueva marca de equipo
     */
    public function insertar($usuarios_supera_horas_limite)
    {
        $data = array(
            'horas_sin_salir' => $usuarios_supera_horas_limite['horas_sin_salir'],
            'idUsuario'=> $usuarios_supera_horas_limite['id'],
            'nomUsuario'=> $usuarios_supera_horas_limite['nombre'],
            'apeUsuario'=> $usuarios_supera_horas_limite['apellido'],
            'eliminado' => 0
        );
        try {
            $Notificacion = Notificacion::insert($data);
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.5.message')),
                config('constants.messages.5.code')
            );
        } catch (QueryException $e) {
            return response() -> json(
                array('data' => $e, 'message' => config('constants.messages.2.message')),
                config('constants.messages.2.code')
            );
        }
    }

    /**
     *  Actualizar o renovar una marca de equipo
    **/
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array(
            'horas_sin_salir' => $request['horas_sin_salir'],
            'idUsuario'=> $request['idUsuario'],
            'nomusuario'=> $request['nomusuario'],
            'apeUsuario'=> $request['apeUsuario'],
            'eliminado' => 0
        );
        try {
            $Notificacion = Notificacion::findOrFail($id) -> update($data);
            return response() -> json(
                array('data' => $Notificacion, 'message' => config('constants.messages.5.message')),
                config('constants.messages.5.code')
            );
        } catch (QueryException $e) {
            return response() -> json(
                array('data' => $e, 'message' => config('constants.messages.2.message')),
                config('constants.messages.2.code')
            );
        }
    }


    /**
     *  Remover una marca de equipo de la base de datos
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            Notificacion::findOrFail($id) -> update($data);
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.7.message')),
                config('constants.messages.7.code')
            );
        } catch (ModelNotFoundException $e) {
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.2.message')),
                config('constants.messages.2.code')
            );
        }
    }
}