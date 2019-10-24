<?php
namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;


use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Usuario;
use App\Models\Sitio;
use App\Models\Subsitio;
use App\Models\Neoface;
use App\Models\Permiso;
use App\Models\PermisosSubsitio;

class NeoFaceController extends Controller
{

    /**
     *  Sinscronizar usuario en neoface
     */
    public static function SINCRONIZAR_USUARIO($id)
    {

        // Consultar guid y guidgrupo de usuario
        $usuario = Usuario::where('id', $id)->with('grupo')->first();
        if(isset($usuario))
            $usuario = $usuario->toArray();

        $idUsuario= $usuario['id']; 
        $guid = $usuario['guid'];

        $result = PermisosSubsitio::select('permiso', 'permisos.usuario AS idUsuario', 'usuarios.nombre AS nombre',
                'subsitios.sitio AS sitio', 'sitios.neoface AS neoface',
                'neofaces.ip AS ip', 'neofaces.puerto AS puerto' , 'neofaces.usuario as usuario', 'neofaces.clave as clave'
            )
                ->from('permisos_subsitio')
                ->join('permisos', 'permisos_subsitio.permiso', '=', 'permisos.id')
                ->join('usuarios', 'permisos.usuario', '=', 'usuarios.id')
                ->join('subsitios', 'permisos_subsitio.subsitio', '=', 'subsitios.id')
                ->join('sitios', 'subsitios.sitio', '=', 'sitios.id')
                ->join('neofaces', 'sitios.neoface', '=', 'neofaces.id')
                ->where('usuarios.id', '=', $id)
                ->get()
                ->toArray();

        foreach ($result as $permiso ) {
            $ip = $permiso['ip'];
            $port=$permiso['puerto'];
            $user=$permiso['usuario'];
            $pass=$permiso['clave'];
   
          
            $sync = new NeoFaceController();
      
            // Si la sincronización fue exitosa se actualiza el estado en neoface
        
            $consulta = $sync -> CONSULTAR_USUARIO($guid, $ip, $port, $user, $pass);
            
            if($consulta == true)
            { 
            
                 $edicion = $sync->EDITAR_USUARIO($usuario, $ip, $port, $user, $pass);
                
                // Si la edición del usuario fue exitosa
                if($edicion == true)
                {
                    // Se procede a actualizar la foto
                    // Si la actualización de la foto fue exitosa
                    $foto = $sync -> ACTUALIZAR_FOTO($usuario);
                    if($foto == true)
                    {
                        return response() -> json(
                            array('data' => [], 'message' => config('constants.messages.12.message')),
                            config('constants.messages.12.code')
                        );
                    }else{
                        return response() -> json(
                            array('data' => [], 'message' => config('constants.messages.14.message')),
                            config('constants.messages.14.code')
                        );
                    }
                }else{
                    
                    $data = array('neoface'  =>  0);
                    Usuario::where('id', $id)->update($data);
                    return response() -> json(
                        array('data' => [], 'message' => config('constants.messages.2.message')),
                        config('constants.messages.2.code')
                    ); 
                } 
            }else{
                
                $agregado = $sync -> AGREGAR_USUARIO($usuario, $ip, $port, $user, $pass);
                $agregadoTodos = 0;
                if($agregado == true)
                {
                    $agregadoTodos++;
                }
            }    
        } 

         if($agregadoTodos == true)
        {
        
            $data = array('neoface'  =>  1);
            Usuario::where('id', $id)->update($data);
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.5.message')),
                config('constants.messages.5.code')
            );
        }else{
        

            $data = array('neoface'  =>  0);
            Usuario::where('id', $id)->update($data);
            return response() -> json(
                array('data' => [], 'message' => config('constants.messages.2.message')),
                config('constants.messages.2.code')
            );
        }
    }


    /**
     *  Consultar si existe un usuario en neoface
     */
    public function CONSULTAR_USUARIO($guid, $ip, $port, $user, $pass)
    {
        
        try {
            // Consultar información désde neoface
            $client = new Client();
            $neofaceurl = config('constants.neofaceurl'). 'user/find/' . $guid;
            $curl = curl_init();
           
            curl_setopt_array($curl, array(
                CURLOPT_URL => $neofaceurl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "ip=".$ip."&port=".$port."&user=".$user."&pass=".$pass,
                CURLOPT_HTTPHEADER => array(
                  "Accept: */*",
                    "Accept-Encoding: gzip, deflate",
                    "Cache-Control: no-cache",
                    "Connection: keep-alive",
                    "Content-Length: 50",
                    "Content-Type: application/x-www-form-urlencoded",
                    "Host: localhost",
                    "Postman-Token: 0e65ae57-5c43-4f1a-bb09-4ad2eefcce67,c9cdb45f-8ac3-4d80-8fe9-b5884adcf7e2",
                    "User-Agent: PostmanRuntime/7.17.1",
                    "cache-control: no-cache",
                ),
            ));
           
            
            $response = curl_exec($curl);
            
         
            // $statuscode =  curl_getinfo($curl, CURLINFO_HTTP_CODE);
            // echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
            // exit();
            if(curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200)
                return true;
            else  
                return 0;

        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     *  Agregar un usuario en neoface
     */
    public function AGREGAR_USUARIO($usuario, $ip, $port, $user, $pass)
    {

        try {
            // Enviar informacion a NEOFACE
            $client = new Client();
            $imgurl = config('constants.profilepicurl') . $usuario['foto'];
            
            $neofaceurl = config('constants.neofaceurl') . 'user/enrol';
            
            
            $request = $client->post($neofaceurl, [
                'multipart' => [
                    [
                        'name' => 'Photo',
                        'contents' => fopen($imgurl, 'r')
                    ]
                ],
                'query' => [
                    'Guid' => $usuario['guid'],
                    'WatchlistId' => $usuario['grupo']['guid'],
                    'FirstName' => $usuario['nombre'],
                    'LastName' => $usuario['apellido'],
                    'MiddleName' => '',
                    'Notes' => '',
                    'Title' => '',
                    'ip' => $ip,
                    'port' => $port,
                    'user' => $user,
                    'pass' => $pass
                ]
            ]);
              
           
            // Obtener respuesta
            $response = $request->getBody();
            $statusCode = $request->getStatusCode();
            
            if($statusCode == 201)
            {
                // Enrolamiento exitoso
                
                return true;
            }else{
                // Enrolamiento fállido
                return 0;
            }
        } catch (\Exception $e) {
            // Problemas internos
            return 0;
        }
    }

    /**
     *  Editar informacion de usuario en neoface
     */
    public function EDITAR_USUARIO($usuario, $ip, $port, $user, $pass)
    {
        try {
            // Enviar informacion a NEOFACE
            $client = new Client();
            $neofaceurl = config('constants.neofaceurl') . 'user/update';
            $request = $client->put($neofaceurl, [
                'query' => [
                    'Guid' => $usuario['guid'],
                    'WatchlistId' => $usuario['grupo']['guid'],
                    'FirstName' => $usuario['nombre'],
                    'LastName' => $usuario['apellido'],
                    'MiddleName' => '',
                    'Notes' => '',
                    'Title' => '',
                    'ip' => $ip,
                    'port' => $port,
                    'user' => $user,
                    'pass' => $pass
                ]
            ]);

            // Obtener respuesta
            $response = $request->getBody();    
            $statusCode = $request->getStatusCode();

            if($statusCode == 200)
            {
                return true;
            }else{
                // Enrolamiento fállido
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
/**
     *  Eliminar un usuario en neoface
     */
    public function ELIMINAR_USUARIO($guid)
    {
        $usuario = Usuario::where('guid', $guid)->with('grupo')->first();
        $id= $usuario['id'];

        $result = PermisosSubsitio::select('permiso', 'permisos.usuario AS idUsuario', 'usuarios.nombre AS nombre',
        'subsitios.sitio AS sitio', 'sitios.neoface AS neoface',
        'neofaces.ip AS ip', 'neofaces.puerto AS puerto' , 'neofaces.usuario as usuario', 'neofaces.clave as clave'
        )
        ->from('permisos_subsitio')
        ->join('permisos', 'permisos_subsitio.permiso', '=', 'permisos.id')
        ->join('usuarios', 'permisos.usuario', '=', 'usuarios.id')
        ->join('subsitios', 'permisos_subsitio.subsitio', '=', 'subsitios.id')
        ->join('sitios', 'subsitios.sitio', '=', 'sitios.id')
        ->join('neofaces', 'sitios.neoface', '=', 'neofaces.id')
        ->where('usuarios.id', '=', $id)
        ->get()
        ->toArray();

            foreach ($result as $permiso ) {
                $ip = $permiso['ip'];
                $port=$permiso['puerto'];
                $user=$permiso['usuario'];
                $pass=$permiso['clave'];

                $sync = new NeoFaceController();

                $eliminado = $sync -> ELIMINAR_USUARIO_NEOFACES($usuario, $ip, $port, $user, $pass);
                
                print_r($eliminado);
                
                /* if($eliminado == 1)
                {  
                    $data = array('neoface'  =>  0);
                    Usuario::where('id', $id)->update($data);
                    return response() -> json(
                        array('data' => [], 'message' => config('constants.messages.5.message')),
                        config('constants.messages.5.code')
                    );
                }else{
                    return false;
                } */
                   // $curl = curl_init();
                    /* curl_setopt_array($curl, array(
                        CURLOPT_URL => $neofaceurl,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => "ip=".$ip."&port=".$port."&user=".$user."&pass=".$pass."&guid=".$guid,
                        CURLOPT_HTTPHEADER => array( */
                       //  "Accept: */*",
                       /* "Accept-Encoding: gzip, deflate",
                        "Cache-Control: no-cache",
                        "Connection: keep-alive",
                        "Content-Length: 92",
                        "Content-Type: application/x-www-form-urlencoded",
                        "Host: localhost",
                        "Postman-Token: b54e102a-9333-4932-9ee1-15bae3b61ce9,1c09c6a2-ce1d-4fb9-97f9-1af09b927a66",
                        "User-Agent: PostmanRuntime/7.17.1",
                        "cache-control: no-cache"
                        ),
                    )); */
                    
                }
    }
    

    public function ELIMINAR_USUARIO_NEOFACES($usuario, $ip, $port, $user, $pass)
    {
        try{
                $client = new Client();
                $neofaceurl = config('constants.neofaceurl'). 'user/delete';
                $request = $client->post($neofaceurl, [
                        'query' => [
                            'guid' => $usuario['guid'],
                            'ip' => $ip,
                            'port' => $port,
                            'user' => $user,
                            'pass' => $pass
                        ]
                    ]);

                    $response = $request->getBody();    
                    $statusCode = $request->getStatusCode();
        
                    if($statusCode == 200)
                    {
                        return true;
                    }else{
                        // Enrolamiento fállido
                        return false;
                    }
            } catch (\Exception $e) {
                    return false;
            }

    }
    

    public static function ACTUALIZAR_FOTO($usuario)
    {
        try {
            // Enviar informacion a NEOFACE
            $client = new Client();
            $imgurl = config('constants.profilepicurl') . $usuario['foto'];
            $neofaceurl = config('constants.neofaceurl') . 'user/updatephoto';
            $request = $client->post($neofaceurl, [
                'multipart' => [
                    [
                        'name' => 'Photo',
                        'contents' => fopen($imgurl, 'r')
                    ]
                ],
                'query' => [
                    'Guid' => $usuario['guid'],
                    'ip' => $ip,
                    'port' => $port,
                    'user' => $user,
                    'pass' => $pass
                ],
                
            ]);

            // Obtener respuesta
            $response = $request->getBody();
            $statusCode = $request->getStatusCode();

            if($statusCode == 201)
            {
                // Foto actualizada exitosamente 
                return true;
            }else{
                //Foto no actualizada
                return false;
            }
        } catch (\Exception $e) {
            // Problemas internos
            return false;
        }
    }

    public function listar ()
    {   
        $result = Neoface::where('eliminado', 0)->get();
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


    public static function TRAER_IMAGEN_MATCH($idmatch){
        
        try{

            $usuario = Usuario::where('guid', $guid)->with('grupo')->first();
            $id= $usuario['id'];

            $result = PermisosSubsitio::select('permiso', 'permisos.usuario AS idUsuario', 'usuarios.nombre AS nombre',
            'subsitios.sitio AS sitio', 'sitios.neoface AS neoface',
            'neofaces.ip AS ip', 'neofaces.puerto AS puerto' , 'neofaces.usuario as usuario', 'neofaces.clave as clave'
            )
            ->from('permisos_subsitio')
            ->join('permisos', 'permisos_subsitio.permiso', '=', 'permisos.id')
            ->join('usuarios', 'permisos.usuario', '=', 'usuarios.id')
            ->join('subsitios', 'permisos_subsitio.subsitio', '=', 'subsitios.id')
            ->join('sitios', 'subsitios.sitio', '=', 'sitios.id')
            ->join('neofaces', 'sitios.neoface', '=', 'neofaces.id')
            ->where('usuarios.id', '=', $id)
            ->get()
            ->toArray();

            $sitio = Sitio::with('neoface')->where('id', '1')->first()->toArray();
            $neoface = $sitio['neoface'];
            $ip = $sitio['neoface']['ip'];
            $port = $sitio['neoface']['puerto'];
            $user = $sitio['neoface']['usuario'];
            $pass = $sitio['neoface']['clave'];
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => config('constants.neofaceurl')."match/getimage",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "ip=".$ip."&port=".$port."&user=".$user."&pass=".$pass."&idmatch=".$idmatch,
              CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Content-Type: application/x-www-form-urlencoded",
                "Postman-Token: 1143d18b-d182-47bf-9e5c-cb09fd225856,2290f9b4-6c4f-49fe-aafd-17a57fa91874",
                "cache-control: no-cache"
              ),
            ));
            
            $response = curl_exec($curl);
            return json_decode($response);
        }
        catch(\Exception $e){
            return false;
        }
    }


    public function GETAUTHTOKEN(){
        try{
            $sitio = Sitio::with('neoface')->where('id', '1')->first()->toArray();
            $neoface = $sitio['neoface'];
            $ip = $sitio['neoface']['ip'];
            $port = $sitio['neoface']['puerto'];
            $user = $sitio['neoface']['usuario'];
            $pass = $sitio['neoface']['clave'];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => config('constants.neofaceurl')."user/getauthtoken",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "ip=".$ip."&port=".$port."&user=".$user."&pass=".$pass,
                CURLOPT_HTTPHEADER => array(
                  "Accept: */*",
                  "Accept-Encoding: gzip, deflate",
                  "Content-Type: application/x-www-form-urlencoded",
                  "cache-control: no-cache"
                ),
            ));
            
            $response = json_decode(curl_exec($curl));
            return response() -> json(
                array('data' => $response, )
            );
        }
        catch(\Exception $e){

        }
    }

}