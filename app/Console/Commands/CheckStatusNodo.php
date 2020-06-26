<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Telegram\Bot\Api;

class CheckStatusNodo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:node_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comprueba el estado de el nodo actual, configurado en el env y actualiza los datos en la bd';

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
        $nodo = \App\Nodo::find(env('NODO_ACTUAL'));
        if(!is_null($nodo))
        {
            
                $process = new Process('df -h /home');
                $process->setTimeout(60);
                $process->run();       
                
                // executes after the command finishes
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }

                $resultado = $process->getOutput();  
                var_dump($resultado);
                
                $pos_porcentaje = strpos($resultado, " /");
                echo "pos porcentaje : ".$pos_porcentaje."\n";
                $aux = "";
                for($i = $pos_porcentaje; $i > 1; $i--)
                {
                    
                    if($resultado[$i] == ' ')
                    {
                        if(strlen($aux) > 0)
                        {
                            break;
                        }
                    }
                    else
                    {
                        $aux.=$resultado[$i];
                    }
                }
                
                
                $porcentaje = intval(strrev($aux));
                
                $nodo->uso_disco = $porcentaje;
                $nodo->save();
                
                if($porcentaje > 92)
                {
                    
                    $nodo->estado = 'OFF';
                    $nodo->save();
                    $telegram->sendMessage([
                    'chat_id' => env('TELEGRAM_ADMIN_GROUP'),
                    'text' => "[Aviso] - El nodo ".$nodo->id." : ".$nodo->url." ha alcanzado el 92% de uso de disco. Se va a desactivar su uso para almacenamiento de vídeos"
                    ]);                      
                    
                }
            
        }
        else
        {
            echo "El nodo no existe \n";
        }
        //
    }
}
