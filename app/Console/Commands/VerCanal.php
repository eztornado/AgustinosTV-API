<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Evento;

class VerCanal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retransmision:ver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script que rellena los stats de los canales con eventos activos';

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

            $eventos = \App\Evento::where('estado','EMITIENDO')->orderBy('id','DESC')->get();
            
            foreach($eventos as $e)
            {
                $evento_activo = Evento::find($e['id']);
                if(!is_null($evento_activo))
                {
                   $gente_online = $this->genteOnline();
                   if(intval($gente_online) > intval($evento_activo->max_online))
                   {
                       $evento_activo->max_online = intval($gente_online);
                   }
                   $evento_activo->save();
                }
                else
                {
                    //
                }                
            }
       
        //
    }
    
    private function genteOnline()
    {
       
        $contenido = file_get_contents("https://agustinos.tv:90/stats"); 

        $aux = '';
        for($i = 20;$i < strlen($contenido);$i++)
        {
            if($contenido[$i] == ' ')
            {
                break;
            }
            else
            $aux.=$contenido[$i];
        }
        $valor = intval($aux) -1;
        
        return $valor;
    }    
}
