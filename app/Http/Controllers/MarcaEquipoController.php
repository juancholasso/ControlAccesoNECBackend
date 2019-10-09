<?php

namespace App\Http\Controllers;
use App\Models\MarcaEquipo;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MarcaEquipoController extends Controller
{
    public function __construct() { }

    /**
     *  Listar las marcas de equipos
     */
    public function listar()
    {   
        $result = MarcaEquipo::where('eliminado', 0)->get();
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
     *  Consultar las marcas de los equipos
     */
    public function consultar($id)
    {
        try{
            $result = MarcaEquipo::where('eliminado', 0)
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
     *  Agregar una nueva marca de equipo
     */
    public function insertar(Request $request)
    {
        $data = array(
            'descripcion' => $request['descripcion'],
            'eliminado'=> 0
        );
        try {
            $MarcaEquipo = MarcaEquipo::insert($data);
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
     */
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array( 
            'descripcion' => $request['descripcion']
        );
        try {
            MarcaEquipo::findOrFail($id) -> update($data);
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
     *  Remover una marca de equipo de la base de datos
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            MarcaEquipo::findOrFail($id) -> update($data);
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