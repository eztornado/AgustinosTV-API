<?php 
namespace App\Services;


use NZTim\Mailchimp\Mailchimp;

class MailChimpService {

    var  $id_lista = 'a5c228f4d0';

    //El formato de detalle esperado es : las ids de las listas de mailchip separadas por ,
    public function CrearCampanyas($tipo_campaña,$concepto,$destino = null)
    {
        $mailchimp = new Mailchimp(env('MC_KEY'));

        if($destino == null)
        {
            $envio = $mailchimp->api('post', 'campaigns', [
                'type' => $tipo_campaña,
                'recipients' => ['list_id' => $this->id_lista],
                'settings' => [
                    'subject_line' => $concepto,
                    'title' => $concepto,
                    'from_name' => "AGUSTINOS.TV",
                    'reply_to' => "ras@agustinosalicante.es",
                ],
            ]);
            return $envio;
        }
        else
        {
            $envio = $mailchimp->api('post', 'campaigns', [
                'type' => $tipo_campaña,
                'recipients' => ['list_id' => $this->id_lista, 'segment_opts' => array('saved_segment_id' => intval($destino))],
                'settings' => [
                    'subject_line' => $concepto,
                    'title' => $concepto,
                    'from_name' => "AGUSTINOS.TV",
                    'reply_to' => "ras@agustinosalicante.es",
                ],
            ]);
            return $envio;
            
        }
        
        
      


    }
    public function SubirContenidoCampanya($campanya,$plantilla,$datos)
    {
        $mailchimp = new Mailchimp(env('MC_KEY'));



        $render = view($plantilla,
            [
                'datos' => $datos
            ])->render();


        return $actualizar = $mailchimp->api('put', '/campaigns/'.$campanya['id'].'/content', [
            'html' =>  $render
            ]
        );


    }

    public function EnviarCampanya($campanya)
    {
        $mailchimp = new Mailchimp(env('MC_KEY'));

        return $enviar = $mailchimp->api('post', '/campaigns/'.$campanya['id'].'/actions/send', []);


    }

    public function ProgramarCampaña($campanya,$fechaUTC)
    {
        $mailchimp = new Mailchimp(env('MC_KEY'));
        return $enviar = $mailchimp->api('post', '/campaigns/'.$campanya['id'].'/actions/schedule',
            ['schedule_time' => $fechaUTC]
        );

    }
    
    public function CrearSegmento($lista,$nombre,$array_emails,$options = null)
    {
        if($options == null)
        {
            $mailchimp = new Mailchimp(env('MC_KEY'));
            return $enviar = $mailchimp->api('post', '/lists/'.$lista.'/segments', [
                'name' => $nombre,
                'static_segment' => $array_emails,
            ]);                 
        }
    }
    
    public function AnularProgramacionCampanya($id)
    {
        $mailchimp = new Mailchimp(env('MC_KEY'));
        return $enviar = $mailchimp->api('post', '/campaigns/'.$id.'/actions/unschedule', []);

    }

    public function obtenerCampanya($id)
    {
        $mailchimp = new Mailchimp(env('MC_KEY'));

        try {
            return $enviar = $mailchimp->api('get', '/campaigns/' . $id, []);
        }catch(\Exception $e)
        {
            return false;
        }

    }

    public function ObtenerListas()
    {
        $mailchimp = new Mailchimp(env('MC_KEY'));
        return $mailchimp->api('get', 'lists', []);


    }
    
    public function ObtenerLista($id)
    {
        $mailchimp = new Mailchimp(env('MC_KEY'));
        return $mailchimp->api('get', '/lists/'.$id, []);        
        
    }
    public function ObtenerMiembrosLista($id,$count,$offset = null)
    {        
        $mailchimp = new Mailchimp(env('MC_KEY'));
        
        if($offset == null)
        {
            return $mailchimp->api('get', '/lists/'.$id."/members", [
                'count' => $count,
            ]); 
        }else
        {
            return $mailchimp->api('get', '/lists/'.$id."/members", [
                'count' => $count,
                'offset' => $offset,
            ]);             
            
        }
    }
    
    public function ObtenerSubscripcionesLista($id,$count,$offset = null)
    {        
        $mailchimp = new Mailchimp(env('MC_KEY'));
        
        if($offset == null)
        {
            return $mailchimp->api('get', '/lists/'.$id."/members", [
                'count' => $count,
                'status' => 'subscribed',
            ]); 
        }else
        {
            return $mailchimp->api('get', '/lists/'.$id."/members", [
                'count' => $count,
                'offset' => $offset,
                'status' => 'subscribed',
            ]);             
            
        }
    }    
    
    public function AñadirMiembroLista($id,$email,$estado)
    {        
        $mailchimp = new Mailchimp(env('MC_KEY'));
        return $mailchimp->api('post', '/lists/'.$id."/members", [
            'email_address' => $email,
            'status' => $estado,
        ]); 
    }  
    
    public function EliminarMiembroLista($lista,$email)
    {
        $mailchimp = new Mailchimp(env('MC_KEY'));
        $hash = md5(strtolower($email));
        return $mailchimp->api('delete', '/lists/'.$lista."/members/".$hash, [
        ]);         
        
    }
    
    public function EditarMiembroLista($lista,$email,$datos)
    {
        $mailchimp = new Mailchimp(env('MC_KEY'));
        $hash = md5(strtolower($email));
        return $mailchimp->api('put', '/lists/'.$lista."/members/".$hash, $datos);         
        
    }    

    public function EliminarCampanya($id)
    {
        $mailchimp = new Mailchimp(env('MC_KEY'));
        return $enviar = $mailchimp->api('delete', '/campaigns/'.$id.'', []);

    }

}