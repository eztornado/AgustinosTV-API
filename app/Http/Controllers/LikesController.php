<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Video;
use App\Evento;
use App\VideosLikes;
use App\EventosLikes;
use Illuminate\Support\Facades\DB;

class LikesController extends Controller
{

    /**
     * @OA\Get(
     *      path="/api/likes/evento/{id_evento}",
     *      tags={"Likes"},
     *      summary="Obtener los likes de un evento",
     *      description="Obtener los likes de un evento",
     *      @OA\Parameter(
     *          name="id_evento",
     *          description="Id de evento",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          ),   
     *      ),   
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=404, description="Evento no encontrado"),
     * )
     * */       
    
    public function getLikesFromEvento($id_evento)
    {
        $evento = Evento::find($id_evento);
        if(!is_null($evento))
        {
            return response(json_encode($evento->likes),200);
        }
        else
        {
            return response(json_encode('Evento no encontrado'),404);
        }
    }
    
    /**
     * @OA\Get(
     *      path="/api/likes/video/{id_video}",
     *      tags={"Likes"},
     *      summary="Obtener los likes de un vídeo",
     *      description="Obtener los likes de un vídeo",
     *      @OA\Parameter(
     *          name="id_video",
     *          description="Id de vídeo",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          ),   
     *      ),   
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=404, description="Vídeo no encontrado"),
     * )
     * */     
    
    public function getLikesFromVideo($id_video)
    {
        $video = Video::find($id_video);
        if(!is_null($video))
        {
            return response(json_encode($video->likes),200);
        }
        else
        {
            return response(json_encode('Vídeo no encontrado'),404);
        }
    }    
    
    /**
     * @OA\Post(
     *      path="/api/likes/evento/{id_evento}",
     *      tags={"Likes"},
     *      summary="Añadir like a un evento",
     *      description="Añadir like un evento",
     *      @OA\Parameter(
     *          name="id_evento",
     *          description="Id de evento",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          ),   
     *      ),   
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=404, description="Evento no encontrado"),
     * )
     * */     
    
    public function AddLikeToEvento($id_evento)
    {
        $evento = Evento::find($id_evento);
        if(!is_null($evento))
        {
            $evento->likes = $evento->likes +1;
            $evento->save();
            
            EventosLikes::create([
                'user_id' => auth('api')->user()->id,
                'evento_id' => $evento->id
            ]);
            
            
            return response(json_encode($evento->likes),200);
        }
        else
        {
            return response(json_encode('Evento no encontrado'),404);
        }
    }  
    
    /**
     * @OA\Delete(
     *      path="/api/likes/evento/{id_evento}",
     *      tags={"Likes"},
     *      summary="Quitar like a un evento",
     *      description="Quitar los likes de un evento",
     *      @OA\Parameter(
     *          name="id_evento",
     *          description="Id de evento",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          ),   
     *      ),   
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=404, description="Evento no encontrado"),
     * )
     * */     
    
    public function QuitLikeToEvento($id_evento)
    {
        $evento = Evento::find($id_evento);
        if(!is_null($evento))
        {
            $evento->likes = $evento->likes -1;
            $evento->save();
            
            $select = DB::select('DELETE FROM eventos_likes where user_id = '.auth('api')->user()->id.' and evento_id = '.$evento->id);
                       
            return response(json_encode($evento->likes),200);
        }
        else
        {
            return response(json_encode('Evento no encontrado'),404);
        }
    }    
    
    /**
     * @OA\Delete(
     *      path="/api/likes/video/{id_video}",
     *      tags={"Likes"},
     *      summary="Quitar like a vídeo",
     *      description="Quitar like a vídoe",
     *      @OA\Parameter(
     *          name="id_evento",
     *          description="Id de vídeo",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          ),   
     *      ),   
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=404, description="Vídeo no encontrado"),
     * )
     * */     
    
    public function QuitLikeToVideo($id_video)
    {
        $video= Video::find($id_video);
        if(!is_null($video))
        {
            $video->likes = $video->likes -1;
            $video->save();
            
            $select = DB::select('DELETE FROM videos_likes where user_id = '.auth('api')->user()->id.' and video_id = '.$video->id);
                       
            return response(json_encode($video->likes),200);
        }
        else
        {
            return response(json_encode('Vídeo no encontrado'),404);
        }
    }    

    /**
     * @OA\Get(
     *      path="/api/check-like/evento/{id_evento}",
     *      tags={"Likes"},
     *      summary="Comprobar si el usuario en sesión ha dado like a este evento",
     *      description="Comprobar si el usuario en sesión ha dado like a este evento",
     *      @OA\Parameter(
     *          name="id_evento",
     *          description="Id de evento",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          ),   
     *      ),   
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=404, description="Evento no encontrado"),
     * )
     * */     
    
    public function CheckLikeEvento($id_evento)
    {
        $evento = Evento::find($id_evento);
        if(!is_null($evento))
        {
            
            $eventoslikes = EventosLikes::where('evento_id',$id_evento)->where('user_id', auth('api')->user()->id)->first();
            
            if(!is_null($eventoslikes))
            {
                return response(json_encode(1),200);
            }
            else
            {
                return response(json_encode(0),200);
            }
            
        }
        else
        {
            return response(json_encode('Evento no encontrado'),404);
        }
    }     
    
    /**
     * @OA\Get(
     *      path="/api/check-like/video/{id_video}",
     *      tags={"Likes"},
     *      summary="Comprobar si el usuario en sesión ha dado like a este vídeo",
     *      description="Comprobar si el usuario en sesión ha dado like a este vídeo",
     *      @OA\Parameter(
     *          name="id_video",
     *          description="Id de vídeo",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          ),   
     *      ),   
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=404, description="Vídeo no encontrado"),
     * )
     * */       
    
    public function CheckLikeVideo($id_video)
    {
        $video = Video::find($id_video);
        if(!is_null($video))
        {
            
            $videoslikes = VideosLikes::where('video_id',$id_video)->where('user_id', auth('api')->user()->id)->first();
            
            if(!is_null($videoslikes))
            {
                return response(json_encode(1),200);
            }
            else
            {
                return response(json_encode(0),200);
            }
            
        }
        else
        {
            return response(json_encode('Vídeo no encontrado'),404);
        }
    }    
    
    /**
     * @OA\Post(
     *      path="/api/likes/video/{id_video}",
     *      tags={"Likes"},
     *      summary="Añadir like a un vídeo",
     *      description="Añadir like un vídeo",
     *      @OA\Parameter(
     *          name="id_video",
     *          description="Id de vídeo",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          ),   
     *      ),   
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=404, description="Vídeo no encontrado"),
     * )
     * */     
    
    public function AddLikeToVideo($id_video)
    {
        $video = Video::find($id_video);
        if(!is_null($video))
        {
            $video->likes = $video->likes +1;
            $video->save();
            
            VideosLikes::create([
                'user_id' =>  auth('api')->user()->id,
                'video_id' => $video->id
            ]);
            
            
            return response(json_encode($video->likes),200);
        }
        else
        {
            return response(json_encode('Evento no encontrado'),404);
        }
    }       
    
    //
}
