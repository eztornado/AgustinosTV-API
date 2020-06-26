<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Api;
use Illuminate\Support\Carbon;

class CheckFinalEvento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gestion_eventos:check_final';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comprueba si un evento ha alcanzado la fecha/hora de finalización. Si hay una emisión en activo no se cierra el evento, si no se cierra';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function ListarFicheros($evento)
    {
        //Esperamos 10 segundos por si el fichero se está almacenando en HDD
        sleep(10);        
        if(!is_null($evento))
        {
            $inicio = Carbon::parse($evento->inicio);
            $final = Carbon::parse(date("Y-m-d H:i:s"));
            
            $nodo = \App\Nodo::where('id',env('NODO_ACTUAL'))->first();
            
            $archivos =  scandir($nodo->ruta_grabaciones);

            foreach($archivos as $archivo)
            {
                if($archivo != '.' && $archivo != '..')
                {
                    $nombre_fichero = $archivo;
                    $fecha_fichero =  Carbon::parse(date("Y-m-d H:i:s", filemtime($nodo->ruta_grabaciones.$nombre_fichero)));
                    
                    //EL FICHERO HA SIDO CREADO MIENTRAS EL EVENTO ESTABA ACTIVO
                    if($fecha_fichero >= $inicio && $fecha_fichero <= $final)
                    {
                        \App\Grabaciones::create([
                            'nombre_fichero' => $nombre_fichero,
                            'nodo_id' => $nodo->id,
                            'evento_id' => $evento->id,
                            'canal_id' => $evento->canal_id,
                        ]);
                    }
                }
                
                
            }
        }
        

        
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       
        $eventos = DB::select("SELECT * FROM eventos
            WHERE YEAR(final) = YEAR(CURRENT_TIMESTAMP())
            AND MONTH(final) = MONTH(CURRENT_TIMESTAMP())
            AND DAY(final) = DAY(CURRENT_TIMESTAMP())
            AND HOUR(final) = HOUR(CURRENT_TIMESTAMP())
            AND MINUTE(final) = MINUTE(CURRENT_TIMESTAMP())
            AND estado = 'EMITIENDO'");
        
        foreach($eventos as $e)
        {
            $retrasmisiones = \App\Retransmision::where('evento_id',$e->id)->where('estado','INICIADA')->get();
            if(sizeof($retrasmisiones) > 0)
            {
                //No Finalizar evento, la retransmisión sigue abierta y se cerrará evento  cuando se finalice retransmisión
            }
            else
            {
                //Cerrar Evento
                $ev = \App\Evento::find($e->id);
                $this->ListarFicheros($ev);
                $ev->estado = 'FINALIZADO';
                $ev->save();
                
                $canal = \App\Canal::find($ev->canal_id);
                $canal->estado = 'OFFLINE';
                $canal->save();
                
                $telegram = new Api(env('TELEGRAM_TOKEN'));
                $telegram->sendMessage([
                'chat_id' => env('TELEGRAM_ADMIN_GROUP'),
                'text' => "[Final Evento] : ".$ev->title.". Fecha/hora final alcanzada sin emisión activa"
                ]);     

            }
            
        }
                
        
        //
    }
}
