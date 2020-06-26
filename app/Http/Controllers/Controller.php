<?php

namespace App\Http\Controllers;



use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


/**
 * @OA\Info(title="Agustinos.tv API", version="3.20.01")
 *
 * @OA\Server(url="http://api.agustinos.tv")
 */


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    function MapeoWithInputRequest(Request $request)
    {
        $with = array();
        $array = $request->all();

            if(isset($array['with-noticia']))
            {
                array_push($with,'Noticias');
            }
            if(isset($array['with-seccion-has-tipo-boletin']))
            {
                array_push($with,'SeccionHasTipoBoletin');
            }
            if(isset($array['with-boletin-has-noticia']))
            {
                array_push($with,'BoletinHasNoticia');
            }

            if(isset($array['with-cabecera']))
            {
                array_push($with,'Cabecera');
            }

            if(isset($array['with-boletin']))
            {
                array_push($with,'Boletines');
            }

            if(isset($array['with-tipos-boletin']))
            {
                array_push($with,'TipoBoletin');
            }

            if(isset($array['with-cabecera-has-ficheros']))
            {
                array_push($with,'CabeceraHasFicheros');
            }

            if(isset($array['with-noticia-has-ficheros']))
            {
                array_push($with,'NoticiaHasFicheros');
            }

            if(isset($array['with-tema']))
            {
                array_push($with,'Tema');
            }

            if(isset($array['with-medios-comunicacion']))
            {
                array_push($with,'MediosComunicacion');
            }

            if(isset($array['with-paises']))
            {
                array_push($with,'Paises');
            }

            if(isset($array['with-seccion']))
            {
                array_push($with,'Seccion');
            }




        return $with;

    }
    function MapeoFiltro(Request $request)
    {
        $filtro = array();
        $array = $request->all();
        if(isset($array['nombre']))
        {
            array_push($filtro,['nombre','like','%'.$request->nombre.'%']);

        }
        if(isset($array['title']))
        {
            array_push($filtro,['title','like','%'.$request->title.'%']);

        }        
        if(isset($array['delegacion_id']))
        {
            array_push($filtro,['delegacion_id',$request->delegacion_id]);

        }
        if(isset($array['canal_id']))
        {
            array_push($filtro,['canal_id',$request->canal_id]);

        }    
        if(isset($array['estado']))
        {

                array_push($filtro,['estado',$request->estado]);

        }            
        return $filtro;

    }    
    
}
