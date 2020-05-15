<?php
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Evento;
use Illuminate\Support\Facades\DB;


class EventosController extends Controller
{
    
    
    /**
     * @OA\Get(
     *     path="/api/eventos",
     *      tags={"Eventos"},
     *     summary="Listar eventos",
     *     description="Obtener la lista de eventos",
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
     *          name="canal_id",
     *          description="Id de el canal",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),     
     *     @OA\Response(
     *         response=200,
     *         description="Lista resultado de los eventos"
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
                            $usuarios = Evento::with($with)->where($filtro)->with($width)->orderBy('id',$request->order)->paginate($request->limite);
                            return response(json_encode($usuarios), 200);                                                 
                        }
                        else
                        {
                            $usuarios = Evento::with($with)->where($filtro)->with($width)->orderBy('id',$request->order)->paginate(25);
                            return response(json_encode($usuarios), 200);                                               
                        }

                    }
                    else
                    {
                        if(isset($request->limite))
                        {
                            $usuarios = Evento::with($with)->where($filtro)->with($width)->orderBy('id','DESC')->paginate($request->limite);
                            return response(json_encode($usuarios), 200);                         

                        }
                        else
                        {
                            $usuarios = Evento::with($with)->where($filtro)->with($width)->orderBy('id','DESC')->paginate(25);
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
                            $usuarios = Evento::where($filtro)->orderBy('id',$request->order)->paginate($request->limite);
                            return response(json_encode($usuarios), 200);                                                 
                        }
                        else
                        {
                            $usuarios = Evento::where($filtro)->orderBy('id',$request->order)->paginate(25);
                            return response(json_encode($usuarios), 200);                                               
                        }

                    }
                    else
                    {
                        if(isset($request->limite))
                        {
                            $usuarios = Evento::where($filtro)->orderBy('id','DESC')->paginate($request->limite);
                            return response(json_encode($usuarios), 200);                         

                        }
                        else
                        {
                            $usuarios = Evento::where($filtro)->orderBy('id','DESC')->paginate(25);
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
                            $usuarios = Evento::where('id','>=',1)->orderBy('id',$request->order)->paginate($request->limite);
                            return response(json_encode($usuarios), 200);                                                 
                        }
                        else
                        {
                            $usuarios = Evento::where('id','>=',1)->orderBy('id',$request->order)->paginate(25);
                            return response(json_encode($usuarios), 200);                                               
                        }

                    }
                    else
                    {
                        if(isset($request->limite))
                        {
                            $usuarios = Evento::where('id','>=',1)->orderBy('id','DESC')->paginate($request->limite);
                            return response(json_encode($usuarios), 200);                         

                        }
                        else
                        {
                            $usuarios = Evento::where('id','>=',1)->orderBy('id','DESC')->paginate(25);
                            return response(json_encode($usuarios), 200);                         
                        }  
                    }                

                }

            }
        }
        else
        {
            return response(json_encode(Evento::all()),200);
        }        
        
    }    
    //
    
    
    public function show(Request $request,$id)
    {
        $evento = Evento::with(['Grabaciones','Retransmisiones'])->where('id',$id)->first();
        
        if(!is_null($evento))
        {
            return response(json_encode($evento),200);
        }
        else
        {
            return response(json_encode("Evento no Encontrado"), 404);
        }
        
    }    
    
    /**
     * @OA\Post(
     *      path="/api/eventos/evento_activo_en_canal/{id}",
     *      tags={"Eventos"},
     *      summary="Obtener evento activo dado un canal",
     *      description="Obtener evento activo dado un canal",
     *      @OA\Parameter(
     *          name="id",
     *          description="Id de canal",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Canal obtenido con éxito"
     *       ),
     *      @OA\Response(response=404, description="Evento no encontrado"),
     * )
     * */
    

    
    public function eventoActivoEnCanal(Request $request, $id){
        
        $evs = DB::select('select * from eventos where deleted_at is null and canal_id ='.$id." and estado = 'EMITIENDO'");

        //SE SUPONE QUE SOLO HAY 1 EVENTO EMITIENDO
        foreach($evs as $e)
        {
            $evento = Evento::find($e->id);
            if(!is_null($evento))
            {
                return response(json_encode($evento), 200);
            }
        }
        return response(json_encode('Sin evento Activo'), 404);
        


    }   
    
    public function store(Request $request)
    {


        //VALIDACIÓN DATOS ?
        $evento = Evento::create([
            'title' => $request->title,
            'descripcion' => $request->url,
            'inicio' => $request->inicio,
            'final' =>  $request->final,
            'delegacion_id' =>  $request->delegacion_id,
            'canal_id' =>  $request->canal_id,

        ]);
        return response(json_encode($evento),200);
    }        
    
    /**
     * @OA\Put(
     *      path="/api/eventos/{id}",
     *      tags={"Eventos"},
     *      summary="Actualizar un evento",
     *      description="Actualizar un evento",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/x-www-form-urlencoded",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="title",
     *                   description="Título del evento",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="description",
     *                   description="Descripción del evento",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="inicio",
     *                   description="Fecha/Hora de Inicio",
     *                   type="DATETIME"
     *               ),
     *               @OA\Property(
     *                   property="final",
     *                   description="Fecha/Hora de el Final",
     *                   type="DATETIME"
     *               ),    
     *               @OA\Property(
     *                   property="delegacion_id",
     *                   description="Id de la Delegación",
     *                   type="integer"
     *               ),              
     *               @OA\Property(
     *                   property="views",
     *                   description="Views",
     *                   type="integer"
     *               ),
     *               @OA\Property(
     *                   property="likes",
     *                   description="Likes",
     *                   type="integer"
     *               ),
     *               @OA\Property(
     *                   property="max_online",
     *                   description="Max Online",
     *                   type="integer"
     *               ),  
     *               @OA\Property(
     *                   property="canal_id",
     *                   description="Id de Canal",
     *                   type="integer"
     *               ),        
     *               @OA\Property(
     *                   property="estado",
     *                   description="estado de el evento ['EN_ESPERA','EMITIENDO','FINALIZADO']",
     *                   type="string"
     *               ),     
     *           )
     *       )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=404, description="Evento no encontrado"),
     * )
     * */
    
    public function update(Request $request,$id)
    {
        $evento = Evento::find($id);
        if(!is_null($user)) {

            if(isset($request->title))
            {
                $evento->title = $request->title;
            }
            
            if(isset($request->descripcion))
            {
                $evento->descripcion = $request->descripcion;
            }            
            
            if(isset($request->inicio))
            {
                $evento->inicio = $request->inicio;
            }  

            if(isset($request->final))
            {
                $evento->final = $request->final;
            }            
            
            if(isset($request->delegacion_id))
            {
                $evento->delegacion_id = $request->delegacion_id;
            }     
            
            if(isset($request->views))
            {
                $evento->views = $request->views;
            }  
            
            if(isset($request->likes))
            {
                $evento->likes = $request->likes;
            }    
            
            if(isset($request->max_online))
            {
                $evento->max_online = $request->max_online;
            }      
            if(isset($request->canal_id))
            {
                $evento->canal_id = $request->canal_id;
            }          
            if(isset($request->estado))
            {
                $evento->estado = $request->estado;
            }            

            

            $evento->save();
            return response(json_encode($evento), 200);

        }
        else{
            return response(json_encode('Evento no encontrado'), 404);
        }
    }
    
    /**
     * @OA\Post(
     *      path="/api/eventos/ver/{id}",
     *      tags={"Eventos"},
     *      summary="Ver un evento como usuario",
     *      description="Ver un evento como usuario",
     *      @OA\Parameter(
     *          name="id",
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
    
    public function VerEvento($id)
    {
        $evento = Evento::find($id);
        if(!is_null($evento))
        {
            $evento->views = $evento->views + 1;
            $evento->save();
        }
        else
        {
            return response(json_encode('Evento no encontrado'), 404);
        }
    }
    
}
    
