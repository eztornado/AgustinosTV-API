<?php

namespace App\Console\Commands;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Console\Command;
use Telegram\Bot\Api;
use GuzzleHttp\Client;

class RenderizarVideo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'planificador:renderizar_video';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $actual = \App\Planner::where('accion','RENDERIZAR_VIDEO')->where('estado','ESPERA')->orderBy('id','ASC')->first();
        
        if(!is_null($actual))
        {
            $this->renderizarVideo($actual);
            
        }
        //
    }
    
    private function renderizarVideo($actual)
    {
        
        $nodo = \App\Nodo::find(env('NODO_ACTUAL'));
        if(!is_null($nodo))
        {
        
            echo "RENDERIZANDO_VIDEO : ".$actual->id." \n";
            
            $telegram = new Api(env('TELEGRAM_TOKEN'));
            $actual->estado = 'EJECUTANDO';
            $actual->save();
            

            echo "id video : ".$actual->id_video."\n";
            $video = \App\Video::where('id',$actual->id_video)->first();
            
            $filename = $video->rutaf_video;
            $buffer =  $video->video_nombre;
            
            $telegram->sendMessage([
            'chat_id' => env('TELEGRAM_ADMIN_GROUP'),
            'text' => "[Aviso] - ".$nodo->url." > Comenzando renderizado de vídeo : ".$buffer."\n"
            ]);               



            if($actual->es_pendiente == 0)
            {

                
                //RENDERIZADO A 1080P
                $process = new Process('ffmpeg -i '.$nodo->ruta_videos_originales.$filename.' -acodec copy -c:v libx264 -preset fast -profile:v baseline -vsync cfr -s 1920x1080 -b:v 16000k -maxrate 16000k -bufsize 4000k -threads 8 -r 60 -f mp4 '.$nodo->ruta_videos_src.$buffer.'_720p.mp4');
                $process->setTimeout(86400);
                $process->run();

                // executes after the command finishes
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }

                echo $process->getOutput(); 
                
                //RENDERIZADO A 720P
                $process = new Process('ffmpeg -i '.$nodo->ruta_videos_originales.$filename.' -acodec copy -c:v libx264 -preset fast -profile:v baseline -vsync cfr -s 640x360 -b:v 8000k -maxrate 8000k -bufsize 8000k -threads 8 -r 30 -f mp4 '.$nodo->ruta_videos_src.$buffer.'_360p.mp4');
                $process->setTimeout(86400);
                $process->run();

                // executes after the command finishes
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }

                echo $process->getOutput();                 
                
            }
            else
            {

                $process = new Process('ffmpeg -i /home/video_recordings/'.$filename.' -acodec copy -c:v libx264 -preset fast -profile:v baseline -vsync cfr -s 1920x1080 -b:v 16000k -maxrate 16000k -bufsize 4000k -threads 8 -r 60 -f mp4 /home/video_src/'.$buffer.'_720p.mp4');
                $process->setTimeout(86400);
                $process->run();

                // executes after the command finishes
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }

                echo $process->getOutput(); 

                //RENDERIZADO A 720P
                $process = new Process('ffmpeg -i /home/video_recordings/'.$filename.' -acodec copy -c:v libx264 -preset fast -profile:v baseline -vsync cfr -s 640x360 -b:v 8000k -maxrate 8000k -bufsize 8000k -threads 8 -r 30 -f mp4 /home/video_src/'.$buffer.'_360p.mp4');
                $process->setTimeout(86400);
                $process->run();

                // executes after the command finishes
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }

                echo $process->getOutput();                   
            }

            
            $video->estado = 'COMPLETED';
            
            $video->save();
            
            //$this->videos_model->completarVideo($id_video,$title,$categoria,$acceso,$details);        
           
            $telegram->sendMessage([
            'chat_id' => env('TELEGRAM_ADMIN_GROUP'),
            'text' => "[Aviso] ".$nodo->url." > Renderizado del vídeo ".$buffer." finalizado \n"
            ]);                
            
           //$this->planificador_model->finalizarTarea($idPlanificador);
            $actual->estado = 'FINALIZADO';
            $actual->save();
            
        }     
        else
        {
            echo "No se encuentra el nodo \n";
        }
    }
}
