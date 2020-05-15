<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Api;

class FinalizarRetransmision extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retransmision:finalizar {canal}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finaliza la retransmisión en un evento, si este evento HA FINALIZADO si no, hasta que no finalice no se finaliza';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    
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
    
    public function handle()
    {
        $canal_id = $this->argument('canal');
        if(isset($canal_id))
        {
            $canal = \App\Canal::find($canal_id);
            if(!is_null($canal))
            {
                $evento = DB::select("SELECT id from eventos where deleted_at is null and final <= CURRENT_TIMESTAMP() and canal_id = ".$canal->id." and estado = 'EMITIENDO' ORDER BY id DESC limit 1");
                
                foreach($evento as $e)
                {
                    $hay_evento = true;
                    //Marcar el canal como offline
                    $canal->estado = 'OFFLINE';
                    $canal->save();                
                    
                    //Marcar el evento como finalizado
                    $ev = \App\Evento::find($e->id);
                    $this->ListarFicheros($ev);
                    $ev->estado = 'FINALIZADO';
                    $ev->save();
                    
                    $telegram = new Api(env('TELEGRAM_TOKEN'));
                    $telegram->sendMessage([
                    'chat_id' => env('TELEGRAM_ADMIN_GROUP'),
                    'text' => "[Final Evento] : ".$ev->title.". Evento fuera de hora con emisión aún activa finalizado"
                    ]);     
                    
                 
                                 
                }
                
                    //Finalizar retransmisión (siempre que se llame a esta función
                    $retransmision = \App\Retransmision::where('canal_id',$canal_id)->orderBy('id','DESC')->first();
                    $retransmision->estado = 'FINALIZADA';
                    $retransmision->save();
                    
                    if($retransmision->evento_id != null)
                    {
                        $ev = \App\Evento::find($retransmision->evento_id);
                        $telegram = new Api(env('TELEGRAM_TOKEN'));
                        $telegram->sendMessage([
                        'chat_id' => env('TELEGRAM_ADMIN_GROUP'),
                        'text' => "[Retramsisión finalizada] : La emisión de el canal ".$canal_id." de el evento ".$ev->title." ha finalizado"
                        ]);                             

                                              
                    }
                    else
                    {
                        $telegram = new Api(env('TELEGRAM_TOKEN'));
                        $telegram->sendMessage([
                        'chat_id' => env('TELEGRAM_ADMIN_GROUP'),
                        'text' => "[Retramsisión finalizada] : La emisión de el canal ".$canal_id." SIN EVENTO PROGRAMADO ha finalizado"
                        ]);                           
                        
                    }
                    
                    
                    
                    
                       
                    
            }
            
        }   
        else
        {
            echo "Canal no encontrado \n";
            return;
        }
        //
    }
}
