<?php

namespace App\Http\Controllers;
use App\Models\Transportadora;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TransportadoraController extends Controller
{

    public function __construct() { }

    /**
     *  Listar las Transportadora
     */
    public function listar()
    {   
        $result = Transportadora::where('eliminado', 0)->get();
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
     *  Consultar las Transportadora
     */
    public function consultar($id)
    {
        try{
            $result = Transportadora::where('id', $id)->where('eliminado', 0)->first();
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
     *  Agregar una nueva transportaodra
     */
    public function insertar(Request $request)
    {
        $data = array(
            'descripcion' => $request['descripcion'],
            'eliminado'=> 0
        );
        try {
            $Transportadora = Transportadora::insert($data);
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
     *  Actualizar o renovar una transportadora
     */
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array( 
            'id' => $request['id'],
            'descripcion' => $request['descripcion']
        );
        try {
            Transportadora::findOrFail($id) -> update($data);
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
     *  Remover una transportadora de la base de datos
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            Transportadora::findOrFail($id) -> update($data);
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