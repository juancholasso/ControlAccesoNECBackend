<?php

namespace App\Http\Controllers;
use App\Models\Permiso;
use App\Models\Subsitio;
use App\Models\Ingreso;
use App\Models\PermisosSubsitio;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Http\Controllers\NeoFaceController;

class PermisoController extends Controller
{

    public function __construct() { }

    /**
     *  Listar todos los permisos de los usuarios
     */
    public function listar()
    {   
        $result = Permiso::
                  with('tipo_permiso')
                ->with('usuario')
                ->with('subsitios')
                //->with('turnos')
                ->with('excepciones')
                ->get();
        if (!count($result) > 0) {
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
     *  Listar todos los permisos de los usuarios
     */
    public function listarPorUsuario($id)
    {   
        $result = Permiso::
                  with('tipo_permiso')
                ->with('usuario')
                ->with('subsitios')
                //->with('turnos')
                ->with('excepciones')
                ->where('usuario', $id)
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

    /**
     *  Consultar la información de un permiso en la base de datos
     */
    public function consultar($id)
    {   
        $result = Permiso::
                  with('tipo_permiso')
                ->with('usuario')
                ->with('subsitios')
               // ->with('turnos')
                ->with('excepciones')
                ->where('id', $id)
                ->where('eliminado', 0)
                ->first();
        if (!empty($result)) {
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.3.message')),
                config('constants.messages.3.code')
            );
        }else{
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.4.message')),
                config('constants.messages.4.code')
            );
        }
    }

    /**
     *  Agregar un nuevo permiso para un usuario
     */
    public function insertar(Request $request)
    {
        try {
            $new_permiso = new Permiso();
            $new_permiso->usuario = $request['usuario'];
            $new_permiso->entrada = 0;
	    $new_permiso->eliminado = 0;
            $new_permiso->fecha_inicial = $request['fecha_inicial'];
            $new_permiso->fecha_final = $request['fecha_final'];
            $new_permiso->tipo_permiso = $request['tipo_permiso'];

            // $permiso = Permiso::insert($data);
            // print_r($permiso);
            $new_permiso->save();
            $id_permiso = $new_permiso->id;

            // echo $id_permiso;

            // exit();
            return response() -> json(
                array('data' => ["newid"=>$id_permiso], 'message' => config('constants.messages.5.message')),
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
     *  Actualizar o renovar permiso de un usuario
     */
    public function actualizar(Request $request)
    {        
        $objectNeoFace = new NeoFaceController;
        $id = $request['id'];

        $data = array(
            'fecha_inicial' => $request['fecha_inicial'],
            'fecha_final' => $request['fecha_final'],
            'hora_inicial' => $request['hora_inicial'],
            'hora_final' => $request['hora_final'],
            'tipo_permiso' => $request['tipo_permiso'],
            'usuario' => $request['usuario'],
            'sitio' => $request['sitio'],
        );
       
        try {
            $permiso = Permiso::findOrFail($id);
            $permiso->update($data);
           
            $resCURL = $this->sincronizacionUsuarioPorPermiso($request['usuario']);
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
     *  Remover permiso de un usuario
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            Permiso::findOrFail($id) -> update($data);
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
     *  Generar sticker de visitante para su ingreso
     */
    public function sticker($id)
    {
        $permiso = Permiso::with('usuario')->with('sitio')->where('id', $id)->first();
        $data = array();
        if(!empty($permiso) > 0)
        {
            $data['nombres'] = $permiso['usuario']['nombres'];
            $data['apellidos'] = $permiso['usuario']['apellidos'];
            $data['tipo_usuario'] = "Visitante";
            $data['fecha_inicial'] = $permiso['fecha_inicial'];
            $data['fecha_final'] = $permiso['fecha_final'];
            return view('card', ['data' => $data]);
        }else{
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.4.message')),
                config('constants.messages.4.code')
            );
        }
    }

    public function exportarPermiso($id, $fecha_inicial, $fecha_final)
    { $fecha_inicial = $fecha_inicial . " 00:00:00";
        $fecha_final = $fecha_final . " 23:59:59";
       
        $result = Permiso::select('fecha_inicial', 'fecha_final', 
        'descripcion',
        'nombre as usuario',  
        'entrada')
        
        ->from('permisos as permiso')
        ->join('tipos_permisos AS tipo_permiso', 'permiso.tipo_permiso', '=', 'tipo_permiso.id')
        ->join('usuarios as usuario', 'permiso.usuario', '=', 'usuario.id')
        ->where('usuario.id', '=', $id)
        ->where('fecha_inicial', '>=', $fecha_inicial)
        ->where('fecha_final', '<=', $fecha_final)
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

    	

   public function insertarPermisoxSubsitio(Request $request){
        
        $data = array(
            'permiso' => $request['permiso'],
            'subsitios' => $request['subsitios'],
            'marcotodo' => $request['marcotodo'],
            'eliminado' => 0, 
        );
            if($data['marcotodo'] == true){
                try{
                    $permiso = Permiso::findOrFail($data['permiso']);
                     $result = Subsitio::where('eliminado',0)->get();
                foreach($result as $subsitio){
                    $permiso->subsitios()->attach($subsitio['id']);
                }
                return response() -> json(
                    array('data' => [], 'message' => config('constants.messages.5.message')),
                    config('constants.messages.5.code')
                );

                }catch (QueryException $e){
                    return response() -> json(
                        array('data' => $e, 'message' => config('constants.messages.2.message')),
                        config('constants.messages.2.code')
                    );
                } 
            }else{

        try {

            $permiso = Permiso::findOrFail($data['permiso']);

            foreach($request['subsitios'] as $idsubsitio){
                $subsitio = Subsitio::findOrFail($idsubsitio);
                $permiso->subsitios()->attach($subsitio);
            }
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.5.message')),
                config('constants.messages.5.code')
            );
            }catch (QueryException $e) {
                return response() -> json(
                    array('data' => $e, 'message' => config('constants.messages.2.message')),
                    config('constants.messages.2.code')
                );
            }
        }
    }
	public function editarPermisoxSubsitio(Request $request){
        
        $data = array(
            'permiso' => $request['permiso'],
            'subsitios' => $request['subsitios'],
            'marcotodo' => $request['marcotodo'],
            'eliminado' => 0,
        );

        if($data['marcotodo'] == true){
            try{
                $permiso = Permiso::findOrFail($data['permiso']);
                DB::table('permisos_subsitio')->where('permiso', '=', $data['permiso'])->delete();
                 $result = Subsitio::where('eliminado',0)->get();
            foreach($result as $subsitio){
                $permiso->subsitios()->attach($subsitio['id']);
            }
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.5.message')),
                config('constants.messages.5.code')
            );

            }catch (QueryException $e){
                return response() -> json(
                    array('data' => $e, 'message' => config('constants.messages.2.message')),
                    config('constants.messages.2.code')
                );
            } 
        }else{
            try {

                $permiso = Permiso::findOrFail($data['permiso']);
              DB::table('permisos_subsitio')->where('permiso', '=', $data['permiso'])->delete();
                foreach($request['subsitios'] as $idsubsitio){
                    $subsitio = Subsitio::findOrFail($idsubsitio);
                    $permiso->subsitios()->attach($subsitio);
                }
               
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
       
    }

    public function sincronizacionUsuarioPorPermiso($idusuario){
        $objectNeoFace = new NeoFaceController;
        $fechaActualUnix = strtotime(date('Y-m-d H:i:s'));
        //Bandera para saber si el usuario no tiene ningún permiso vigente
        $desenrrolar = true;
        //Permisos del usuario
        $permisos = Permiso::where('usuario','=',$idusuario)->get();
        //Recorremos los permisos y verificamos que esten vigentes
        foreach($permisos as $permiso){
            //Si alguno de los permisos está vigente, entonces cambiamos desenrrollar a falso
            if(  $fechaActualUnix >= strtotime($permiso['fecha_inicial']) &&  $fechaActualUnix <= strtotime($permiso['fecha_final']) ){
                $desenrrolar = false;
                break;
            }
        }
        if($desenrrolar){
            //Si desenrrollar es true entonces dessincronizamos el usuario
            $usuario = Usuario::find($idusuario);
            $usuario->neoface = 0;
            if($usuario->save()){
                return $objectNeoFace->ELIMINAR_USUARIO($usuario->guid);
            }
        }
        else{
            //Si desenrrollar es false entonces sincronizamos el usuario
            $usuario = Usuario::find($idusuario);
            $usuario->neoface = 1;
            if($usuario->save()){
                $objectNeoFace->SINCRONIZAR_USUARIO($usuario->id);
            }                
        }
    }
    
}