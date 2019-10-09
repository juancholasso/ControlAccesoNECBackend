<?php

namespace App\Http\Controllers;
use App\Models\AsignacionTurno;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AsignacionTurnoController extends Controller
{

    public function __construct() { }

    /**
     *  Listar las asignaciones
     */
    public function listar()
    {   
        $result = AsignacionTurno::with('turno')->with('permiso')->where('eliminado', 0)->get();
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
     *  Listar las asignaciones por usuario
     */
    public function listarPorUsuario($id)
    {   
        $result = AsignacionTurno::
                  select('asignaciones_turnos.*')
                ->with('turno')
                ->with('permiso')
                ->join('permisos', 'permisos.id', '=', 'asignaciones_turnos.permiso')
                ->join('usuarios', 'usuarios.id', '=', 'permisos.usuario')
                ->where('usuarios.id', $id)
                ->where('asignaciones_turnos.eliminado', 0)
                ->get();
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
     *   Listar por permiso
     */

     public function listarPorPermiso($id)
     {
        $result = AsignacionTurno::
                      with('turno')
                    ->with('permiso')
                    ->where('eliminado', 0)
                    ->where('permiso', $id)
                    ->get();
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
     *  Consultar una asignacion
     */
    public function consultar($id)
    {
        try{
            $result = AsignacionTurno::with('turno')->with('permiso')->where('id', $id)->where('eliminado', 0)->first();
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
     *  Agregar una nueva asignacion
     */
    public function insertar(Request $request)
    {
        $data = array(
            'turno' => $request['turno'],
            'permiso' => $request['permiso'],
            'eliminado'=> 0
        );
        try {
            $areas = AsignacionTurno::insert($data);
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
     *  Actualizar o renovar una asignacion
     */
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array(
            'turno' => $request['turno'],
            'permiso' => $request['permiso']
        );
        try {
            AsignacionTurno::findOrFail($id) -> update($data);
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.6.message')),
                config('constants.messages.6.code')
            );
        } catch (ModelNotFoundException $e) {
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.2.message')),
                config('constants.messages.2.code')
            );
        }
    }

    /**
     *  Remover las areas
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            AsignacionTurno::findOrFail($id) -> update($data);
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
