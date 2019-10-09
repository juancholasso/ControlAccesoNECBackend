<?php

namespace App\Http\Controllers;
use App\Models\EstadoHerramienta;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EstadoHerramientaController extends Controller
{
    public function __construct() { }

    /**
     *  Listar los estados de herramientas
     */
    public function listar()
    {   
        $result = EstadoHerramientas::all();
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
     *  Consultar el estado de herramientas
     */
    public function consultar($id)
    {
        try{
            $result = EstadoHerramientas::find($id);
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
     *  Agregar un nuevo estado de herramienta
     */
    public function insertar(Request $request)
    {
        $data = array(
            'descripcion' => $request['descripcion'],
            'eliminado'=> 0
        );
        try {
            $EstadoHerramientas = EstadoHerramientas::insert($data);
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
     *  Actualizar o renovar un estado de herramineta 
     */
    public function actualizar(Request $request, $id)
    {
        $id = $request['id'];
        $data = array( 
            'descripcion' => $request['descripcion']
        );
        try {
            EstadoHerramientas::findOrFail($id) -> update($data);
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
     *  Remover el estado de la heramineta de la base de datos
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            EstadoHerramientas::findOrFail($id) -> update($data);
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