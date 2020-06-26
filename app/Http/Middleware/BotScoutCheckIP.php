<?php

namespace App\Http\Middleware;

use App\BotScoutResult;
use App\Services\TornadoCoreService;
use Closure;
use BotScout;
use Telegram\Bot\Api;
use DB;
use Illuminate\Support\Facades\Log;

class BotScoutCheckIP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     *
     */

    public function __construct(TornadoCoreService $tornadoCoreService)
    {
        $this->tornadoCoreService = $tornadoCoreService;
    }

    private function llamarBotScout($ip_usuario)
    {
        $apiquery = "http://botscout.com/test/?ip=".$ip_usuario.'&key='.env('BOTSCOUT_API');
        if(function_exists('file_get_contents')){
            // Use file_get_contents
            $returned_data = file_get_contents($apiquery);
        }else{
            $ch = curl_init($apiquery);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $returned_data = curl_exec($ch);
            curl_close($ch);
        }
        $botdata = explode('|', $returned_data);

        $res = 0;
        if($botdata[0] == 'Y') $res = 1;

        return $res;
    }

    public function handle($request, Closure $next)
    {

        $ip_usuario = $this->getIp();


        $this->tornadoCoreService->login();
        $result = $this->tornadoCoreService->checkBSResult($ip_usuario);
        if($result['data'] == false)
        {
            // !!No está en TornadoCore
            // Procesar BotScout aqui
            $resultado = $this->llamarBotScout($ip_usuario);

            //Almacenar Resultado en Local
            BotScoutResult::create([
                'ip' => $ip_usuario,
                'result' => $resultado,
            ]);

            //Enviar Copia a TornadoCore
            $llamada = $this->tornadoCoreService->addBSResult($ip_usuario,$resultado);

            if($resultado == 1)
            {
                return response(json_encode('IP NO PERMITIDA. BOT DETECTADO'),200);
            }


        }
        else
        {
            //Resultado positivo TornadoCore
            return response(json_encode('IP NO PERMITIDA. BOT DETECTADO'),200);

        }

        /*$result = \App\BotScoutResult::where('ip',$ip_usuario)->first();

        if(!is_null($result))
        {
            if($result->result == 1)
            {
                return response(json_encode('IP NO PERMITIDA. BOT DETECTADO'),200);
            }
            else
            {
                $recomprobar = DB::select('SELECT id, TIMESTAMPDIFF(DAY,updated_at, CURRENT_TIMESTAMP()) tiempo  FROM  botScoutResults
                WHERE TIMESTAMPDIFF(DAY,created_at, CURRENT_TIMESTAMP()) AND id = '.$result->id.'
                HAVING tiempo > 10');

                foreach($recomprobar as $r)
                {
                    $apiquery = "http://botscout.com/test/?ip=".$ip_usuario.'&key'.env('BOTSCOUT_API');
                    if(function_exists('file_get_contents')){
                        // Use file_get_contents
                        $returned_data = file_get_contents($apiquery);
                    }else{
                        $ch = curl_init($apiquery);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $returned_data = curl_exec($ch);
                        curl_close($ch);
                    }
                    $botdata = explode('|', $returned_data);

                    $res = 0;
                    if($botdata[0] == 'Y') $res = 1;
                    $bsr = \App\BotScoutResult::find($r->id);
                    $bsr->result = $res;
                    $bsr->save();


                }
            }


        }
        else
        {
            try{





                } catch (Exception $ex) {

                }
        }*/

        return $next($request);
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
}
