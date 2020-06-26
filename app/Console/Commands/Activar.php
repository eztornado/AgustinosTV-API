<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class Activar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activar:aviso';

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

        
        $u = User::where('email','agarciaalcazar20799@gmail.com')->first();
        $u->notify(new \App\Notifications\ActivarCuentasNotification());
      /*  $users = User::where('id','>=',3)->where('active',1)->orderBy('id','ASC')->get();
        foreach($users as $u)
        {
            $u = User::find($u['id']);
            try{
                echo "Enviando id : ".$u['id']."\n";
                $u->notify(new \App\Notifications\ActivarCuentasNotification());
            }catch(Exception $e)
            {
                echo $e->getMessage();
            }
            
            sleep(1);
        }*/
        
        //
    }
}
