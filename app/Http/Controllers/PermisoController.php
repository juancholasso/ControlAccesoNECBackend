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

    
    public function listarUltimoIngreso()
    {   
        $result = DB::select('SELECT MAX(per.fecha_inicial)as fecha_ingreso,per.fecha_final,usu.documento,usu.nombre,usu.apellido,usu.id as idUsuario,per.id as 					idPermiso,per.entrada,per.eliminado
        FROM permisos as per
        JOIN usuarios as usu
        ON per.usuario = usu.id
        WHERE per.entrada = 1
        AND per.eliminado = 0
        GROUP BY per.usuario
        ORDER BY MAX(per.fecha_inicial) DESC');

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
            if (!empty($result))  {
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
        $tipo_permiso= $request['tipo_permiso'];
        $fecha_final = $request['fecha_final'];
        if ($tipo_permiso != 1 || $fecha_final)
        { 
          
            $new_permiso = new Permiso();
            $new_permiso->usuario = $request['usuario'];
            $new_permiso->entrada = 0;
	        $new_permiso->eliminado = 0;
            $new_permiso->fecha_inicial = $request['fecha_inicial'];
            $new_permiso->fecha_final = $request['fecha_final'];
            $new_permiso->tipo_permiso = $request['tipo_permiso'];
        }else{
           
            $new_permiso = new Permiso();
            $new_permiso->usuario = $request['usuario'];
            $new_permiso->entrada = 0;
	        $new_permiso->eliminado = 0;
            $new_permiso->fecha_inicial = $request['fecha_inicial'];
            $new_permiso->fecha_final = '4444-01-01 00:00:00';
            $new_permiso->tipo_permiso = $request['tipo_permiso'];
           
        }
        
        try{
            $new_permiso->save();
            $id_permiso = $new_permiso->id;

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
        $result = PermisosSubsitio::select('*', 'neofaces.usuario as neou')
        ->from('permisos_subsitio')
        ->join('subsitios', 'permisos_subsitio.subsitio', '=', 'subsitios.id')
        ->join('sitios', 'subsitios.sitio', '=', 'sitios.id')
        ->join('neofaces', 'sitios.neoface', '=', 'neofaces.id')
        ->join('permisos', 'permisos_subsitio.permiso', '=', 'permisos.id')
        ->join('usuarios', 'permisos.usuario', '=', 'usuarios.id')
        ->where('permisos.id', '=', $id)
        ->get()
        ->toArray();

      
        $idusuario = $result[0]['id'];
        $guid = $result[0]['guid'];
        $ip = $result[0]['ip'];
        $port = $result[0]['puerto'];
        $user = $result[0]['neou'];
        $pass = $result[0]['clave'];

        $usuario = Usuario::where('guid', $guid)->first();
     
        $result2 = PermisosSubsitio::select('*')
        ->from('permisos_subsitio')
        ->join('subsitios', 'permisos_subsitio.subsitio', '=', 'subsitios.id')
        ->join('sitios', 'subsitios.sitio', '=', 'sitios.id')
        ->join('neofaces', 'sitios.neoface', '=', 'neofaces.id')
        ->join('permisos', 'permisos_subsitio.permiso', '=', 'permisos.id')
        ->join('usuarios', 'permisos.usuario', '=', 'usuarios.id')
        ->where('usuarios.id', '=', $idusuario)
        ->where ('permisos.id', '<>' ,$id)    
        ->get()
        ->toArray();

        $permisosUSuarioTotal = array();
        foreach ($result2 as $i => $permiso) {
          $ip = $result2[$i]['ip'];
          array_push($permisosUSuarioTotal, $ip);
        }

        $permisosUSuario = array();
        foreach ($result as $i => $permiso) {
          $ip = $result[$i]['ip'];
          array_push($permisosUSuario, $ip);
        }

        $resultado = array_diff($permisosUSuario, $permisosUSuarioTotal);
        
        if($resultado == null){
            try {
                $data = array(
                    'eliminado' => 1,
                );
                Permiso::findOrFail($id) -> update($data);
                $message = "permiso";
                return response() -> json(
                    array('data' => [$message], 'message' => config('constants.messages.7.message')),
                    config('constants.messages.7.code')
                );
            } catch (ModelNotFoundException $e) {
                return response() -> json(
                    array('data' => [], 'message' => config('constants.messages.2.message')),
                    config('constants.messages.2.code')
                );
            }
        }else{
           
            $sync = new NeoFaceController;

            $desonrolar = $sync -> ELIMINAR_USUARIO_NEOFACES($usuario, $ip, $port, $user, $pass);  
            try {
                $data = array(
                    'eliminado' => 1,
                );
                Permiso::findOrFail($id) -> update($data);
                $message = "usuario";
                return response() -> json(
                    array('data' => [$message], 'message' => config('constants.messages.7.message')),
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


    public function estadoEntrada($id)
    {
        $data = array(
            'entrada' => 0,
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
    
}