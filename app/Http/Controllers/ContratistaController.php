<?php

namespace App\Http\Controllers;
use App\Models\Contratista;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ContratistaController extends Controller
{

    public function __construct() { }

    /**
     *  Listar los salones
     */
    public function listar()
    {   
        $result = Contratista::with('usuario')->where('eliminado', 0)->get();
        if (count($result) > 0) {
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
        $result = Contratista::with('usuario')->with('usuario.grupo')->get();
        if (count($result) > 0) {
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
     *  Consultar los contratistas
     */
    public function consultar($id)
    {
        try{
            $result = Contratista::with('usuario')->where('id', $id)->where('eliminado', 0)->first();
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
     *  Consultar los contratistas por usuario
     */
    public function consultarPorUsuario($id)
    {
        try{
            $result = Contratista::with('usuario')->where('eliminado', 0)->where('usuario', $id)->first();
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
     *  Agregar un nuevo contratista
     */
    public function insertar(Request $request)
    {
        $data = array(
            'cargo' => $request['cargo'],
            'elementos' => $request['elementos'],
            'induccion' => $request['induccion'],
            'seguridad_ocupacional' => $request['seguridad_ocupacional'],
            'salud_ocupacional' => $request['salud_ocupacional'],
            'usuario' => $request['usuario'],
            'eliminado'=> 0
        );
        try {
            $contratista = Contratista::insert($data);
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
     *  Actualizar o renovar un salon
     */
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array( 
            'cargo' => $request['cargo'],
            'elementos' => $request['elementos'],
            'induccion' => $request['induccion'],
            'seguridad_ocupacional' => $request['seguridad_ocupacional'],
            'salud_ocupacional' => $request['salud_ocupacional'],
        );
        try {
            Contratista::findOrFail($id) -> update($data);
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
     *  Remover un salon
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            $contratista=Contratista::findOrFail($id);
            Usuario::findOrFail($contratista->usuario) -> update($data);
            $contratista -> update($data);
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