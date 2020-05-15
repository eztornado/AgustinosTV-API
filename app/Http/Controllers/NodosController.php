<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Nodo;

class NodosController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users/",
     *      tags={"Usuarios"},
     *     summary="Listar usuarios",
     *     description="Obtener un usuario",
     *      @OA\Parameter(
     *          name="page",
     *          description="Número de Página",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="order",
     *          description="Orden de resultados [ASC/DESC]",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),  
     *      @OA\Parameter(
     *          name="limit",
     *          description="Límite de resultados",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ), 
     *      @OA\Parameter(
     *          name="with_delegacion",
     *          description="Activar relación con Delegación",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),              
     *     @OA\Response(
     *         response=200,
     *         description="Lista resultado de los usuarios"
     *     )
     * )
     */
    
    public function index(Request $request){

        
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
                        $usuarios = Nodo::with($with)->where($filtro)->with($width)->orderBy('id',$request->order)->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                                                 
                    }
                    else
                    {
                        $usuarios = Nodo::with($with)->where($filtro)->with($width)->orderBy('id',$request->order)->paginate(25);
                        return response(json_encode($usuarios), 200);                                               
                    }
                                     
                }
                else
                {
                    if(isset($request->limite))
                    {
                        $usuarios = Nodo::with($with)->where($filtro)->with($width)->orderBy('id','DESC')->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                         
                        
                    }
                    else
                    {
                        $usuarios = Nodo::with($with)->where($filtro)->with($width)->orderBy('id','DESC')->paginate(25);
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
                        $usuarios = Nodo::where($filtro)->orderBy('id',$request->order)->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                                                 
                    }
                    else
                    {
                        $usuarios = Nodo::where($filtro)->orderBy('id',$request->order)->paginate(25);
                        return response(json_encode($usuarios), 200);                                               
                    }
                                     
                }
                else
                {
                    if(isset($request->limite))
                    {
                        $usuarios = Nodo::where($filtro)->orderBy('id','DESC')->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                         
                        
                    }
                    else
                    {
                        $usuarios = Nodo::where($filtro)->orderBy('id','DESC')->paginate(25);
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
                        $usuarios = Nodo::where('id','>=',1)->orderBy('id',$request->order)->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                                                 
                    }
                    else
                    {
                        $usuarios = Nodo::where('id','>=',1)->orderBy('id',$request->order)->paginate(25);
                        return response(json_encode($usuarios), 200);                                               
                    }
                                     
                }
                else
                {
                    if(isset($request->limite))
                    {
                        $usuarios = Nodo::where('id','>=',1)->orderBy('id','DESC')->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                         
                        
                    }
                    else
                    {
                        $usuarios = Nodo::where('id','>=',1)->orderBy('id','DESC')->paginate(25);
                        return response(json_encode($usuarios), 200);                         
                    }  
                }                
                
            }

        }
    }
    else
    {
        return response(json_encode(Nodo::all()),200);
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
    
    public function show(Request $request, $id){
        $nodo = Nodo::find($id);
        if(!is_null($nodo)) {
            return response(json_encode($nodo), 200);
        }
        else{
            return response(json_encode('Nodo no encontrado'), 404);
        }


    }    
    
    public function store(Request $request)
    {


        //VALIDACIÓN DATOS ?
        $nodo= Nodo::create([
            'nombre' => $request->nombre,
            'url' => $request->url,
            'estado' => $request->estado,
            'capacidad' =>  $request->capacidad,
            'ruta_videos_src' =>  $request->ruta_videos_src,
            'ruta_videos_originales' =>  $request->ruta_videos_originales,
            'ruta_imagenes' =>  $request->ruta_imagenes,

        ]);
        return response(json_encode($nodo),200);
    }    
    //
    
    public function update(Request $request,$id)
    {
        $nodo = Nodo::find($id);
        if(!is_null($nodo)) {


                $nodo->fill([
                    'nombre' => $request->nombre,
                    'url' => $request->url,
                    'estado' => $request->estado,
                    'capacidad' =>  $request->capacidad,
                    'ruta_videos_src' =>  $request->ruta_videos_src,
                    'ruta_videos_originales' =>  $request->ruta_videos_originales,
                    'ruta_imagenes' =>  $request->ruta_imagenes,
                ]);

            $nodo->save();
            return response(json_encode($nodo), 200);

        }
        else{
            return response(json_encode('Nodo no encontrado'), 404);
        }
    }
    
    public function destroy($id){
        $nodo = Nodo::find($id);
        if(!is_null($nodo)) {
            $nodo->delete();
            return response(json_encode('Nodo eliminado con éxito'),200);
        }
        else {
            return response(json_encode('Nodo no encontrado'),404);
        }



    }    
    
}
