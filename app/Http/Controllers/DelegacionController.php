<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Delegacion;
use Illuminate\Support\Str;
use App\Nodo;

class DelegacionController extends Controller
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
                        $usuarios = Delegacion::with($with)->where($filtro)->with($width)->orderBy('id',$request->order)->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                                                 
                    }
                    else
                    {
                        $usuarios = Delegacion::with($with)->where($filtro)->with($width)->orderBy('id',$request->order)->paginate(25);
                        return response(json_encode($usuarios), 200);                                               
                    }
                                     
                }
                else
                {
                    if(isset($request->limite))
                    {
                        $usuarios = Delegacion::with($with)->where($filtro)->with($width)->orderBy('id','DESC')->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                         
                        
                    }
                    else
                    {
                        $usuarios = Delegacion::with($with)->where($filtro)->with($width)->orderBy('id','DESC')->paginate(25);
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
                        $usuarios = Delegacion::where($filtro)->orderBy('id',$request->order)->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                                                 
                    }
                    else
                    {
                        $usuarios = Delegacion::where($filtro)->orderBy('id',$request->order)->paginate(25);
                        return response(json_encode($usuarios), 200);                                               
                    }
                                     
                }
                else
                {
                    if(isset($request->limite))
                    {
                        $usuarios = Delegacion::where($filtro)->orderBy('id','DESC')->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                         
                        
                    }
                    else
                    {
                        $usuarios = Delegacion::where($filtro)->orderBy('id','DESC')->paginate(25);
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
                        $usuarios = Delegacion::where('id','>=',1)->orderBy('id',$request->order)->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                                                 
                    }
                    else
                    {
                        $usuarios = Delegacion::where('id','>=',1)->orderBy('id',$request->order)->paginate(25);
                        return response(json_encode($usuarios), 200);                                               
                    }
                                     
                }
                else
                {
                    if(isset($request->limite))
                    {
                        $usuarios = Delegacion::where('id','>=',1)->orderBy('id','DESC')->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                         
                        
                    }
                    else
                    {
                        $usuarios = Delegacion::where('id','>=',1)->orderBy('id','DESC')->paginate(25);
                        return response(json_encode($usuarios), 200);                         
                    }  
                }                
                
            }

        }
    }
    else
    {
        return response(json_encode(Delegacion::all()),200);
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
    
    public function store(Request $request)
    {


        $extension = '';
        $nombre_fichero = ''; 
        $nombre = '';
        
        try {
            $imagen = $request->fichero_contenido;
            $nombre = $request->fichero_nombre;         
            
            var_dump($imagen);
            var_dump($nombre);
            $explode_nombre = explode(".", $nombre);
            $extension = $explode_nombre[1];
            $nombre = $explode_nombre[0];
            $nombre_fichero = Str::slug($nombre).".".$extension;  
        }
        catch(Exception $e)
        {
            return response(json_encode("El nombre de fichero debe tener una extensión"),500);
        }        

        //VALIDACIÓN DATOS ?
        $delegacion = Delegacion::create([
            'nombre' => $request->nombre,
            'web' => $request->web,
            'imagen' => $nombre_fichero,
        ]);
        

        
        $delegacion = \App\Delegacion::find($delegacion->id);
        if(!is_null($delegacion))
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

            $nodo = Nodo::find(env('NODO_ACTUAL'));
            $ruta = ''.$nodo->ruta_imagenes.'delegaciones/'.$delegacion->id.'';

            mkdir($ruta, 0777);
            $ruta.= '/';

            echo "Escribiendo : ".$ruta.$nombre_fichero."\n";
            file_put_contents ($ruta.$nombre_fichero,$contenido);            
            
        }
        else {
              return response(json_encode('Delegación no encontrada'),404);
        }        
        return response(json_encode($delegacion),200);
    } 
    
    public function show(Request $request, $id){
        $delegacion = Delegacion::find($id);
        if(!is_null($delegacion)) {
            return response(json_encode($delegacion), 200);
        }
        else{
            return response(json_encode('Delegación no encontrada'), 404);
        }


    }        
    
    public function update(Request $request,$id)
    {
        $delegacion = Delegacion::find($id);
        if(!is_null($delegacion)) {



            
            

            if(isset($request->fichero_contenido) && isset($request->fichero_nombre))
            {
                $extension = '';
                $nombre_fichero = ''; 
                $nombre = '';

                try {
                    $imagen = $request->fichero_contenido;
                    $nombre = $request->fichero_nombre;         

                    var_dump($imagen);
                    var_dump($nombre);
                    $explode_nombre = explode(".", $nombre);
                    $extension = $explode_nombre[1];
                    $nombre = $explode_nombre[0];
                    $nombre_fichero = Str::slug($nombre).".".$extension;  
                }
                catch(Exception $e)
                {
                    return response(json_encode("El nombre de fichero debe tener una extensión"),500);
                }      

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

                $nodo = Nodo::find(env('NODO_ACTUAL'));
                $ruta = ''.$nodo->ruta_imagenes.'delegaciones/'.$delegacion->id.'';

                //mkdir($ruta, 0777);
                $ruta.= '/';

                echo "Escribiendo : ".$ruta.$nombre_fichero."\n";
                file_put_contents ($ruta.$nombre_fichero,$contenido);   

                $delegacion->fill([
                    'nombre' => $request->nombre,
                    'web' => $request->web,
                    'imagen' => $nombre_fichero,
                ]);                
                $delegacion->save();

                return response(json_encode($delegacion), 200);

                }   
                
                else
                {
                    $delegacion->fill([
                        'nombre' => $request->nombre,
                        'web' => $request->web,
                    ]);
                    $delegacion->save();
                }
            }

    }
    
    public function destroy($id){
        $delegacion = Delegacion::find($id);
        if(!is_null($delegacion)) {
            $delegacion->delete();
            return response(json_encode('Delegación eliminada con éxito'),200);
        }
        else {
            return response(json_encode('Delegación no encontrada'),404);
        }



    }     
        
}
