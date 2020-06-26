<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Telegram\Bot\Api;
use Illuminate\Support\Facades\Hash;
use App\Notifications\RecuperarContrasenyaNotification;

class LegacyController extends Controller
{
    //
    
    public function store(Request $request)
    {

        $password = "";
        if(isset($request->password))
        {
            $cost = 10;
            $password = Hash::make($request->password);
        }
        $usuario_nuevo = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $password,
            'remember_token' => ' ',
            'active' => 0
            
        ]);

        
        
            
        $telegram = new Api(env('TELEGRAM_TOKEN'));
        $telegram->sendMessage([
        'chat_id' => env('TELEGRAM_ADMIN_GROUP'),
        'text' => "[Nuevo Registro] : ".$request->email."\n"
        ]);         
        
        
        
        $activation = md5("AGUSTINOS_".$request->email);
            
        $usuario_nuevo->activation_code = $activation;    
        $usuario_nuevo->save();
        $usuario_nuevo->notify(new \App\Notifications\RegistroNotification($usuario_nuevo->id));
        
        return json_encode(['message' => "ok"]);

    }
    
    
    
    //
    
    public function recuperarContrasenya($email)
    {
        $errors = array();
        
        if(strlen($email) > 0)
        {
            $activation = md5("AGUSTINOS_RECOVERY_".$email);
            $usuario = \App\User::where('email',$email)->first();
            
            if(!is_null($usuario))
            {
                $usuario->activation_code = $activation;
                $usuario->save();
                
                
                $usuario->notify(new RecuperarContrasenyaNotification($usuario->id));
                return json_encode(['message' => "ok"]);
                
            }
            else
            {
                array_push($errors,"Usuario no encontrado");
            }
            
        }
        else {
            array_push($errors,"Email no válido");
     
        }
        
        return json_encode(['message' => "ko", 'errors' => $errors]);
    }
    
    public function ValidarRecuperarContrasenya($cod)
    {
        $errors = array();
        
        if(strlen($cod) > 0)
        {
            $usuario = \App\User::where('activation_code',$cod)->first();
            if(!is_null($usuario))
            {
                $usuario->activation_code = '';
                $usuario->save();
                return json_encode(['message' => "ok",'id' => $usuario->id]);
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
    
    public function Activar($cod)
    {
        $errors = array();
        
        if(strlen($cod) > 0)
        {
            $usuario = \App\User::where('activation_code',$cod)->first();
            if(!is_null($usuario))
            {
                $usuario->activation_code = '';
                $usuario->active = 1;
                $usuario->save();
                return redirect('https://agustinos.tv/login');
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
        
    
}
