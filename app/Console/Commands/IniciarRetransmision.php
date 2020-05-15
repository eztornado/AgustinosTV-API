<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Api;

class IniciarRetransmision extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retransmision:iniciar {canal}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia una retransmisión en el canal seleccionado';

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
    public function handle()
    {
        
        $canal_id = $this->argument('canal');
        if(isset($canal_id))
        {
            $canal = \App\Canal::find($canal_id);
            if(!is_null($canal))
            {
                
                //Iniciar Retransmisión
                $r = \App\Retransmision::create([
                    'delegacion_id' => $canal->delegacion_id,
                    'canal_id' => $canal->id
                ]);
                
                //Recoger el ID del evento que acaba de comenzar            
                $evento = DB::select("SELECT id,canal_id,title from eventos where deleted_at is null and inicio <= CURRENT_TIMESTAMP() and estado = 'EMITIENDO' ORDER BY id DESC limit 1");
                $id_evento = null;
                $id_canal = null;
                $title_evento = null;
                foreach($evento as $e)
                {
                    $id_evento = $e->id;
                    $id_canal = $e->canal_id;
                    $title_evento = $e->title;
                }
                
                //Establcecer el evento de la retransmisión actual
                $retransmision = \App\Retransmision::find($r->id);
                $retransmision->evento_id = $id_evento;
                $retransmision->save();
                
                //Indicar que el canal está emitiendo
                $canal = \App\Canal::find($id_canal);
                if(!is_null($canal))
                {
                    
                    $canal->estado = 'ONLINE';
                    $canal->save();
                }
                
                $ev = null;
                if($id_evento != null)
                {
                    $ev = \App\Evento::find($id_evento);
                }
                
                if(!is_null($ev))
                {
                    $telegram = new Api(env('TELEGRAM_TOKEN'));
                    $telegram->sendMessage([
                    'chat_id' => env('TELEGRAM_ADMIN_GROUP'),
                    'text' => "[Retransmisión detectada] : La emisión de el canal ".$id_canal." entrante corresponde al evento ".$ev->title
                    ]);   
                }
                else 
                {
                    $telegram = new Api(env('TELEGRAM_TOKEN'));
                    $telegram->sendMessage([
                    'chat_id' => env('TELEGRAM_ADMIN_GROUP'),
                    'text' => "[Retransmisión detectada] : Detectada emisión en el canal ".$id_canal." SIN EVENTO PROGRAMADO"
                    ]);                     
                    
                }
                
                
                
            }
            else
            {
                echo "Canal no encontrado\n";
                return;
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
