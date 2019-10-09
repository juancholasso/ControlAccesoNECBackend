<?php

namespace App\Http\Controllers;
use App\Models\IngresoEquipo;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class IngresoEquipoController extends Controller
{

    public function __construct() { }

    /**
     *  Listar las areas
     */
    public function listar()
    {   
        $result = IngresoEquipo::with('marca')->with('estado')->with('tipo')->where('eliminado', 0)->get();
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
     *  Listar las areas
     */
    public function listarPorPermiso($id)
    {   
        $result = IngresoEquipo::
                with('marca')
                ->with('estado')
                ->with('tipo')
                ->where('permiso', $id)
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

    /**
     *  Consultar las areas
     */
     public function consultar($id)
    {
        try{
            $result = IngresoEquipo::with('marca')
                        ->with('estado')
                        ->with('tipo')
                        ->where('eliminado', 0)
                        ->where('eliminado', 0)
                        ->where('id',$id)
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
     *  Consultar equipos y Herramientas por fecha
     */

    public function consultarEquiposxFecha($fecha_inicial, $fecha_final)
    {   
        $fecha_inicial = $fecha_inicial." 00:00:00";
        $fecha_final = $fecha_final." 23:59:59";

        $result = IngresoEquipo::select('ingresos_equipos.color AS color', 'ingresos_equipos.cantidad AS cantidad',
        'estados_equipos.descripcion AS estado', 
        'marcas_equipos.descripcion AS marca', 
        'tipos_equipos.descripcion AS tipo',
        'permisos.fecha_inicial AS fecha_inicial',
        'permisos.fecha_final AS fecha_final',
        'usuarios.apellido AS apellido',
        'usuarios.nombre AS usuario')
        ->from('ingresos_equipos')
        ->join('estados_equipos','ingresos_equipos.estado', '=', 'estados_equipos.id')
        ->join('marcas_equipos','ingresos_equipos.marca', '=', 'marcas_equipos.id')
        ->join('tipos_equipos', 'ingresos_equipos.tipo', '=', 'tipos_equipos.id')
        ->join('permisos','ingresos_equipos.permiso', '=', 'permisos.id')
        ->join('usuarios', 'permisos.usuario', '=', 'usuarios.id')
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
     *  Fin Consultar equipos y Herramientas por fecha
     */


    /**
     *  Agregar una nueva areas
     */
   public function insertar(Request $request)
    {
        $data = array(
            'color' => $request['color'],
            'cantidad' => $request['cantidad'],
            'estado' => $request['estado'],
            'marca' => $request['marca'],
            'tipo' => $request['tipo'],
            'permiso' => $request['permiso'],
            'eliminado'=> 0
        );
        try {
            $areas = IngresoEquipo::insert($data);
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
            'id' => $request['id'],
            'color' => $request['color'],
            'cantidad' => $request['cantidad'],
            'estado' => $request['estado'],
            'marca' => $request['marca'],
            'tipo' => $request['tipo'],
            'permiso' => $request['permiso'],
        );
        try {
            IngresoEquipo::findOrFail($id) -> update($data);
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
            IngresoEquipo::findOrFail($id) -> update($data);
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
