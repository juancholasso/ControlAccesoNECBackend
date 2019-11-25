<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Permiso;
use App\Models\Usuario;
use App\Models\Puerta;
use App\Models\Controladora;
use App\Models\Sitio;
use App\Models\Subsitio;
use App\Models\Neoface;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\NeoFaceController;
use File;
// Socket Client
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version2X;

class IngresoController extends Controller
{

    public function __construct() { }

    /**
     *  Listar todos los ingresos del sistema
     */
    public function listar()
    {   
        $result = Ingreso::with('usuario')
                            ->with('usuario.area')
                            ->with('usuario.tipo_documento')
                            ->with('usuario.tipo_usuario')
                            ->with('usuario.grupo')
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
     * Consultar la información de un ingreso por ID
     */
    public function consultar($id)
    {   
        $result = Ingreso::with('usuario')->where('id', $id)->first();
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



    public function ingresar(Request $request)
    {   
        $request = $request -> json() -> all();
        $guid = $request["usuario"];
        $match = $request["match"];

        date_default_timezone_set('America/Bogota');
        $fecha_actual = date('Y-m-d H:i:s');

        // Obtener ID del usuario
        $usuario = Usuario::where('guid', $guid)->first();

        // Obtener Id del sitio
        $info = Sitio::select('sitios.*', 'puertas.id as idPuerta', 'puertas.tipo_puerta as tipo_puerta')
         ->join('subsitios', 'subsitios.sitio', '=', 'sitios.id')
         ->join('puertas', 'puertas.subsitio', '=', 'subsitios.id')
         ->where('puertas.guid', $request['camara'])
         ->first();

         if($info['tipo_puerta'] == 1)
         {
            // Insertar nuevo registro
            $sitio = $info['id'];
            $puerta = $info['idPuerta']; 
            $res = $this->insertar($usuario, $fecha_actual, $puerta, $match);
            return $res;
         }else{
            // Actualizar registro existente
            $sitio = $info['id'];
            $puerta = $info['idPuerta'];
            $res = $this->actualizar($usuario, $puerta, $fecha_actual);
            return $res;
         }

    }


    /**
     *  Cuando un usuario registre su entrada la información será almacenada a través de este método
     *  Antes de registrar la información verifica si el usuario ya se encuentra dentro del sitio
     *  y si el usuario se encuentra en en el rango permitido de ingresos
     */
    public function insertar($usuario, $fecha_actual, $puertaid, $idmatch)
    {
        $neoface = new NeoFaceController;
        date_default_timezone_set('America/Bogota');
        $fecha_actual = date('Y-m-d H:i:s');
        
        $puerta = Puerta::find($puertaid);
       
        //Subsitio al cual quiere ingresar            
        $subsitioIngresarId = $puerta->subsitio;

        $subSitio = Subsitio:: find($subsitioIngresarId);

        $sitioIngresoid = $subSitio->sitio;


        //verificar si el usuario se encuentra dentro del sitio y si está autorizado en la fecha correspondiente
        $result = Permiso::
              where('entrada', 0)
            ->where('usuario', $usuario['id'])
            ->where('fecha_inicial', '<=', $fecha_actual)
            ->Where('fecha_final', '>=', $fecha_actual)
            ->Where('eliminado', '=', '0')
            ->get();

        $ingresoPermitido = false;
        $idPermisoIngreso = 0;

        foreach($result as $permiso){
            $subsitiosPermitidos = $permiso->subsitios;
            foreach($subsitiosPermitidos as $subsitioPermitido){
                if($subsitioIngresarId == $subsitioPermitido->id){
                    $ingresoPermitido = true;
                    $idPermisoIngreso = $permiso->id;
                    break 2;
                }
            }
        }
        
         // Si el usuario se encuentra afuera
        if(!$ingresoPermitido){
            $this->emitir($usuario, false, $puerta, "Entrada No Permitida", "", $puertaid);
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.8.message')),
                config('constants.messages.8.code')
            );
        }
       
        try {
            // Actualizar el valor entrada en la tabla permisos (Entrada)
                try {
                    Permiso::where('usuario', $usuario['id'])->where('id', $idPermisoIngreso)->update(['entrada' => 1]);
                } catch (\Throwable $th) {
                    
                    return response() -> json(
                        array('data' => [], 'message' => config('constants.messages.2.message')),
                        config('constants.messages.2.code')
                    );
                }

                //Traer infomración del neoface para traer imagen match

                $idNeoface= Sitio:: where ('id', $sitioIngresoid)
                ->pluck('neoface')
                ->first();
                $neoface_model = Neoface:: where('id', $idNeoface)->first();
                $ip= $neoface_model->ip; 
                $port=$neoface_model->puerto;
                $user=$neoface_model->usuario;
                $pass=$neoface_model->clave;
                
                //Traer imagen del match
                $imagenMatchB64 = $neoface->TRAER_IMAGEN_MATCH($idmatch, $ip, $port, $user, $pass)->data;                
                $content = base64_decode($imagenMatchB64);
                $file = fopen(base_path().'/public/uploads/match/'.$idmatch.'.jpg', "wb");
                fwrite($file, $content);
                fclose($file);

                // Estructurar datos para ingreso
                $data = array(
                    'ingreso' => $fecha_actual,
                    'salida' => null,
                    'puerta' => $puertaid,
                    'usuario' => $usuario['id'],
                    'fotousuario' => $idmatch.".jpg",
                    'kactus' => 0,
                    'eliminado' => 0
                );
            
                //Agregar ingreso
                Ingreso::insert($data);
                $this->emitir($usuario, true, $puerta, "Entrada", $idmatch.".jpg",$puertaid);
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
	
	public function emitir($usuario,$acceso,$puerta,$tipoIngreso,$urlImagenMatch,$puertaid)
    {
        $controladora = Controladora::where('puerta', $puertaid)->where('eliminado','0')->first();
        
        $client = new Client(new Version2X('http://localhost:8080/'));
        $client->initialize();
        $client->emit('acceso', [
             'usuario' => $usuario, 
             'acceso' => $acceso,
             'puerta' => $puerta,
             'tipoIngreso' => $tipoIngreso,
             'urlImagenMatch' => $urlImagenMatch,
        ]);
         if($acceso){
             $client->emit('control', [
                 'evento' => "control",                
                 'accion'=>"open",
                 'commandcode'=>$controladora['command_code'],
                 'evento'=>"control",
                 'macaddress'=>$controladora['mac'],
                 'parameters'=> $controladora['parameters']
             ]);
             $client->emit('control', [
                'evento' => "control",                
                'accion'=>"close",
                'commandcode'=>$controladora['command_code'],
                'evento'=>"control",
                'macaddress'=>$controladora['mac'],
                'parameters'=> $controladora['parameters_salida']
            ]);
         }
         $client->close();
    }

    /**
     *  Cuando un usuario registre su salida la información será actualizada a través de este método
     *  Antes de registrar la información verica si el usuario ya se encuentra afuera del sitio
     */
    public function actualizar($usuario, $puertaid, $fecha_actual)
    {
        $puerta = Puerta::find($puertaid);
        // SI el usuario ya se encuentra adentro
        $result = Permiso::
              where('entrada', 1)
            ->where('usuario', $usuario['id'])
            ->where('fecha_inicial', '<=', $fecha_actual)
            ->Where('fecha_final', '>=', $fecha_actual)
            ->get();
        

        //Subsitio del cual quiere salir           
        $subsitioIngresarId = $puerta->subsitio;

        $ingresoPermitido = false;
        $idPermisoIngreso = 0;

        //Verifica si tiene permisos sobre el subsitio
        foreach($result as $permiso){
            $subsitiosPermitidos = $permiso->subsitios;
            foreach($subsitiosPermitidos as $subsitioPermitido){
                if($subsitioIngresarId == $subsitioPermitido->id){
                    $ingresoPermitido = true;
                    $idPermisoIngreso = $permiso->id;
                    break 2;
                }
            }
        }

        if(!$ingresoPermitido){
            $this->emitir($usuario, false, $puerta, "Salida No Permitida", "");
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.8.message')),
                config('constants.messages.8.code')
            );
        }

        try {
            
            // Actualizar el valor entrada en la tabla permisos (Salida)
            try {
                // Obtener último ingreso
                $ingreso = Ingreso::where('usuario', $usuario['id'])->orderBy('ingreso', 'DESC')->first();

                // Actualizar permiso
                Permiso::where('usuario', $usuario['id'])->update(['entrada' => 0]);
                
                //Actualizar ingreso
                $data = array(
                    'salida' => $fecha_actual,
                    'puerta_salida' => $puerta->id
                );

                $res = Ingreso::
                         where('puerta', $ingreso['puerta'])
                        ->where('usuario', $usuario['id'])
                        ->where('id', $ingreso['id'])
                        ->update($data);
                
                if($res > 0)
                {

                    $this->emitir($usuario, true, $puerta, "Salida", $ingreso->fotousuario);
                    return response() -> json(
                        array('data' => [], 'message' => config('constants.messages.6.message')),
                        config('constants.messages.6.code')
                    );
                }else{
                    return response() -> json(
                        array('data' => [], 'message' => config('constants.messages.2.message')),
                        config('constants.messages.2.code')
                    );  
                }
                
            } catch (\Throwable $th) {
                return response() -> json(
                    array('data' => $th, 'message' => config('constants.messages.2.message')),
                    config('constants.messages.2.code')
                );
            }

            
        } catch (ModelNotFoundException $e) {
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.2.message')),
                config('constants.messages.2.code')
            );
        }
    }

    /**
     *   En caso que el registro no se desee almacenar más, podrá ser eliminado por medio de este método
     */
    public function eliminar($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            Ingreso::findOrFail($id) -> update($data);
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


    public function estadoIngreso($id)
    {
        $data = array(
            'eliminado' => 1,
        );
        try {
            Ingreso::where('usuario',$id)
            ->where('salida',null)
            ->update($data);
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
     *  Mostrar cantidad de empleados, visitantes y contratistas ingresados al dia
     */
    public function contar_usuarios($tipo_usuario, $fecha_inicial, $fecha_final)
    {
        try {

            
            date_default_timezone_set('America/Bogota');
            if($fecha_inicial == 'null' || $fecha_final == 'null')
            {
                $fecha_inicial = date('Y-m-d') . " 00:00:00";
                $fecha_final = date('Y-m-d') . " 23:59:59";
            }else{
                $fecha_inicial = $fecha_inicial . " 00:00:00";
                $fecha_final = $fecha_final . " 23:59:59";
            }
            
            
            $result = Ingreso::
                          select('ingresos.*')
                        ->join('usuarios', 'usuarios.id', 'ingresos.usuario')
                        ->where('usuarios.tipo_usuario', $tipo_usuario)
                        ->where('ingresos.ingreso', '>=', $fecha_inicial)
                        ->where('ingresos.ingreso', '<=', $fecha_final)
                        ->groupBy('ingresos.usuario')
                        ->get();
                        
            return response() -> json(
                array('data' => $result, 'message' => config('constants.messages.3.message')),
                config('constants.messages.3.code')
            );
        } catch (ModelNotFoundException $e) {
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.2.message')),
                config('constants.messages.2.code')
            );
        }
    }

    public function consultaIngreso($idIngreso)
     {
	    $result = Ingreso::select('ingresos.id AS id', 'ingresos.ingreso AS ingreso', 
	   'ingresos.salida AS salida', 'puertas.descripcion AS puerta', 'usuarios.nombre AS usuario', 'ingresos.fotousuario as fotousuario')
	    ->from('ingresos')
	    ->join('usuarios', 'ingresos.usuario', '=', 'usuarios.id')
	    ->join('puertas', 'ingresos.puerta', '=', 'puertas.id')
	    ->where('usuarios.id', '=', $idIngreso)
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

    public function exportarIngreso($id, $fecha_inicial, $fecha_final)
     {
         $fecha_inicial = $fecha_inicial . " 00:00:00";
         $fecha_final = $fecha_final . " 23:59:59";
         $result = Ingreso::select('ingresos.id AS id', 'ingresos.ingreso AS ingreso', 
         'ingresos.salida AS salida', 'puertas.descripcion AS puerta', 'usuarios.nombre AS usuario')
         ->from('ingresos')
         ->join('usuarios', 'ingresos.usuario', '=', 'usuarios.id')
         ->join('puertas', 'ingresos.puerta', '=', 'puertas.id')
         ->where('usuarios.id', '=', $id)
         ->where('ingreso', '>=', $fecha_inicial)
         ->where('salida', '<=', $fecha_final)
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

    public function filtrarxFechas($fecha_inicial, $fecha_final)
    {
        $fecha_inicial = $fecha_inicial . "";
         $fecha_final = $fecha_final . "";
         $result = Ingreso::select('ingresos.id AS id', 'ingresos.ingreso AS ingreso', 
         'ingresos.salida AS salida', 'puertas.descripcion AS puerta', 'usuarios.nombre AS usuario', 'usuarios.documento as documento',
         'usuarios.nombre AS usuario', 'usuarios.documento as documento', 
         'usuarios.apellido as apellido', 'usuarios.telefono as telefono', 'usuarios.neoface as neoface')
         ->from('ingresos') 
              
         ->join('usuarios', 'ingresos.usuario', '=', 'usuarios.id')
         ->join('puertas', 'ingresos.puerta', '=', 'puertas.id')
         ->where('ingreso', '>=', $fecha_inicial)
         ->where('salida', '<=', $fecha_final)
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

    public function filtroxSitio($id)
    {
        $result = Ingreso::select('ingresos.ingreso AS ingreso', 
         'ingresos.salida AS salida', 'puertas.descripcion AS puerta',
         'usuarios.documento', 'usuarios.nombre', 'usuarios.apellido', 'usuarios.telefono', 'usuarios.neoface',
         'tipos_usuarios.descripcion AS tipo_usuario',
         'grupos_usuarios.nombre AS grupo',
         'areas_usuarios.descripcion AS area',
         'sitios.nombre AS sitio')
         ->from('ingresos')
         ->join('usuarios', 'ingresos.usuario', '=', 'usuarios.id')
         ->join('puertas', 'ingresos.puerta', '=', 'puertas.id')
         ->join('subsitios', 'subsitios.id', '=', 'puertas.subsitio')
         ->join('sitios', 'sitios.id', '=', 'subsitios.id')
         ->join('tipos_usuarios', 'tipos_usuarios.id', '=', 'usuarios.tipo_usuario')
         ->join('grupos_usuarios', 'grupos_usuarios.id', '=', 'usuarios.grupo')
         ->join('areas_usuarios', 'areas_usuarios.id', '=', 'usuarios.area')
         ->where('sitios.id', '=', $id)
         //->with('usuario')
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

    
    
}

