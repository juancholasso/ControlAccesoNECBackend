<?php

namespace App\Http\Controllers;
use App\Models\Empleado;
use App\Models\Ingreso;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmpleadoController extends Controller
{

    public function __construct() { }

    /**
     *  Listar los Empleado
     */
    public function listar()
    {   
        $result = Empleado::
                with('usuario')
                ->with('usuario.area')
                ->with('usuario.tipo_documento')
                ->with('usuario.tipo_usuario')
                ->with('usuario.grupo')
                ->where('eliminado', 0)
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

    
    public function listarTodos()
    {   
        $result = Empleado::with('usuario')->with('usuario.grupo')->get();
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

    public function consultarPorUsuario($id)
    {   
        $result = Empleado::
                with('usuario')
                ->with('usuario.area')
                ->with('usuario.tipo_documento')
                ->where('eliminado', 0)
                ->where('usuario', $id)
                ->first();
        if ( $result != null) {
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
     *  Consultar los Empleado
     */
    public function consultar($id)
    {
        try{
            $result = Empleado::
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
     *  Agregar un nuevo empleado
     */
    public function insertar(Request $request)
    {
        $data = array(
            'contrato' => NULL, // $request['contrato'],
            'usuario' => $request['usuario'],
            'tipo' => $request['tipo'],
            'eliminado'=> 0
        );
        try {
            $Empleado = Empleado::insert($data);
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
     *  Actualizar o renovar un empleado
     */
    public function actualizar(Request $request)
    {
        $id = $request['id'];
        $data = array( 
            //'contrato' => $request['contrato'],
            'tipo' => $request['tipo'],
            'usuario' => $request['usuario']
        );
        try {
            Empleado::findOrFail($id) -> update($data);
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
     *  Remover un empleado
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            
            $empleado = Empleado::findOrFail($id);
            $usuario = Usuario::findOrFail($empleado->usuario);
            $usuario -> update($data);
            $empleado -> update($data);
            
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
}