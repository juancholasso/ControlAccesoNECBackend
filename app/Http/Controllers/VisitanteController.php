<?php

namespace App\Http\Controllers;
use App\Models\Visitante;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\NeoFaceController;
use DB;


class VisitanteController extends Controller
{

    public function __construct() { }

    /**
     *  Listar los Visitante
     */
    public function listar()
    {   
        $result = Visitante::
                with('usuario')
                ->with('usuario.area')
                ->with('usuario.tipo_documento')
                ->where('eliminado', 0)
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

    public function listarTodos()
    {   
        $result = Visitante::with('usuario')->with('usuario.grupo')->get();
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

    public function consultarPorUsuario($id)
    {   
    try{
        $result = Visitante::
                where('usuario', $id)
                ->with('usuario')
                ->with('usuario.area')
                ->with('usuario.tipo_documento')
                ->where('eliminado', 0)
                
                ->first();
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.3.message')),
                config('constants.messages.3.code')
            );
        }catch (Exception $ex){
            return response() -> json(
                array('data' => [] , 'message' => config('constants.messages.4.message')),
                config('constants.messages.4.code')
            );
        }
    }


    /**
     *  Consultar los Visitante
     */
    public function consultar($id)
    {
        try{
            $result = Visitante::
              with('usuario')
            ->with('usuario.area')
            ->with('usuario.tipo_documento')
            ->where('eliminado', 0)
            ->where('id', $id)
            ->first();
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
     *  Agregar un nuevo Visitante
     */
    public function insertar(Request $request)
    {
        $data = array(
            'descripcion'=> $request['descripcion'],
            'responsable'=> $request['responsable'],
            'usuario' => $request['usuario'],
            'eliminado'=> 0
        );
        try {
            $Visitante = Visitante::insert($data);
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
     *  Actualizar o renovar un Visitante
     */
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array( 
            'descripcion'=> $request['descripcion'],
            'responsable'=> $request['responsable'],
            'usuario' => $request['usuario'],
            'eliminado' => 0
        );
        try {
            Visitante::findOrFail($id) -> update($data);
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
     *  Remover un Visitante
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            $visitante = Visitante::findOrFail($id);
            $usuario = Usuario::findOrFail($visitante->usuario);
            $usuario -> update($data);
            $visitante-> update($data);
            $neoface = new NeoFaceController;
            $desenrolNeoface = $neoface->ELIMINAR_USUARIO($usuario->guid);
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


    public function exportarconPermiso()
    {
        

       
    }
}
