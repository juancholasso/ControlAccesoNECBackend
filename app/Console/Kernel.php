<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Models\Permiso;
use App\Models\Usuario;
use App\Models\Notificacion;
use App\Http\Controllers\NeoFaceController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\ExampleController;
use Illuminate\Support\Facades\DB;
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version2X;
use Exception;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        //Tarea que enviar una alerta con las persosas que llevan mas de 8 horas sin salir
        $schedule->call(function () {
            date_default_timezone_set('America/Bogota');
            // JOIN entre ingresos, puertas y usuarios
            $ingresos_puertas_usuarios_sin_salida = DB::table('ingresos')
            ->join('puertas', 'ingresos.puerta', '=', 'puertas.id')
            ->join('usuarios', 'ingresos.usuario', '=', 'usuarios.id')
            ->where("ingresos.eliminado","=",0) 
            ->whereNull('salida')
            ->select('*')
            ->get();

            DB::table('notificacion')->update(['eliminado' => 1]);

            $usuarios_supera_horas_limite = array();

            foreach($ingresos_puertas_usuarios_sin_salida as $usuario){
                $fecha_ingreso = date_create(date($usuario->ingreso));
                $fecha_actual = date_create(date('Y-m-d H:i:s'));
                $diff = date_diff($fecha_actual, $fecha_ingreso);

                $horas_sin_salir = ($diff->y * 8760) + ($diff->m * 730) + ($diff->d * 24) + $diff->h;
                if($horas_sin_salir > 8){
                    //Creamos un array temporal para los datos de cada usuario
                    $usuario_temp = array();
                    $usuario_temp['horas_sin_salir'] = $horas_sin_salir;
                    $usuario_temp['id'] = $usuario->id;
                    $usuario_temp['nombre'] = $usuario->nombre;
                    $usuario_temp['apellido'] = $usuario->apellido;

                    array_push($usuarios_supera_horas_limite, $usuario_temp);

                    $data = array(
                        'horas_sin_salir' => $usuario_temp['horas_sin_salir'],
                        'Usuario'=> $usuario_temp['id'],
                        'eliminado' => 0
                    );
                    $notificacion = Notificacion::where('usuario', $usuario_temp['id']);
                    if(!$notificacion->exists()){
                        Notificacion::insert($data);
                    }
                    else{
                        $notificacion ->first() -> update($data);
                    }

                }
            }

            // Emitir al socket
            $client = new Client(new Version2X('http://localhost:8080/',));
            $client->initialize();
            $client->emit('usuarios_no_salida',["data"=>$usuarios_supera_horas_limite]);
            $client->close();
        })->everyMinute();//->hourly();//

        //Tarea que desenrrola las personas cuando no tienen ningún permiso vigente
        $schedule->call(function () {
            $objectNeoFace = new NeoFaceController;

            //Este array trae los id de los usuarios que tienen permisos
            $usuariosConPermiso = DB::table('permisos')
            ->select('usuario')
            ->groupBy('usuario')
            ->get();
            
            foreach($usuariosConPermiso as $idusuario){
                $fechaActualUnix = strtotime(date('Y-m-d H:i:s'));
                //Bandera para saber si el usuario no tiene ningún permiso vigente
                $desenrrolar = true;
                //Permisos del usuario
                $permisos = Permiso::where('usuario','=',$idusuario->usuario)->get();
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
                    $usuario = Usuario::find($idusuario->usuario);
                    $usuario->neoface = 0;
                    if($usuario->save()){
                        $objectNeoFace->ELIMINAR_USUARIO($usuario->guid);
                    }
                }
                else{
                    //Si desenrrollar es false entonces sincronizamos el usuario
                    $usuario = Usuario::find($idusuario->usuario);
                    $usuario->neoface = 1;
                    if($usuario->save()){
                        $objectNeoFace->SINCRONIZAR_USUARIO($usuario->id);
                    }                
                }
            }
        
        })->hourly();//->everyMinute();

        $schedule->call(function () {
            $Notificacion = new NotificacionController;
            
             // Emitir al socket
             $client = new Client(new Version2X('http://localhost:8080/',));
             $client->initialize();
             $client->emit('campanita',["data"=>$Notificacion->listar()]);
             $client->close();
        })->everyMinute();


        $schedule->call(function () {
            $actualizacion = new ExampleController;
            
             // Emitir al socket
            $actualizacion->integracionKactus();

        })->hourly();
    }
   
}
