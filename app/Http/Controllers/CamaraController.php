<?php
namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;


use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Usuario;
use App\Models\Subsitio;
use App\Models\Neoface;


use App\Models\Puerta;
use App\Models\PermisosSubsitio;
class CamaraController extends Controller
{

public function listar()
    {
        $ip= '172.20.96.233';
        $port= '8790';
        $user= 'system';
        $pass= 'system';

        try {
            // Consultar información désde neoface
            $client = new Client();
            $neofaceurl = config('constants.neofaceurl'). 'camera/all';
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
           
            
            $response = json_decode(curl_exec($curl));
            return response() -> json(
                array('data' => $response, )
            );

        } catch (\Throwable $th) {
            return false;
        }
    }
}