<?php

namespace App\Http\Controllers;
use App\Models\Evento;
use App\Models\UsuarioEvento;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class EventoController extends Controller
{

    public function __construct() { }

    /**
     *  Listar las areas
     */
    public function listar()
    {   
        $result = Evento::with('salon')->with('responsable')->where('eliminado', 0)->orderBy('id', 'DESC')->get();
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
     *  Consultar las areas
     */
    public function consultar($id)
    {
        try{
            $result = Evento::with('salon')->with('responsable')->where('eliminado', 0)->where('id', $id)->first();
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
     *  Agregar un nuevo 
     */
    public function insertar(Request $request)
    {
       // $request = $request -> json() -> all();
        $data = array(
            'nombre' => $request['nombre'],
            'fecha_inicial'=> $request['fecha_inicial'],
            'fecha_final'=> $request['fecha_final'],
            'salon'=> $request['salon'],
            'responsable'=> $request['responsable'],
            'observaciones'=> $request['observaciones'],
	    'eliminado'=> 0
        );

        try {
            $areas = Evento::insert($data);
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
     *  Actualizar o renovar una area
     */
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array( 
            'nombre' => $request['nombre'],
            'fecha_inicial'=> $request['fecha_inicial'],
            'fecha_final'=> $request['fecha_final'],
            'salon'=> $request['salon'],
            'responsable'=> $request['responsable'],
            'observaciones'=> $request['observaciones'],
        );
        try {
            Evento::findOrFail($id) -> update($data);
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
            Evento::findOrFail($id) -> update($data);
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
/**
     *  Consultar x evento
     */
    public function consultarxevento($idevento)
    {
        $result = UsuarioEvento::select('usuarios.nombre as usuario', 'eventos.nombre AS evento',
            'tipos_documentos.descripcion as tipo_documento', 'usuarios.documento as documento')
         ->from('usuarios_eventos')
         ->join('eventos', 'usuarios_eventos.evento', '=', 'eventos.id')
         ->join('usuarios', 'usuarios_eventos.usuario', '=', 'usuarios.id')
         ->join('tipos_documentos', 'usuarios.tipo_documento', '=', 'tipos_documentos.id')
         ->where('eventos.id', '=', $idevento)
         ->get();
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
     *  Fin Consultar x evento
     */

     /**
     *  Contar asistentes a  evento
     */

    public function contarevento($idevento)
    {
        $result = UsuarioEvento::select('usuarios_eventos.id')
         ->from('usuarios_eventos')
         ->join('eventos', 'usuarios_eventos.evento', '=', 'eventos.id')
         ->join('usuarios', 'usuarios_eventos.usuario', '=', 'usuarios.id')
         ->where('eventos.id', '=', $idevento)
         ->get();
         
        if (count($result) > 0) {
            echo count($result);
        }else{
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.4.message')),
                config('constants.messages.4.code')
            );
        }
    }

    /**
     *  Fin Contar asistentes a  evento
     */


}
