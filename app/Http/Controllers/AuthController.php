<?php
namespace App\Http\Controllers;

use App\Notifications\RecuperarContrasenyaNotification;
use App\RecuperarContrasenya;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use App\Sesiones;
use Telegram\Bot\Api;
use App\Services\MailChimpService;
class AuthController extends Controller
{
    
    /**
     * @OA\Post(
     *      path="/api/auth/register",
     *      tags={"Autenticación"},
     *      summary="Registra un nuevo usuario",
     *      description="Registra un nuevo usuario",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/x-www-form-urlencoded",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="email",
     *                   description="Email del usuario",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="password",                 
     *                   description="Contraseña del usuario",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="username",
     *                   description="Nombre de Usuario",
     *                   type="string"
     *               ), 
     *               @OA\Property(
     *                   property="acepto_avisos_contenido",
     *                   description="Acepta el envío de notificaciones y emails sobre cambios en el contenido de Agustinos.TV (notificaciones push por ej)",
     *                   type="string"
     *               ),              
     *           )
     *       )
     *   ),
     *      @OA\Response(
     *          response=201,
     *          description="Contraseña modificada con éxito"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="El token no es válido o ha caducado"
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Usuario no encontrado"
     *       ),
     * )
     * */    
    
    public function __construct(MailChimpService $mailChipService)
    {
        $this->mailChimpService = $mailChipService;
    }
    
    
    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'username' => 'required|min:3',
            'password'  => 'required|min:3',
        ]);
              

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        
         $emailChecker = new \Tintnaingwin\EmailCheckerPHP\EmailChecker();
         $existe =  $emailChecker->check($request->email);
         
         if (strpos($request->email, 'gmail.com') === false || strpos($request->email, 'hotmail.es') === false || strpos($request->email, 'hotmail.com') === false )
         {
             $existe = true;
         }
         
         if($existe == true)
         {
            $user = new User;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->role = 'USUARIO';
            $user->active = 0;
            $user->save();

            /*$telegram = new Api(env('TELEGRAM_TOKEN'));
            $telegram->sendMessage([
            'chat_id' => env('TELEGRAM_ADMIN_GROUP'),
            'text' => "[Nuevo Registro] : ".$request->email."\n"
            ]); */   



            $activation = md5("AGUSTINOS_".$request->email);
            

            $user->activation_code = $activation;    
            $user->save();
            $user = User::find($user->id);

            try {
            $this->mailChimpService->AñadirMiembroLista('a5c228f4d0',$request->email,'subscribed');
            $segmento = $this->mailChimpService->CrearSegmento('a5c228f4d0',$request->email,array ($request->email));
            
            $user->mailchimp_segment = $segmento['id'];
            $user->save();                        
            

            
            
            //$user->save();
            //$user->notify(new \App\Notifications\RegistroNotification($user->id));     
            
            $campanya = $this->mailChimpService->CrearCampanyas('regular','Activar Cuenta',$user->mailchimp_segment);                  
            $contenido = $this->mailChimpService->SubirContenidoCampanya($campanya,'nuevo_registro',array('activation' => $user->activation_code));                   
            $envio = $this->mailChimpService->EnviarCampanya($campanya);
            //return $envio;                
            //$user->notify(new \App\Notifications\RegistroNotification($user->id));   
            }
            catch(Exception $e)
            {
                echo $e->getMessage();
                $user->notify(new \App\Notifications\RegistroNotification($user->id));  
            } 
            
            //$user->notify(new \App\Notifications\RegistroNotification($user->id));  

            return response()->json(['status' => 'success'], 200);
         }
         else
         {
            return response()->json(['message' => 'No podemos verificar que tu correo exista', 'status' => $existe], 404);
         }
    }
    
    
    /**
     * @OA\Get(
     *      path="/api/auth/ask-activate/{email}",
     *      tags={"Autenticación"},
     *      summary="Reenviar Activación",
     *      description="Reenviar Activación",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/x-www-form-urlencoded",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="email",
     *                   description="Email del usuario",
     *                   type="string"
     *               ),          
     *           )
     *       )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Notificación enviada"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Usuario no encontrado"
     *       ),
     *      @OA\Response(
     *          response=409,
     *          description="Cuenta ya activada"
     *       ),
     * )
     * */   
    
    public function PedirActivacion($email)
    {
        
        $user = null;
        if($email != null)
        {
            $user = User::where('email',$email)->first();
        }
        if(!is_null($user))
        {
            if($user->active == 0)
            {
                $campanya = $this->mailChimpService->CrearCampanyas('regular','Activar Cuenta',$user->mailchimp_segment);                  
                $contenido = $this->mailChimpService->SubirContenidoCampanya($campanya,'nuevo_registro',array('activation' => $user->activation_code));                   
                $envio = $this->mailChimpService->EnviarCampanya($campanya);
                //return $envio;                
                //$user->notify(new \App\Notifications\RegistroNotification($user->id)); 
                return response(json_encode('Notificación enviada'),200);
            }
            else
            {
                return response(json_encode('Cuenta ya activada'),409);
            }
            
        }
        else
        {
            return response(json_encode('Usuario no encontrado'),404);
        }
    }
    
    /**
     * @OA\Post(
     *      path="/api/auth/login",
     *      tags={"Autenticación"},
     *      summary="Login",
     *      description="Login",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/x-www-form-urlencoded",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="email",
     *                   description="Email de Usuario a autenticar",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="password",
     *                   description="Contraseña del Usuario a autenticar",
     *                   type="string"
     *               ),
     *           )
     *       )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Usuario autenticado correctamente"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Usuario no encontrado"
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Usuario no activado | Datos incorrectos (Unauthorized)"
     *       ),
     * )
     * */

    
    public function login(Request $request)
    {

        $credentials = request(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {     
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        
        $cerrar_anteriores = \App\Sesiones::where('user_id',auth('api')->user()->id)->where('active',1)->get();
        foreach($cerrar_anteriores as $anteriores)
        {
            $sesion_anterior = \App\Sesiones::find($anteriores['id']);
            $sesion_anterior->active = 0;
            $sesion_anterior->token = null;
            $sesion_anterior->save();
        }
        
        $sesion = \App\Sesiones::create([
            'user_id' => auth('api')->user()->id,
            'token' => $token,
            'active' => 1,
            'isMobile' => $this->CalculateIsMobile(),
            'ip' => $this->getIp(),
            'webBrowserData' => $request->header('User-Agent'),
        ]);
        
        $sesion = Sesiones::find($sesion->id);
        $sesion->isMacOs = $this->IsMacOs(auth('api')->user()->id);
        $sesion->save();
        
        


        return $this->respondWithToken($token);

    }
    
    private function IsMacOs($user_id)
    {
            $sesion = \App\Sesiones::where('user_id',$user_id)->where('active',1)->first();
            $es_macos = false;
            if(!is_null($sesion))
            {
                $webBrowserData = $sesion->webBrowserData;
                $es_macos = strstr($webBrowserData,'Mac OS'); 
                $es_ios = strstr($webBrowserData,'iOS');
                $es_macos = $es_macos && !$es_ios;
            }
             
            return $es_macos;
      
    }

    protected function respondWithToken($token)
    {
        $user = auth('api')->user();
        
        if($user->active == 0)
        {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        

        return response()->json([
            'access_token' => $token,
            'user_id' => $user->id,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ])->header('Authorization', $token);
    }
    
    /**
     * @OA\Post(
     *      path="/api/auth/logout",
     *      tags={"Autenticación"},
     *      summary="Logout",
     *      description="Logout",
     *      @OA\Response(
     *          response=200,
     *          description="Usuario desconectado correctamente"
     *       ),
     * )
     * */    

    public function logout()
    {

        $user = auth('api')->user();

        $sesion = \App\Sesiones::where('user_id',$user->id)->where('active',1)->first();
        if(!is_null($sesion))
        {
            $sesion->token = null;
            $sesion->active = 0;
            $sesion->save();
        }
        
        $this->guard()->logout();
        return response()->json([
            'status' => 'success',
            'msg' => 'Logged out Successfully.'
        ], 200);

    }
    
    /**
     * @OA\Get(
     *      path="/api/auth/user",
     *      tags={"Autenticación"},
     *      summary="Obtener el usuario conectado",
     *      description="Obtener el usuario conectado",
     *      @OA\Response(
     *          response=200,
     *          description="Objeto del usuario conectado"
     *       ),
     *      @OA\Response(response=404, description="Usuario no encontrado"),
     * )
     * */
    
    public function user(Request $request)
    {

        $token = null;
        if(isset($request->server->getHeaders()['AUTHORIZATION']) && strlen($request->server->getHeaders()['AUTHORIZATION']) > 0)
        {
            $aux = explode(" ", $request->server->getHeaders()['AUTHORIZATION']);
            $token = $aux[1];
        }
        
        $user = User::find(Auth::user()->id);
        $sesion_activa = \App\Sesiones::where('user_id',$user->id)->where('token',$token)->where('active',1)->first();
        
        if(!is_null($user) && !is_null($sesion_activa))
        {
          

            return response(json_encode($user),200);
        }
        else
        {
            return response("Sesión Expirada | Usuario no válido",401);
        }


    }
    

    /**
     * @OA\Post(
     *      path="/api/auth/password-recovery/ask",
     *      tags={"Autenticación"},
     *      summary="Pedir recuperación de contraseña",
     *      description="Pedir recuperación de contraseña",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/x-www-form-urlencoded",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="email",
     *                   description="Email del usuario",
     *                   type="string"
     *               ),
     *           )
     *       )
     *   ),
     *      @OA\Response(
     *          response=201,
     *          description="Petición enviada"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Usuario no encontrado"
     *       ),
     * )
     * */    

    public function PeticionRecuperarContrasenya()
    {
        $email = Input::post('email');
        
        if(strlen($email) > 0)
        {
            

                $activation = md5("AGUSTINOS_RECOVERY_".$email);
                $usuario = \App\User::where('email',$email)->first();

                if(!is_null($usuario))
                {
                    $usuario->activation_code = $activation;
                    $usuario->save();

                    $campanya = $this->mailChimpService->CrearCampanyas('regular','Recuperar Contraseña',$usuario->mailchimp_segment);                  
                    $contenido = $this->mailChimpService->SubirContenidoCampanya($campanya,'recuperar_contrasenya',array('activation' => $activation));                   
                    $envio = $this->mailChimpService->EnviarCampanya($campanya);
                    return response(json_encode('Notificación enviada'),200);
                }    
                else
                {
                     return response("Usuario no encontradoo",404);
                }                   
 
        }
        else {
            return response("Usuario no encontrado",404);
     
        }
        

    }
    
    /**
     * @OA\Post(
     *      path="/api/auth/password-recovery/update",
     *      tags={"Autenticación"},
     *      summary="Realizar cambio de contraseña",
     *      description="Realizar cambio de contraseña",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/x-www-form-urlencoded",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="token",
     *                   description="Token de la Petición de cambio de contraseña",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="password",
     *                   description="Nueva contraseña",
     *                   type="string"
     *               ),
     *           )
     *       )
     *   ),
     *      @OA\Response(
     *          response=201,
     *          description="Contraseña modificada con éxito"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="El token no es válido o ha caducado"
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Usuario no encontrado"
     *       ),
     *      @OA\Response(
     *          response=409,
     *          description="Faltan datos"
     *       ),     
     * )
     * */    

    public function ActualizarContrasenya()
    {
        
        $token= Input::post('token');
        $password = Input::post('password');
        $errors = array();
        
        if(strlen($token) > 0 && strlen($password) > 0)
        {
            $usuario = \App\User::where('activation_code',$token)->first();
            if(!is_null($usuario))
            {
                $usuario->activation_code = null;
                $usuario->password = bcrypt($password);
                $usuario->save();
                return response(json_encode("Contraseña modificada con éxito"),200);
            }
            else
            {
                return response(json_encode("El token no es válido"),404);
            }
            

            
        }
        else {
                return response(json_encode("Faltan datos"),409);
        }
        
    }
    
    /**
     * @OA\Post(
     *      path="/api/auth/activate/{token}",
     *      tags={"Autenticación"},
     *      summary="Activar una cuenta",
     *      description="Activar una cuenta",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/x-www-form-urlencoded",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="token",
     *                   description="Email de Usuario a autenticar",
     *                   type="string"
     *               ),
     *           )
     *       )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Ok"
     *       ),
     * )
     * */    
    
    public function Activar($token)
    {
        $errors = array();
        
        if(strlen($token) > 0)
        {
            $usuario = \App\User::where('activation_code',$token)->first();
            if(!is_null($usuario))
            {
                $usuario->activation_code = '';
                $usuario->active = 1;
                $usuario->save();
                echo "CUENTA ACTIVADA CON ÉXITO ...</br>";
                return redirect('https://agustinos.tv/#/auth/login?activado=1');
            }
            else
            {
                array_push($errors,"El código no es válido");
            }
            

            
        }
        else {
            array_push($errors,"Es necesario introducir un código");
     
        }
        
        return json_encode(['message' => "ko", 'errors' => $errors]);
    }      
        
        
    
    /**
     * @OA\Get(
     *     path="/api/auth/refresh",
     *      tags={"Autenticación"},
     *     summary="Refresca el token de sesión",
     *     description="Refresca el token de sesión",
     *     @OA\Response(
     *         response=200,
     *         description="Token refrescado con éxito"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token no válido o expirado"
     *     )
     * )
     */    

    public function refresh()
    {

        if ($token = $this->guard()->refresh()) {
            return response()
                ->json(['status' => 'successs','access_token' => $token], 200)
                ->header('Authorization', $token);
        }
        return response()->json(['error' => 'refresh_token_error'], 401);

    }
    private function guard()
    {
        return Auth::guard('api');
    }
    
    /**
     * @OA\Get(
     *     path="/api/auth/isMobile",
     *      tags={"Autenticación"},
     *     summary="Detecta si el dispositivo es un mobil o tablet",
     *     description="Detecta si el dispositivo es un mobil o tablet. Es obligatorio llamar a este método con sesión de usuario abierta",
     *     @OA\Response(
     *         response=200,
     *         description="Booleano resultado del diagnóstico"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
     */    
    
    
    private function CalculateIsMobile()
    {
        $agent = new Agent();
        $resupuesta = false;
        if( $agent->isMobile() )
        {
            $resupuesta  = true;
        }
        if( $agent->isTablet() )
        {
            $resupuesta  = true;
        }       
        if($agent->is('iPhone'))
        {
           $resupuesta  = true; 
        }
        if($agent->is('OS X'))
        {
            $resupuesta  = true;
        }
        return $resupuesta;
        
    }
    
    /**
     * @OA\Post(
     *      path="/api/auth/isMobile",
     *      tags={"Autenticación"},
     *      summary="Comprobar si la sesión iniciada está en un dispositivo móvil",
     *      description="Comprobar si la sesión iniciada está en un dispositivo móvil",
     *   
     *      @OA\Response(
     *          response=200,
     *          description="Usuario autenticado correctamente"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Usuario no encontrado"
     *       ),
     * )
     * */
    
    public function isMobile()
    {
        $user = User::find(Auth::id());
        if(!is_null($user))
        {
            $resupuesta = $this->CalculateIsMobile();
            

            if($this->IsMacOs($user->id) == true) 
            {
                $resuesta = false;
            }
            return response(json_encode(['is_mobile' => $resupuesta]),200);
        }
        else
        {
            return response(json_encode("Usuario no encontrado"),404);
        }
        
            
    
        
    }
    
    private function getIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
    }    
    
    public function AutenticaRTMP(Request $request)
    {

        $delegacion = Input::get('delegacion');
        
        $password = Input::get('password');
        
        if(intval($delegacion) == 1 && $password == "RAStv19")
        {
            $user = User::find(1);
            $user->notify(new RecuperarContrasenyaNotification($usuario->id));
            return response('ok',201);
        }
        else
        {
          return response('error',404);  
        }
    }
}
