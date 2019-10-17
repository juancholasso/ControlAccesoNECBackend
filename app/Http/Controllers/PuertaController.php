<?php

namespace App\Http\Controllers;
use App\Models\Puerta;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PuertaController extends Controller
{

    public function __construct() { }

    /**
     *  Listar las puertas
     */
    public function listar()
    {   
        $result = Puerta::with('tipo_puerta')->with('subsitio')->where('eliminado', 0)->get();
        if ($result != null) {
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
     *  Consultar las puertas
     */
    public function consultar($id)
    {
        try{
            $result = Puerta::where('id', $id)
            ->with('controladoras')->first(); //Puerta::with('tipo_puerta')->with('subsitio')->where('id', $id)->where('eliminado', 0)->first();
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
     *  Agregar una nueva puerta
     */
    public function insertar(Request $request)
    {
        $data = array(
            'guid' => GUID(),
            'descripcion' => $request['descripcion'],
            'subsitio' => $request['subsitio'],
            'tipo_puerta' => $request['tipo_puerta'],
            'urlCamara' => $request['urlCamara'],
            'eliminado'=> 0
        );
        try {
            $areas = Puerta::insert($data);
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
     *  Actualizar o renovar una puerta
     */
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array(
            'descripcion' => $request['descripcion'],
            'subsitio' => $request['subsitio'],
            'tipo_puerta' => $request['tipo_puerta'],
            'urlCamara' => $request['urlCamara'],
            'eliminado'=> 0
        );
        try {
            Puerta::findOrFail($id) -> update($data);
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
     *  Remover las puertas
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            Puerta::findOrFail($id) -> update($data);
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
