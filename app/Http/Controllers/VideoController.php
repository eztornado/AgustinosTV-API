<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Video;
use Illuminate\Support\Str;
use App\Nodo;
use Illuminate\Support\Facades\Input;
use Zend\Diactoros\ServerRequestFactory;

class VideoController extends Controller
{
    
    
    public function index(Request $request)
    {
        if(isset($request->page)) {
            
        $filtro = $this->MapeoFiltro($request);
        $with = $this->MapeoWithInputRequest($request);
        
        if(sizeof($with) > 0)
        {
            if(sizeof($filtro) > 0)
            {
                if(isset($request->order))
                {
                    if(isset($request->limite))
                    {
                        $usuarios = Video::with($with)->where($filtro)->with($width)->orderBy('id',$request->order)->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                                                 
                    }
                    else
                    {
                        $usuarios = Video::with($with)->where($filtro)->with($width)->orderBy('id',$request->order)->paginate(25);
                        return response(json_encode($usuarios), 200);                                               
                    }
                                     
                }
                else
                {
                    if(isset($request->limite))
                    {
                        $usuarios = Video::with($with)->where($filtro)->with($width)->orderBy('id','DESC')->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                         
                        
                    }
                    else
                    {
                        $usuarios = Video::with($with)->where($filtro)->with($width)->orderBy('id','DESC')->paginate(25);
                        return response(json_encode($usuarios), 200);                         
                    }  
                }
            }            
        }
        else
        {
            if(sizeof($filtro) > 0)
            {
                if(isset($request->order))
                {
                    if(isset($request->limite))
                    {
                        $usuarios = Video::where($filtro)->orderBy('id',$request->order)->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                                                 
                    }
                    else
                    {
                        $usuarios = Video::where($filtro)->orderBy('id',$request->order)->paginate(25);
                        return response(json_encode($usuarios), 200);                                               
                    }
                                     
                }
                else
                {
                    if(isset($request->limite))
                    {
                        $usuarios = Video::where($filtro)->orderBy('id','DESC')->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                         
                        
                    }
                    else
                    {
                        $usuarios = Video::where($filtro)->orderBy('id','DESC')->paginate(25);
                        return response(json_encode($usuarios), 200);                         
                    }  
                }
            }    
            else
            {
                if(isset($request->order))
                {
                    if(isset($request->limite))
                    {
                        $usuarios = Video::where('id','>=',1)->orderBy('id',$request->order)->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                                                 
                    }
                    else
                    {
                        $usuarios = Video::where('id','>=',1)->orderBy('id',$request->order)->paginate(25);
                        return response(json_encode($usuarios), 200);                                               
                    }
                                     
                }
                else
                {
                    if(isset($request->limite))
                    {
                        $usuarios = Video::where('id','>=',1)->orderBy('id','DESC')->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                         
                        
                    }
                    else
                    {
                        $usuarios = Video::where('id','>=',1)->orderBy('id','DESC')->paginate(25);
                        return response(json_encode($usuarios), 200);                         
                    }  
                }                
                
            }

        }
    }
    else
    {
        return response(json_encode(Video::all()),200);
    }        
        
       /* if(isset($request->page)) {
            $users = User::where('id', '>=', 1)->orderBy('id','DESC')->paginate(25);
            return response(json_encode($users), 200);
        }
        else
        {
            return response(json_encode(User::all()),200);
        }*/
    }    
    //
    
    /**
     * @OA\Get(
     *      path="/api/ver-video/{id}",
     *      tags={"Videos"},
     *      summary="Obtener un vídeo para ser reproducido",
     *      description="Obtener un vídeo para ser reproducido",
     *      @OA\Parameter(
     *          name="id",
     *          description="Id de el Vídeo",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Vídeo obtenido con éxito"
     *       ),
     *      @OA\Response(response=404, description="Vídeo no encontrado"),
     * )
     * */
    
    
    public function verVideo($id)
    {
        $video = Video::where('id',$id)->where('active',1)->with(['Delegacion','VideoCategory'])->first();
        if(!is_null($video))
        {
            return response(json_encode($video),200);
        }
        else
        {
            return response(json_encode('Vídeo no encontrado'),404);
        }
    }
    
  
    public function reproducir($id,$tipo)
    {
        $video_nombre = null;
        $video_nombre2 = null;
        $file = null;
        $size = "";
        
        

        //$r_video = $this->db->query("SELECT * FROM videos where id = ".$this->db->escape($id)."");
        $video = Video::find($id);
        if(!is_null($video))
        {
            $video_nombre2 = explode(".",$video->video_nombre);

            if($tipo == "360p")
            {
                    $file = "/home/video_src/".$video_nombre2[0]."_360p.mp4";
                    $size = filesize('/home/video_src/'.$video_nombre2[0]."_360p.mp4");            

            }
            if($tipo == "720p")
            {
                    $file = "/home/video_src/".$video_nombre2[0]."_720p.mp4";
                    $size = filesize('/home/video_src/'.$video_nombre2[0]."_720p.mp4");            

            }

            //REPRODUCIR OTROS
            header('X-sendfile: '.$file.'');
            header("Content-Type: video/mp4");	
            header("Content-Disposition: attachment; filename=\"".$video->video_nombre.".mp4");	
            header("Content-Size: ".$size."");    
        } 
        else
        {
            return response(json_encode('Vídeo no encontrado'),404);
        }
    }
    
    /**
     * @OA\Post(
     *      path="/api/videos/{id}/addView",
     *      tags={"Videos"},
     *      summary="Añadir visualización a un vídeo",
     *      description="Añadir visualización a un vídeo",
     *      @OA\Parameter(
     *          name="id",
     *          description="Id de el Vídeo",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Vídeo obtenido con éxito"
     *       ),
     *      @OA\Response(response=404, description="Vídeo no encontrado"),
     * )
     * */      
    public function addview($id)
    {
        $video = Video::find($id);
        if(!is_null($video))
        {
            $video->views = $video->views + 1;
            $video->save();
            return response(json_encode('Añadida visualización'),200);
            
        }else
        {
             return response(json_encode('Vídeo no encontrado'),404);
        }
        
    }
    
    public function store(Request $request)
    {


        $extension = '';
        $nombre_fichero = ''; 
        $nombre = '';
        
        try {
            $imagen = $request->fichero_contenido;
            $nombre = $request->fichero_nombre;         
            
            $explode_nombre = explode(".", $nombre);
            $extension = $explode_nombre[1];
            $nombre = $explode_nombre[0];
            $nombre_fichero = Str::slug($nombre).".".$extension;  
        }
        catch(Exception $e)
        {
            return response(json_encode("El nombre de fichero debe tener una extensión"),500);
        }        

        $nodo = Nodo::find(env('NODO_ACTUAL'));
        //VALIDACIÓN DATOS ?
        $video = Video::create([
            'title' => $request->title,
            'details' => $request->details,
            'estado' => $request->estado,
            'delegacion_id' => $request->delegacion_id,
            'user_id' => $request->user_id,
            'nodo_id' => $nodo->id,
            'image' => $nombre_fichero,
        ]);
        

        
        $video = \App\Video::find($video->id);
        if(!is_null($video))
        {

            try {
                $imagen_explode = explode(";", $imagen);
                $mimetype_explode = explode(":", $imagen_explode[0]);
                $mimetype = $mimetype_explode[1];
                $contenido_explode = explode(",",str_replace(" ","",$imagen_explode[1]));
                $contenido = base64_decode($contenido_explode[1]);
            }catch(Exception $e)
            {
                return response(json_encode("El contenido del fichero no tiene el formato esperado"),500);
            }

            
            $ruta = ''.$nodo->ruta_imagenes.'videos/'.$video->id.'';

            mkdir($ruta, 0777);
            $ruta.= '/';

            file_put_contents ($ruta.$nombre_fichero,$contenido);            
            
        }
        else {
              return response(json_encode('Delegación no encontrada'),404);
        }        
        return response(json_encode($video),200);
    }    
    
    public function upload(Request $request,$id,$video_upload_title)
    {
        
        $datos_fichero = $request->files->all();
        if(sizeof($datos_fichero) == 0)
        {
            return response(json_encode('No hay fichero adjunto'),404);
        }
        else
        {
            $video = Video::find($id);
            $nodo = Nodo::find(env('NODO_ACTUAL'));
            
            
        $nombre_fichero = $datos_fichero['file']->getClientOriginalName();
        $explode_nombre = explode(".", $nombre_fichero);
        $extension = $explode_nombre[1];
        $nombre = $explode_nombre[0];        

        $i=1;
        while(file_exists($nodo->ruta_videos_originales.$nombre_fichero)) {
            $nombre_fichero = Str::slug($nombre,'-') . '_'.$i . '.' . $extension;
            $i++;
        }            
            
            
            $datos_fichero['file']->move($nodo->ruta_videos_originales,$nombre_fichero);
            if(!is_null($video))
            {
               $array_nombre = explode('.',$datos_fichero['file']->getClientOriginalName());
               $video->rutaf_video = $datos_fichero['file']->getClientOriginalName();
               $video->video_nombre = $nombre_fichero;
               $video->save();
               
               
               $planer = \App\Planner::create([
                  'estado' => 'ESPERA',
                  'accion' => 'RENDERIZAR_VIDEO',
                  'id_video' => $video->id,
                  'es_pendiente' => 0,
                   
               ]);
               
               return response(json_encode('ok'),200);
               
               
                
                
            }
            else
            {
                //Eliminar Fichero subido
                unlink($nodo->ruta_videos_originales.$datos_fichero['file']->getClientOriginalName());
                return response(json_encode('Vídeo no encontrado'),404);
            }
            
        }
        

  


            
            
            
    }
    
    public function show(Request $request, $id){
        $video = Video::find($id);
        if(!is_null($video)) {
            return response(json_encode($video), 200);
        }
        else{
            return response(json_encode('Vídeo no encontrado'), 404);
        }


    }       
    
    public function destroy($id){
        $video = Video::find($id);
        if(!is_null($video)) {
            $video->delete();
            return response(json_encode('Vídeo eliminado con éxito'),200);
        }
        else {
            return response(json_encode('Vídeo no encontrado'),404);
        }



    }    
    
    public function update(Request $request,$id)
    {
        $video = Video::find($id);
        if(!is_null($video)) {
            
        $extension = '';
        $nombre_fichero = ''; 
        $nombre = '';
        $nodo = Nodo::find(env('NODO_ACTUAL'));
        
        try {
            if(isset($request->fichero_contenido) && isset($request->fichero_nombre))
            {
                $imagen = $request->fichero_contenido;
                $nombre = $request->fichero_nombre; 
                $explode_nombre = explode(".", $nombre);
                $extension = $explode_nombre[1];
                $nombre = $explode_nombre[0];
                $nombre_fichero = Str::slug($nombre).".".$extension;      
                $contenido = null;
                
                try {
                    $imagen_explode = explode(";", $imagen);
                    $mimetype_explode = explode(":", $imagen_explode[0]);
                    $mimetype = $mimetype_explode[1];
                    $contenido_explode = explode(",",str_replace(" ","",$imagen_explode[1]));
                    $contenido = base64_decode($contenido_explode[1]);
                }catch(Exception $e)
                {
                    return response(json_encode("El contenido del fichero no tiene el formato esperado"),500);
                }
                
                
                $ruta = ''.$nodo->ruta_imagenes.'videos/'.$video->id.'';

                //mkdir($ruta, 0777);
                $ruta.= '/';

                file_put_contents ($ruta.$nombre_fichero,$contenido);            

            }
    
            
        }
        catch(Exception $e)
        {
            return response(json_encode("El nombre de fichero debe tener una extensión"),500);
        }               


            if($nombre_fichero != '')
            {
        
                $video->fill([
                    'title' => $request->title,
                    'details' => $request->details,
                    'estado' => $request->estado,
                    'delegacion_id' => $request->delegacion_id,
                    'user_id' => $request->user_id,
                    'nodo_id' => $nodo->id,
                    'image' => $nombre_fichero,
                ]);

                $video->save();
            }
            else
            {
                $video->fill([
                    'title' => $request->title,
                    'details' => $request->details,
                    'estado' => $request->estado,
                    'delegacion_id' => $request->delegacion_id,
                    'user_id' => $request->user_id,
                    'nodo_id' => $nodo->id,
                ]);         
                $video->save();
                
            }
            return response(json_encode($video), 200);

        }
        else{
            return response(json_encode('Nodo no encontrado'), 404);
        }
    }    
        
    //
}
