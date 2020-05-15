<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Sesiones;

class CheckPermissionsVerVideo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $video_id = $request->id;
        $video = \App\Video::find($video_id);
        $token = $request->token;
        $puede_ver = false;
        $usuario_sesion = null;
        if(!is_null($token))
        {
            $sesion = Sesiones::where('token',$token)->where('active',1)->first();
            if(!is_null($sesion))
            {
                $usuario_sesion = User::find($sesion->user_id);
            }
        }
        
        
            if(!is_null($video))
            {
                if($video->access == "GUEST")
                {
                    $puede_ver = true;
                }
                if($video->access == "REGISTERED")
                {
                    $puede_ver = true;

                }
                if($video->access == "PAID")
                {
                    if($usuario_sesion->role == "SUBCRIPCION" ||
                        $usuario_sesion->role == "SUPERVISOR" ||
                        $usuario_sesion->role == "ADMIN" ||
                        $usuario_sesion->role == "ROOT"     
                      )
                        $puede_ver = true;
                }
                if($video->access == "ADMIN")
                {
                    if($usuario_sesion->role == "ADMIN" ||
                        $usuario_sesion->role == "ROOT"     
                      )
                        $puede_ver = true;
                }
                        
                if((!is_null($usuario_sesion) || $video->access == "GUEST") && $puede_ver == true)
                {               
                   return $next($request);
                }
                else
                {
                    return response()->json(['error' => 'Rol de Usuario no autorizado'], 403); 
                }

            }
            else
            {
               return response()->json(['error' => 'Vídeo no encontrado'], 404); 
            }
        }
        
    }

