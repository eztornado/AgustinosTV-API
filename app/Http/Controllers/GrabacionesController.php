<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GrabacionesController extends Controller
{
    
    public function show(Request $request,$id)
    {
        $grabacion = \App\Grabaciones::find($id);
        if(!is_null($grabacion))
        {
            return response(json_encode($grabacion),200);
        }
        else
        {
           return response(json_encode("Grabación no Encontrada"),404); 
        }
        
    }
    

    public function subir($id)
    {
        $grabacion = \App\Grabaciones::find($id);
        if(!is_null($grabacion))
        {
            $nombre = explode(".",$grabacion->nombre_fichero);
            $evento = \App\Evento::find($grabacion->evento_id);
            $nodo = \App\Nodo::find(env('NODO_ACTUAL'));
            $delegacion = \App\Delegacion::find($evento->delegacion_id);
            if(!is_null($delegacion))
            {
                if(!is_null($nodo))
                {
                    if(!is_null($evento))
                    {
                        $video = \App\Video::create([
                            'nodo_id' => $grabacion->nodo_id,
                            'user_id' => auth('api')->user()->id,
                            'estado' => 'DRAFT',
                            'access' => 'REGISTERED',
                            'title' => $evento->title,
                            'details' => $evento->descripcion,
                            'active' => 1,
                            'rutaf_video' => $grabacion->nombre_fichero,
                            'video_nombre' => $nombre[0],
                            'delegacion_id' => $evento->delegacion_id,
                            'views' => $evento->views,
                            'likes' => $evento->likes,
                            'image' => $delegacion->imagen,
                        ]);  

                        $grabacion->video_id = $video->id;
                        $grabacion->save();

                        $ruta_imagen_delegacion = $nodo->ruta_imagenes."delegaciones/".$evento->delegacion_id."/".$delegacion->imagen;
                        
                        $ruta = ''.$nodo->ruta_imagenes.'videos/'.$video->id.'';

                        mkdir($ruta, 0777);                        
                        copy($ruta_imagen_delegacion,$nodo->ruta_imagenes."videos/".$video->id."/".$delegacion->imagen);


                        $likes_evento = \App\EventosLikes::where('evento_id',$evento->id)->get();
                        foreach($likes_evento as $le)
                        {
                            $like = \App\VideosLikes::create([
                                'video_id' => $video->id,
                                'user_id' => $le->user_id
                             ]);
                        }

                        $planificador = \App\Planner::create([
                            'estado' => 'ESPERA',
                            'accion' => 'RENDERIZAR_VIDEO',
                            'id_video' => $video->id,
                            'es_pendiente' => 1,
                        ]);

                        return response(json_encode("ok"),200);


                    }
                    else
                    {
                        return response(json_encode("Evento no encontrado"),404); 
                    }
                }
                else
                {
                    return response(json_encode("Nodo no encontrado"),404); 
                }
            }
            else
            {
                return response(json_encode("Delegación no encontrada"),404); 
            }
            

            
        }
        else
        {
            return response(json_encode("Grabación no Encontrada"),404); 
        }
        
    }
    //
}
