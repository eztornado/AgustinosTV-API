<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MailChimpService;

class EliminarCuentasCorreoFalso extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:delete_fake';

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
    public function __construct(MailChimpService $mailChipService)
    {
        parent::__construct();
        $this->mailChimpService = $mailChipService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id_lista_princpal = 'a5c228f4d0';

        $usuarios = \App\User::where('id','>=',1)->where('active',1)->get();
        
        foreach($usuarios as $u)
        {
            echo "==============================================\n";
            echo ">> Analizando usuario : ".$u['email']." ... \n ";
    

            echo ">> Comprobando si el email existe ... \n";
            $emailChecker = new \Tintnaingwin\EmailCheckerPHP\EmailChecker();
            $existe =  $emailChecker->check($u['email']);
            echo ">> Existe el email : ".intval($existe)."\n";
            
            if (strpos($u['email'], 'icloud.com') !== false)
            {
                $existe = true;
            }            

            if($existe == false)
            {
                echo ">> Desactivando esta cuenta ... \n";
                $user = \App\User::find($u['id']);
                $user->active = 0;
                $user->save();
                

            }
                 
        }            
        //
    }
}
