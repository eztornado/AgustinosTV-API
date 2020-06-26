<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Services\MailChimpService;
use App\User;

class CheckExisteEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     * 
     */
    protected $signature = 'mailchimp:update_audience';

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
        
        
        
      $usuarios = User::where('id','>=',1)->where('active',1)->get(); 
      
      

        $id_lista_princpal = 'a5c228f4d0';

        $lista = $this->mailChimpService->ObtenerMiembrosLista($id_lista_princpal,1000);
        $miembros = $lista['members'];
        $lista = $this->mailChimpService->ObtenerMiembrosLista($id_lista_princpal,999,1000);
        $miembros2 = $lista['members'];    
        
        $lista = $this->mailChimpService->ObtenerSubscripcionesLista($id_lista_princpal,1000);
        $subscritos = $lista['members'];
        $lista = $this->mailChimpService->ObtenerSubscripcionesLista($id_lista_princpal,999,1000);
        $subscritos2 = $lista['members'];            
        echo sizeof($miembros)."\n";
        echo sizeof($miembros2)."\n";
        echo sizeof($subscritos)."\n";
        echo sizeof($subscritos2)."\n";        

        
        $usuarios = \App\User::where('id','>=',1)->where('active',1)->get();
        
        foreach($usuarios as $u)
        {
                if($u['email'] != 'carmenmartinezbaeza@hotmail.com'){
                echo "==============================================\n";
                echo ">> Analizando usuario : ".$u['email']." ... \n ";
                $registrado_en_mailchimp = false;
                foreach($miembros as $m)
                {
                    if(trim($m['email_address'],' ') == trim($u['email'],' '))
                    {
                        $registrado_en_mailchimp = true;
                    }
                }
                foreach($miembros2 as $m)
                {
                    if($m['email_address'] == $u['email'])
                    {
                        $registrado_en_mailchimp = true;
                    }
                }            

                echo ">> Existe en MailChimp : ".intval($registrado_en_mailchimp)."\n";
                    if($registrado_en_mailchimp == false)
                    {
                        echo ">> Comprobando si el email existe ... \n";
                        $emailChecker = new \Tintnaingwin\EmailCheckerPHP\EmailChecker();
                        $existe =  $emailChecker->check($u['email']);
                        echo ">> Existe el email : ".intval($existe)."\n";
                        
                        if (strpos($u['email'], 'gmail.com') === false || strpos($u['email'], 'hotmail.es') === false || strpos($u['email'], 'hotmail.com') === false )
                        {
                            $existe = true;
                        }
                        
                        if($existe == true)
                        {
                            echo ">> Añadiendo miembro a MailChimp ... \n";
                            try {
                            $this->mailChimpService->AñadirMiembroLista($id_lista_princpal,$u['email'],'subscribed');
                            echo ">> Usuario sin segmento detectado. Creando Segmento .... \n";
                            $segmento = $this->mailChimpService->CrearSegmento('a5c228f4d0',$u['email'],array ($u['email']));
                            echo ">> Segmento creado \n";
                            $user = User::find($u['id']);
                            $user->mailchimp_segment = $segmento['id'];
                            $user->save();
                            echo ">> Segmento añadido al usuario correspondiente \n";                            
                            }
                            catch(Exception $e)
                            {
                                echo $e->getMessage();
                            }
                            echo ">> Miembro añadido exitosamente !! \n";
                        }

                }
                
                if($registrado_en_mailchimp == true)
                {
                    
                    //Comprobar si está subscrito
                    
                    $subscrito = false;
                    foreach($subscritos as $m)
                    {
                        if(trim($m['email_address'],' ') == trim($u['email'],' '))
                        {
                            $subscrito = true;
                        }
                    }
                    foreach($subscritos2 as $m)
                    {
                        if($m['email_address'] == $u['email'])
                        {
                            $subscrito = true;
                        }
                    }            
                    
                    $user = User::find($u['id']);
                    echo "Comprobando subscripción : Acepto Avisos : ".intval($user->acepto_avisos_contenido)."\n";
                    echo "Subcrito : ".intval($subscrito)."\n";
                    
                    
                    
                    if($user->acepto_avisos_contenido == 0 && $subscrito == true)
                    {                      
                        echo " >> Detectado usuario que no acepta avisos. Desubscribiendo al usuario .. \n";
                        $this->mailChimpService->EditarMiembroLista($id_lista_princpal,$user->email,[
                            'status' => 'unsubscribed',
                        ]);                                               
                    }            
                    else if($user->acepto_avisos_contenido == 1 && $subscrito == false)
                    {
                        echo " >> Detectado usuario no subscrito que hay que subcribir. Subscribiendo al usuario ... \n";
                        $this->mailChimpService->EditarMiembroLista($id_lista_princpal,$user->email,[
                            'status' => 'subscribed',
                        ]);     
                        
                    }
                    
                }





            }    
        }
        
            
        //
    }
}
