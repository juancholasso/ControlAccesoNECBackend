<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Puerta;
use App\Models\Usuario;
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
        //Autenticacion ante Kactus
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_PORT => config('constants.kactus.urlAuthPort')."",
            CURLOPT_URL => config('constants.kactus.urlAuth')."",
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
            echo "cURL Error #1:" . $err;
        } 
        else {
            //Token de autenticacion
            $token = json_decode($responseAuth);

            //Separamos cada ingreso en entrada y salida
            $ingresos = Ingreso::with('usuario')
            ->where('kactus','=',0)
            ->where('eliminado','0')
            ->where('salida','!=', null)
            ->where('puerta_salida','!=', "")
            ->get();
            $resultadoSincronizacion = array();

            foreach($ingresos as $ingreso){
                $puertaEntrada = Puerta::find($ingreso->puerta);
                $puertaSalida = Puerta::find($ingreso->puerta_salida);
                $usuarioObject = Usuario::find($ingreso->usuario);
                $entrada = [
                    'pSmCodEmpr' => '689',
                    'pFlCodEmpl' => $usuarioObject->documento,
                    'pStNroCont' => null,
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
                    'pFlCodEmpl' => $usuarioObject->documento,
                    'pStNroCont' => null,
                    'pStFecRelo' => date('d/m/Y',strtotime($ingreso->salida)),
                    'pStHorRelo' => date('H:i',strtotime($ingreso->salida)),
                    'pStCodRelo' => '1',
                    'pStCodCrel' => null,
                    'pStTipMovi' => 'S',
                    'pSmCodCcos' => '0',
                    'pStCodCetr' => null,
                ];

                $arrayBodyMarcacion = array();
                array_push($arrayBodyMarcacion, $entrada);
                array_push($arrayBodyMarcacion, $salida);

                //  Envio de datos entrada
                $curlMarcacion = curl_init();
                curl_setopt_array($curlMarcacion, array(
                    CURLOPT_PORT => config('constants.kactus.urlSyncPort')."",
                    CURLOPT_URL => config('constants.kactus.urlSync')."",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($arrayBodyMarcacion),
                    CURLOPT_HTTPHEADER => array(
                        "Accept: */*",
                        "Authorization: ".$token->pStToken,
                        "Cache-Control: no-cache",
                        "Content-Type: application/json",
                    ),
                ));

                $responseMarcacion = curl_exec($curlMarcacion);
                $errMarcacion = curl_error($curlMarcacion);
                curl_close($curlMarcacion);
                
                if ($errMarcacion) {
                    $arrayResMarcacion = array();
                    $arrayResMarcacion['idingreso'] = $ingreso->id;
                    $arrayResMarcacion['estadoSync'] = 0;
                    $arrayResMarcacion['resultado'] = $errMarcacion;
                    array_push($resultadoSincronizacion, $arrayResMarcacion);
                }
                else{
                    $responseMarcacion = json_decode($responseMarcacion);
                    if(isset($responseMarcacion[0]) && isset($responseMarcacion[1])){
                        if($responseMarcacion[0]->pInCodigo == 0 &&  $responseMarcacion[1]->pInCodigo == 0){
                            $ingreso->kactus = 1;
                            $ingreso->save();
                            $arrayResMarcacion = array();
                            $arrayResMarcacion['idingreso'] = $ingreso->id;
                            $arrayResMarcacion['estadoSync'] = 1;
                            $arrayResMarcacion['resultado'] = $responseMarcacion;
                            array_push($resultadoSincronizacion, $arrayResMarcacion);
                        }
                        else if($responseMarcacion[0]->pInCodigo == 4 &&  $responseMarcacion[1]->pInCodigo == 4){
                            $ingreso->kactus = 1;
                            $ingreso->save();
                            $arrayResMarcacion = array();
                            $arrayResMarcacion['idingreso'] = $ingreso->id;
                            $arrayResMarcacion['estadoSync'] = 1;
                            $arrayResMarcacion['resultado'] = $responseMarcacion;
                            array_push($resultadoSincronizacion, $arrayResMarcacion);
                        }
                        else{
                            $ingreso->kactus = 2;
                            $ingreso->save();
                            $arrayResMarcacion = array();
                            $arrayResMarcacion['idingreso'] = $ingreso->id;
                            $arrayResMarcacion['estadoSync'] = 0;
                            $arrayResMarcacion['resultado'] = $responseMarcacion;
                            array_push($resultadoSincronizacion, $arrayResMarcacion);
                        }
                    }
                }
                $arraySerializado = serialize($resultadoSincronizacion);
                $hash = md5($arraySerializado);
                
            }

            $arraySerializado = serialize($resultadoSincronizacion);
            $hash = md5($arraySerializado);

            return response() -> json(
                array('data' => $resultadoSincronizacion, 'hash' => $hash,'message' => config('constants.messages.3.message')),
                config('constants.messages.3.code')
            );
            
        }
    }
}
