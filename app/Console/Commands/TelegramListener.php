<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;
use GuzzleHttp\Client;

class TelegramListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //
        //echo "Esto es un bot \n";
        $telegram = new Api(env('TELEGRAM_TOKEN'));
        $response = $telegram->getMe();
        //echo " id : ".$response->getId()."</br>";

        $update = $telegram->commandsHandler(false, ['timeout' => 30]);
        
        $usuario_telegram = "";
        $isbot = 0;
        $first_name = "";
        $comando = "";
        
        print_r($update);   
        
        foreach($update as $u)
        {
            foreach($u as $mensaje)
            {
                $mensaje_id = $mensaje['message_id'];
                $usuario_telegram = $mensaje['chat']['id'];
               // $isbot = $from['is_bot'];
               // $first_name = $from['first_name'];
                $comando = $mensaje['text'];
            }
            
        }              
    }
}
