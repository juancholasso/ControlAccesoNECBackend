<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Puerta;
use App\Models\Subsitio;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //Integración Kactus
    //Separa los registros de ingreso por entradas y salidas.
    public function integracionKactus(){
        $ingresos = Ingreso::where('kactus','=',0)->get();
        $ingresosSeparadosEntradaSalida = array();
        foreach($ingresos as $ingreso){
            $puertaEntrada = Puerta::find($ingreso->puerta);
            $puertaSalida = Puerta::find($ingreso->puerta_salida);

            $entrada = [
                'pSmCodEmpr' => '689',
                'pFlCodEmpl' => $ingreso->usuario, //Id identificación
                'pStNroCont' => $ingreso->id,
                'pStFecRelo' => date('d/m/Y',strtotime($ingreso->ingreso)),
                'pStHorRelo' => date('H:i',strtotime($ingreso->ingreso)),
                'pStCodRelo' => '1',
                'pStCodCrel' => null,
                'pStTipMovi' => 'E',
                'pSmCodCcos' => '0',
                'pStCodCetr' => null,
            ];
            $salida = [
                'pSmCodEmpr' => '689',
                'pFlCodEmpl' => $ingreso->usuario,
                'pStNroCont' => $ingreso->id,
                'pStFecRelo' => date('d/m/Y',strtotime($ingreso->salida)),
                'pStHorRelo' => date('H:i',strtotime($ingreso->salida)),
                'pStCodRelo' => '1',
                'pStCodCrel' => null,
                'pStTipMovi' => 'S',
                'pSmCodCcos' => '0',
                'pStCodCetr' => null,
            ];
            array_push($ingresosSeparadosEntradaSalida, $entrada);
            array_push($ingresosSeparadosEntradaSalida, $salida);
        }
        $arraySerializado = serialize($ingresosSeparadosEntradaSalida);
        $hash = md5($arraySerializado);

        //Autenticación - Token
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_PORT => "88",
            CURLOPT_URL => "http://kactus1.digitalwaresaas.com.co:88/KWsIctie/KWsIctie.svc/Autenticacion",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\t\"pStUser\": \"SEGOVIA\",\n\t\"pStPass\": \"S3g0v14\"\n}",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Content-Length: 48",
                "Content-Type: application/json",
            ),
        ));
        $responseAuth = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        //Error en la autenticación
        if ($err) {
            echo "cURL Error #:" . $err;
        } 
        else {
            $token = json_decode($responseAuth);

            //Envio de datos
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_PORT => "88",
                CURLOPT_URL => "http://kactus1.digitalwaresaas.com.co:88/KWsIctie/KWsIctie.svc/InsertaMarcacion",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($ingresosSeparadosEntradaSalida),
                CURLOPT_HTTPHEADER => array(
                    "Accept: */*",
                    "Authorization: ".$token->pStToken,
                    "Cache-Control: no-cache",
                    "Content-Type: application/json",
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
            if ($err) {
                echo "cURL Error #:" . $err;
            } 
            else {
                return response() -> json(
                    array('data' => json_decode($response), 'hash' => $hash,'message' => config('constants.messages.3.message')),
                    config('constants.messages.3.code')
                );
            }
        }
    }
}
