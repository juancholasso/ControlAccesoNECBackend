<?php
namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;


use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Usuario;
use App\Models\Sitio;
use App\Models\Neoface;

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

        $guid = $usuario['guid'];
        $guidgrupo = $usuario['grupo']['guid'];

    
        $sitio = Sitio::with('neoface')->where('id', '1')->first()->toArray();
        $neoface = $sitio['neoface'];
        $ip = $sitio['neoface']['ip'];
        $port = $sitio['neoface']['puerto'];
        $user = $sitio['neoface']['usuario'];
        $pass = $sitio['neoface']['clave'];
        
        //Prueba de url;
       /*  $neofaceurl = 'http://'.$ip.':'.$port.'/'.$guid.'/'.$user.'/'.$pass;
        return $neofaceurl; */

        $sync = new NeoFaceController();

         // Si la sincronización fue exitosa se actualiza el estado en neoface
        
        $consulta = $sync -> CONSULTAR_USUARIO($guid, $ip, $port, $user, $pass);
        if($consulta == true)
        {
            $edicion = $sync->EDITAR_USUARIO($usuario);

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
                return response() -> json(
                    array('data' => [], 'message' => config('constants.messages.2.message')),
                    config('constants.messages.2.code')
                );
            } 
        }else{
           
            $agregado = $sync -> AGREGAR_USUARIO($usuario);
            if($agregado == true)
            {
                $data = array('neoface'  =>  1);
                Usuario::where('id', $id)->update($data);
                return response() -> json(
                    array('data' => [], 'message' => config('constants.messages.5.message')),
                    config('constants.messages.5.code')
                );
            }else{
                return response() -> json(
                    array('data' => [], 'message' => config('constants.messages.2.message')),
                    config('constants.messages.2.code')
                );
            }   
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
    public function AGREGAR_USUARIO($usuario)
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
                return false;
            }
        } catch (\Exception $e) {
            // Problemas internos
            return false;
        }
    }

    /**
     *  Editar informacion de usuario en neoface
     */
    public function EDITAR_USUARIO($usuario)
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
                    'Title' => ''
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
        try {
            $sitio = Sitio::with('neoface')->where('id', '1')->first()->toArray();
            $neoface = $sitio['neoface'];
            $ip = $sitio['neoface']['ip'];
            $port = $sitio['neoface']['puerto'];
            $user = $sitio['neoface']['usuario'];
            $pass = $sitio['neoface']['clave'];

            $curl = curl_init();
            $neofaceurl = config('constants.neofaceurl'). 'user/delete';

            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => $neofaceurl,
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => "",
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 30,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => "POST",
            //     CURLOPT_POSTFIELDS => ,
            //     //CURLOPT_POSTFIELDS => "guid=bc84ff24-d9e3-4b68-bebc-63f9a19e4e84",

            //     CURLOPT_HTTPHEADER => array(
            //         "Accept: */*",
            //         "Accept-Encoding: gzip, deflate",
            //         "Cache-Control: no-cache",
            //         "Connection: keep-alive",
            //         "Content-Length: 41",
            //         "Content-Type: application/x-www-form-urlencoded",
            //         "Host: localhost",
            //         "Postman-Token: 7d8cb53a-eb0e-4096-9367-b11d9debfd82,77b31aee-4dd5-43e5-99a2-26a472c0f277",
            //         "User-Agent: PostmanRuntime/7.17.1",
            //         "cache-control: no-cache"
            //     ),
            // ));

            curl_setopt_array($curl, array(
                CURLOPT_URL => $neofaceurl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "ip=".$ip."&port=".$port."&user=".$user."&pass=".$pass."&guid=".$guid,
                CURLOPT_HTTPHEADER => array(
                  "Accept: */*",
                  "Accept-Encoding: gzip, deflate",
                  "Cache-Control: no-cache",
                  "Connection: keep-alive",
                  "Content-Length: 92",
                  "Content-Type: application/x-www-form-urlencoded",
                  "Host: localhost",
                  "Postman-Token: b54e102a-9333-4932-9ee1-15bae3b61ce9,1c09c6a2-ce1d-4fb9-97f9-1af09b927a66",
                  "User-Agent: PostmanRuntime/7.17.1",
                  "cache-control: no-cache"
                ),
              ));

            $response = curl_exec($curl);

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
                ]
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