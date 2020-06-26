<?php

namespace App\Http\Controllers\Imagenes;

use Illuminate\Http\Request;
use League\Glide\ServerFactory;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Filesystem\Filesystem;
use League\Glide\Responses\LaravelResponseFactory;

class ImagenesDelegacionController extends Controller
{
    public function __invoke($d){
        
        $delegacion = \App\Delegacion::find($d);
        if(!is_null($delegacion))
        {
            $dir = '';
            $path = $delegacion->imagen;
            $filesystem = app(Filesystem::class);
            $driver = $filesystem->getDriver();
            $adapter = $driver->getAdapter();
            $adapter->setPathPrefix('/home/imagenes/delegaciones/'.$delegacion->id.'/');
            $server = ServerFactory::create(
                            [
                            'response' => new LaravelResponseFactory(app('request')),
                            'source' => $filesystem->getDriver(),
                            'cache' => $filesystem->getDriver(),
                            'source_path_prefix' => '/' . $dir,
                            'cache_path_prefix' => '/.cache',
                            'base_url' => '/glide/'
                            ]);
            return $server->getImageResponse($path, request()->all());                  
        }
        else
        {
            return response(json_encode('Delegación no encontrado'),404);
        }

        
    }

}
