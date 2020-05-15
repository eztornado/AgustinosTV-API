<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\MailChimpService;


class UserController extends Controller
{
    
    public function __construct(MailChimpService $mailChipService)
    {
        $this->mailChimpService = $mailChipService;
    }    
    
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
                        $usuarios = User::with($with)->where($filtro)->with($width)->orderBy('id',$request->order)->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                                                 
                    }
                    else
                    {
                        $usuarios = User::with($with)->where($filtro)->with($width)->orderBy('id',$request->order)->paginate(25);
                        return response(json_encode($usuarios), 200);                                               
                    }
                                     
                }
                else
                {
                    if(isset($request->limite))
                    {
                        $usuarios = User::with($with)->where($filtro)->with($width)->orderBy('id','DESC')->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                         
                        
                    }
                    else
                    {
                        $usuarios = User::with($with)->where($filtro)->with($width)->orderBy('id','DESC')->paginate(25);
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
                        $usuarios = User::where($filtro)->orderBy('id',$request->order)->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                                                 
                    }
                    else
                    {
                        $usuarios = User::where($filtro)->orderBy('id',$request->order)->paginate(25);
                        return response(json_encode($usuarios), 200);                                               
                    }
                                     
                }
                else
                {
                    if(isset($request->limite))
                    {
                        $usuarios = User::where($filtro)->orderBy('id','DESC')->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                         
                        
                    }
                    else
                    {
                        $usuarios = User::where($filtro)->orderBy('id','DESC')->paginate(25);
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
                        $usuarios = User::where('id','>=',1)->orderBy('id',$request->order)->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                                                 
                    }
                    else
                    {
                        $usuarios = User::where('id','>=',1)->orderBy('id',$request->order)->paginate(25);
                        return response(json_encode($usuarios), 200);                                               
                    }
                                     
                }
                else
                {
                    if(isset($request->limite))
                    {
                        $usuarios = User::where('id','>=',1)->orderBy('id','DESC')->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                         
                        
                    }
                    else
                    {
                        $usuarios = User::where('id','>=',1)->orderBy('id','DESC')->paginate(25);
                        return response(json_encode($usuarios), 200);                         
                    }  
                }                
                
            }

        }
    }
    else
    {
        return response(json_encode(User::all()),200);
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
    
    /**
     * @OA\Get(
     *      path="/api/users/{id}",
     *      tags={"Usuarios"},
     *      summary="Obtener un usuario",
     *      description="Obtener un usuario",
     *      @OA\Parameter(
     *          name="id",
     *          description="Id de usuario",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Usuario obtenido con éxito"
     *       ),
     *      @OA\Response(response=404, description="Usuario no encontrado"),
     * )
     * */
    
    public function show(Request $request, $id){
        $user = User::find($id);
        if(!is_null($user)) {
            return response(json_encode($user), 200);
        }
        else{
            return response(json_encode('Usuario no encontrado'), 404);
        }


    }

    /**
     * @OA\Post(
     *      path="/api/users/",
     *      tags={"Usuarios"},
     *      summary="Guardar un usuario",
     *      description="Guardar un usuario",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/x-www-form-urlencoded",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="username",
     *                   description="Nombre de usuario",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="email",
     *                   description="Email de usuario",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="password",
     *                   description="Contraseña del usuario",
     *                   type="password"
     *               ),
     *           )
     *       )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=409, description="El email introducido ya existe en el sitema"),
     * )
     * */
    
    public function store(Request $request)
    {

        if(isset($request->email))
        {
            //Comprobar si el email ya existe en algún usuario
            $usuarios = User::where('email',$request->email)->get();
            if(sizeof($usuarios) > 0)
            {
                return response(json_encode("El email introducido ya existe en el sistema"),409); // 409 = CONFLICTO DE DATOS
            }
        }


        //VALIDACIÓN DATOS ?
        $usuario = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => 'CUENTA_CREADA_POR_ADMIN',
            'role' =>  'USUARIO',
            'active' => 1,

        ]);
        return response(json_encode($usuario),200);
    }

    /**
     * @OA\Put(
     *      path="/api/users/{id}",
     *      tags={"Usuarios"},
     *      summary="Actualizar un usuario",
     *      description="Actualizar un usuario",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/x-www-form-urlencoded",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="name",
     *                   description="Nombre de usuario",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="email",
     *                   description="Email de usuario",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="password",
     *                   description="Contraseña del usuario",
     *                   type="password"
     *               ),
     *               @OA\Property(
     *                   property="role",
     *                   description="Rol del usuario ['USUARIO','ADMIN','ROOT']",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="active",
     *                   description="Cuenta activada : [0 / 1]",
     *                   type="integer"
     *               ),
     *           )
     *       )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=409, description="El email introducido ya existe en el sitema"),
     * )
     * */
    
    public function update(Request $request,$id)
    {
        $user = User::find($id);
        if(!is_null($user)) {


                $user->fill([
                    'username' => $request->username,
                    'email' => $request->email,
                    'role' => $request->role,
                    'active' => $request->active,

                ]);

            $user->save();
            return response(json_encode($user), 200);

        }
        else{
            return response(json_encode('Usuario no encontrado'), 404);
        }
    }

    
    /**
     * @OA\Delete(
     *      path="/api/users/",
     *      tags={"Usuarios"},
     *      summary="Eliminar un usuario",
     *      description="Eliminar un usuario",
     *      @OA\Parameter(
     *          name="id",
     *          description="Id de usuario",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=403, description="No está permitido eliminar el usuario que está actualmente logueado"),
     *     @OA\Response(response=404, description="Usuario no encontrado"),
     * )
     * */

    
    public function destroy($id){
        $user = User::find($id);
        if(!is_null($user)) {
            if(Auth::id() != $user->id) {
                $user->delete();
                return response(json_encode('Usuario eliminado con éxito'),200);
            }
            else {
                return response(json_encode('No está permitido eliminar el usuario que está actualmente logueado'),403);
            }

        }
        else {
            return response(json_encode('Usuario no encontrado'),404);
        }



    }
    
    public function eliminarCuenta($id)
    {
        $user = User::find($id);
        if(!is_null($user))
        {
            $id_lista_princpal = 'a5c228f4d0';
            if(Auth::id() == $id)
            {
                //Desubscribir en MailChimp
                $this->mailChimpService->EditarMiembroLista($id_lista_princpal,$user->email,[
                    'status' => 'unsubscribed',
                ]);         
                
                //Eliminar MailChimp
                $this->mailChimpService->EliminarMiembroLista($id_lista_princpal,$user->email);
                        
                //Desloguear

                $sesion = \App\Sesiones::where('user_id',$user->id)->where('active',1)->first();
                if(!is_null($sesion))
                {
                    $sesion->token = null;
                    $sesion->active = 0;
                    $sesion->save();
                }

                Auth::guard('api')->logout();
                
                //Eliminar BD
                $user->active = 0;
                $user->deleted_at = date("Y-m-d H:i:s");   
                $user->save();
                
                return response()->json([
                    'status' => 'success',
                    'msg' => 'Logged out Successfully.'
                ], 200);                
            }
            else
            {
                return response(json_encode('Sin permisos para realizar esta acción'),401);
            }
        }
        else
        {
            return response(json_encode('Usuario no encontrado'),404);
        }

                    
                

    }
    
    /**
     * @OA\Get(
     *      path="/api/novedades",
     *      tags={"Novedades"},
     *      summary="Obtiene las novedades de la web personalizadas para el usuario",
     *      description="Obtiene las novedades de la web personalizadas para el usuario",
     *      @OA\Parameter(
     *          name="page",
     *          description="Número de Página",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     * )
     * */    
    public function getNovedades(Request $request)
    {
        try
        {
            $user_id = Auth::id();
            $user = User::find($user_id);
            
        } catch (Exception $ex) {
            return response(json_encode('Usuario no encontrado'),404);

        }            
            
        $resultado = array();
        
        $filtros = $this->MapeoFiltro($request);

        $orden = 'DESC';
        if(isset($request->orden))
        {
            if($request->orden == 'ASC') $orden = 'ASC';
        }
        if(sizeof($filtros) > 0)
        {
            if(isset($request->tipo))
            {
                
                if(intval($request->tipo) == 1)
                {
                    $videos = \App\Video::with(['VideoCategory','Delegacion'])->where($filtros)->orderBy('id',$orden)->where('estado','COMPLETED')->paginate(25);
                    $eventos = array();                   
                }
                else if(intval($request->tipo) == 2)
                {
                    $videos = array();
                    $eventos = \App\Evento::with(['Delegacion','Canal'])->where($filtros)->orderBy('id',$orden)->where('estado','EMITIENDO')->paginate(25);                     

                }
            }
            else
            {
                $videos = \App\Video::with(['VideoCategory','Delegacion'])->where($filtros)->orderBy('id',$orden)->where('estado','COMPLETED')->paginate(25);
                $eventos = \App\Evento::with(['Delegacion','Canal'])->where($filtros)->orderBy('id',$orden)->where('estado','EMITIENDO')->paginate(25);                          
            }
            
        }
        else
        {
            if(isset($request->tipo))
            {
                if(intval($request->tipo) == 1)
                {
                    $videos = \App\Video::with(['VideoCategory','Delegacion'])->orderBy('id',$orden)->where('estado','COMPLETED')->paginate(25);
                    $eventos = array();          
                }
                if(intval($request->tipo) == 2)
                {
                    $videos = array();
                    $eventos = \App\Evento::with(['Delegacion','Canal'])->orderBy('id',$orden)->where('estado','EMITIENDO')->paginate(25); 
                }
                
            }
            else
            {
                $videos = \App\Video::with(['VideoCategory','Delegacion'])->where('id','>=',1)->orderBy('id',$orden)->where('estado','COMPLETED')->paginate(25);
                $eventos = \App\Evento::with(['Delegacion','Canal'])->where('id','>=',1)->orderBy('id',$orden)->where('estado','EMITIENDO')->paginate(25);                     
                
            }
       
            
        }
        foreach($videos as $v)
        {
            $v['tipo_dato'] = 1;
            array_push($resultado,$v);
        }
        foreach($eventos as $e)
        {
            $e['tipo_dato'] = 2;
            array_push($resultado,$e);
        }        

        return response(json_encode($resultado),200);
            
            


    }
    
    public function AdminHomeStats()
    {
        $resultado = array();
        $query_total_usuarios = DB::select('select count(id) contador from users where active = 1 and deleted_at is null'); 
         foreach($query_total_usuarios as $u)
        {
             $resultado['total_usuarios'] = $u->contador;
            
        }
        
        $query_usuarios_conectados = DB::select('select count(token) contador from sesiones where token is not null and active = 1');
         foreach($query_usuarios_conectados as $u)
        {
             $resultado['usuarios_conectados'] = $u->contador;
            
        }   
        
        $query_maximo_online = DB::select('select max(max_online)contador from eventos');
         foreach($query_maximo_online as $u)
        {
             $resultado['maximo_online'] = $u->contador;
            
        }  
        
        $query_visiones_totales = DB::select('select sum(views) contador from videos where active = 1 and deleted_at is null');
         foreach($query_visiones_totales as $u)
        {
             $resultado['visiones_totales'] = $u->contador;
            
        }          
        
        
        $query_espacio_libre = DB::select('select sum(((100 - uso_disco)/100) * capacidad) contador from nodos');
         foreach($query_espacio_libre as $u)
        {
             $resultado['espacio_libre'] = $u->contador;
            
        }    
        
        $query_likes_totales = DB::select('select sum(likes) contador from videos where active = 1 and deleted_at is null');
         foreach($query_likes_totales as $u)
        {
             $resultado['likes_totales'] = $u->contador;
            
        }    
        
        return response(json_encode($resultado),200);
        
    }
    
    public function getActividad(Request $request,$id,$page)
    {
        
        $limit = 10;
        $offset = ($page -1) * 25;
        
        $sesiones = DB::select('SELECT sesiones.* FROM sesiones where user_id = '.$id.' order by id DESC limit '.$limit.' offset '.$offset);
        $videos_likes = DB::select('SELECT vl.*,v.title title FROM videos_likes vl'
                . ' left join videos v on v.id = vl.video_id '
                . ' where vl.user_id = '.$id.' ORDER BY vl.id DESC '
                . ' limit '.$limit.' offset '.$offset);
        $eventos_likes = DB::select('SELECT el.*,e.title FROM eventos_likes el'
                . ' left join eventos e on e.id = el.evento_id'
                . ' where el.user_id = '.$id.' ORDER BY el.id DESC '
                . ' limit '.$limit.' offset '.$offset);    
        
        $resultado = array();
        $resultado['sesiones'] = $sesiones;
        $resultado['videos_likes'] = $videos_likes;
        $resultado['eventos_likes'] = $eventos_likes;
        
        return response(json_encode($resultado),200);
    }
    
    public function cambiarNombre(Request $request,$id)
    {
        $usuario = User::find($id);
        if(!is_null($usuario))
        {
            if(Auth::user()->id == $id)
            {
                $usuario->username = $request->username;
                $usuario->save();
                return response(json_encode(['status' => 'success', 'message' => 'ok']),200);
                
            }
            else
            {
                return response(json_encode(['status' => 'error', 'message' => 'El usuario autenticado no tiene permisos para modificar a este usuario']),401);
            }
        }
        else
        {
            return response(json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']),404);
        }
    }
    
    public function cambiarAvisos(Request $request,$id)
    {
        $usuario = User::find($id);
        if(!is_null($usuario))
        {
            if(Auth::user()->id == $id)
            {
                $usuario->acepto_avisos_contenido = intval($request->acepto_avisos_contenido);
                $usuario->save();
                return response(json_encode(['status' => 'success', 'message' => 'ok']),200);
                
            }
            else
            {
                return response(json_encode(['status' => 'error', 'message' => 'El usuario autenticado no tiene permisos para modificar a este usuario']),401);
            }
        }
        else
        {
            return response(json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']),404);
        }
    }    
    
   /* public function cambiarEmail(Request $request,$id)
    {
        $usuario = User::find($id);
        if(!is_null($usuario))
        {
            if(Auth::user()->id == $id)
            {
                //Chequear que no existe usuario con ese correo
                $usuarios = User::where('email',$request->email)->first();
                if(!is_null($usuarios))
                {
                    return response(json_encode(['status' => 'error', 'message' => 'El email introducido ya pertenece a un usuario de el sistema']),409);
                }
                
                
                if($request->email != $usuario->email)
                {
                    //Desubscribir en MailChimp
                    $this->mailChimpService->EditarMiembroLista($id_lista_princpal,$usuario->email,[
                        'status' => 'unsubscribed',
                    ]);                  


                    $usuario->email = $request->email;
                    $usuario->save();

                    //Subscribir al nuevo
                    try {
                        $this->mailChimpService->AñadirMiembroLista($id_lista_princpal,$usuario->email,'subscribed');
                        $segmento = $this->mailChimpService->CrearSegmento('a5c228f4d0',$usuario->email,array ($usuario->email));
                        $user = User::find($usuario->id);
                        $user->mailchimp_segment = $segmento['id'];
                        $user->save();                     
                    }
                    catch(Exception $e)
                    {
                        return response(json_encode(['status' => 'error', 'message' => 'No hemos podido procesar tu solicitud']),500);
                    }                
                }

                
                return response(json_encode(['status' => 'success', 'message' => 'ok']),200);
                
            }
            else
            {
                return response(json_encode(['status' => 'error', 'message' => 'El usuario autenticado no tiene permisos para modificar a este usuario']),401);
            }
        }
        else
        {
            return response(json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']),404);
        }
    }    */
}
