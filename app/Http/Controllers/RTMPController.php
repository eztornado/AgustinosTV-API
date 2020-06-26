<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RTMPController extends Controller
{
    

    public function genteOnline()
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
       
        
        return response(\GuzzleHttp\json_encode($valor),200);
    }
    
    public function Ok(Request $request)
    {
        
        $nodo = Nodo::find(env('NODO_ACTUAL'));
        $datos_fichero = $request->files->all();
        if(sizeof($datos_fichero) > 0)
        {
            $datos_fichero['file']->move($nodo->ruta_videos_originales,$datos_fichero['file']->getClientOriginalName());
            unlink($nodo->ruta_videos_originales.$datos_fichero['file']->getClientOriginalName());
            
        }         
        
    }
}