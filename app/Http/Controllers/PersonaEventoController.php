<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Usuario;
use App\Models\Permiso;
use App\Models\TipoDocumento;
use App\Models\TipoUsuario;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

class PersonaEventoController extends Controller
{

    public function __construct() { }


    public function listar(){
       //buscar usuario y su ultimo ingreso
        $ultimaSalida = \DB::select('SELECT
        MAX(ing.salida),
        tu.descripcion as tipo_usuario, 
        td.descripcion as tipo_documento, 
        usu.id,
        usu.documento, 
        usu.nombre,
        usu.apellido,
        usu.telefono,
        usu.eliminado,
        usu.neoface
        
        
        FROM ingresos AS ing
        
        LEFT JOIN usuarios AS usu
        ON ing.usuario = usu.id
        LEFT JOIN tipos_documentos AS td
        ON usu.tipo_documento = td.id
        LEFT JOIN tipos_usuarios AS tu
        ON usu.tipo_usuario = tu.id
        
        WHERE ing.eliminado = 0
        
        group by usuario');
        
      
        foreach($ultimaSalida as $key =>$user){
            //ultima salida
             $salida= $user->{"MAX(ing.salida)"};
             //dia actual
             $diaActual = Carbon::now();
             $diaActual = Carbon::parse($diaActual);
             $diaActual =  $diaActual->format('Y-m-d');
             
             //diferencia de dias
             $fecha_inicial = Carbon::parse($salida);
             $diasDiferencia = $fecha_inicial->diffInDays($diaActual);
 
           
                $infoUsuario[$key] = array(
                    'usuario' => $user,
                    'salida' => $salida,
                    'dias_sin_asistir'=> $diasDiferencia
                );
 
        }
        
        return response() -> json(
            array('data' => $infoUsuario, 'message' => config('constants.messages.3.message')),
            config('constants.messages.3.code')
        );
    }  


    
      /**
     *  Eliminar un usuario en neoface
     */
   /**
     *  Eliminar un usuario en neoface
     */
    public function ELIMINAR_USUARIO($guid)
    {
        try {
 
            $curl = curl_init();
 
            curl_setopt_array($curl, array(
                CURLOPT_URL => config('constants.neofaceurl')."user/delete",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "guid=".$guid,
                //CURLOPT_POSTFIELDS => "guid=bc84ff24-d9e3-4b68-bebc-63f9a19e4e84",
 
                CURLOPT_HTTPHEADER => array(
                    "Accept: */*",
                    "Accept-Encoding: gzip, deflate",
                    "Cache-Control: no-cache",
                    "Connection: keep-alive",
                    "Content-Length: 41",
                    "Content-Type: application/x-www-form-urlencoded",
                    "Host: localhost",
                    "Postman-Token: 7d8cb53a-eb0e-4096-9367-b11d9debfd82,77b31aee-4dd5-43e5-99a2-26a472c0f277",
                    "User-Agent: PostmanRuntime/7.17.1",
                    "cache-control: no-cache"
                ),
            ));
 
            $response = curl_exec($curl);
            
            return $response;
            if(intval(curl_getinfo($curl, CURLINFO_HTTP_CODE)) == 200)
            {
                // Enrolamiento exitoso
                return true;
            }
            return false;
 
        } catch (\Exception $e) {
            // Problemas internos
            return false;
        }
    }        
    }


    


   

    
