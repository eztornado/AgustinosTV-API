<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::prefix('auth')->group(function () {
    Route::group(['middleware' => ['cors']], function () {
        Route::post('register', 'AuthController@register')->middleware('BotScoutCheckIP');
        Route::post('login', 'AuthController@login')->middleware('BotScoutCheckIP');
        Route::post('password-recovery/ask/', 'AuthController@PeticionRecuperarContrasenya')->middleware('BotScoutCheckIP');;
        Route::post('password-recovery/update/', 'AuthController@ActualizarContrasenya')->middleware('BotScoutCheckIP');;
        Route::get('activate/{token}', 'AuthController@Activar')->middleware('BotScoutCheckIP');;
        
        Route::get('ask-activate/{email}', 'AuthController@PedirActivacion')->middleware('BotScoutCheckIP');;
         Route::get('autenticaRTMP', 'AuthController@autenticaRTMP');
        Route::group(['middleware' => 'auth:api'], function(){
            Route::get('user', 'AuthController@user');
            Route::get('refresh', 'AuthController@refresh');
            Route::get('isMobile', 'AuthController@isMobile');
            Route::post('logout', 'AuthController@logout');
        });
    });
});

Route::group(['middleware' => ['auth:api','cors']], function(){

    
    // Users
    Route::get('novedades', 'UserController@getNovedades');
    Route::get('users', 'UserController@index')->middleware('isAdmin');
    Route::get('users/{id}', 'UserController@show')->middleware('isAdminOrSelf');
    Route::post('users', 'UserController@store')->middleware('isAdminOrSelf');
    Route::put('users/{id}', 'UserController@update')->middleware('isAdminOrSelf');
    Route::delete('users/{id}', 'UserController@destroy')->middleware('isAdmin');
    Route::post('users/actividad/{id}/{page}', 'UserController@getActividad')->middleware('isAdminOrSelf');
    Route::post('users/change-name/{id}','UserController@cambiarNombre');
    //Route::post('users/change-email/{id}','UserController@cambiarEmail');
    Route::post('users/change-notifications/{id}','UserController@cambiarAvisos');
    Route::post('users/delete-account/{id}','UserController@eliminarCuenta');
    
    //Nodos
    Route::get('nodos', 'NodosController@index')->middleware('isAdmin');
    Route::get('nodos/{id}', 'NodosController@show')->middleware('isAdmin');
    Route::post('nodos', 'NodosController@store')->middleware('isAdmin');
    Route::put('nodos/{id}', 'NodosController@update')->middleware('isAdmin');
    Route::delete('nodos/{id}', 'NodosController@destroy')->middleware('isAdmin');
    
    //Delegaciones
    Route::get('delegaciones', 'DelegacionController@index')->middleware('isAdmin'); 
    Route::get('delegaciones/{id}', 'DelegacionController@show')->middleware('isAdmin'); 
    Route::post('delegaciones', 'DelegacionController@store')->middleware('isAdmin');
    Route::put('delegaciones/{id}', 'DelegacionController@update')->middleware('isAdmin');
    Route::delete('delegaciones/{id}', 'DelegacionController@destroy')->middleware('isAdmin');
    
    //Videos
    Route::get('ver-video/{id}','VideoController@verVideo');
    Route::get('videos/{id}','VideoController@show')->middleware('isAdmin');
    Route::post('videos/{id}/addview','VideoController@addview');
    Route::post('videos/create','VideoController@store')->middleware('isAdmin');
    Route::post('videos/upload/{id}/{video_upload_title}','VideoController@upload')->middleware('isAdmin');
    Route::post('videos/{id}','VideoController@update')->middleware('isAdmin');
    Route::delete('videos/{id}','VideoController@destroy')->middleware('isAdmin');
    
    //Canales
    Route::get('canales', 'CanalesController@index');
    Route::get('ver-canal/{id}', 'CanalesController@show');
    Route::post('canales', 'CanalesController@store')->middleware('isAdminOrSelf');
    Route::put('canales/{id}', 'CanalesController@update')->middleware('isAdminOrSelf');
    Route::delete('canales/{id}', 'CanalesController@destroy')->middleware('isAdmin');
    Route::get('ver-canal/{id}', 'CanalesController@show');
    
    //Eventos
    Route::get('eventos/', 'EventosController@index');
    Route::post('eventos/ver/{id}', 'EventosController@VerEvento');
    Route::post('eventos/evento_activo_en_canal/{id}', 'EventosController@eventoActivoEnCanal');
    Route::post('eventos', 'EventosController@store')->middleware('isAdmin');
    Route::post('eventos/{id}', 'EventosController@show')->middleware('isAdmin');
    
    //Grabaciones
    Route::post('grabaciones/subir/{id}', 'GrabacionesController@subir');
    
    //Likes
    Route::get('likes/evento/{id_evento}', 'LikesController@getLikesFromEvento');
    Route::get('likes/video/{id_evento}', 'LikesController@getLikesFromVideo');
    Route::get('check-like/evento/{id_evento}', 'LikesController@CheckLikeEvento');
    Route::get('check-like/video/{id_evento}', 'LikesController@CheckLikeVideo');
    Route::post('likes/evento/{id_evento}', 'LikesController@AddLikeToEvento');
    Route::post('likes/video/{id_video}', 'LikesController@AddLikeToVideo');
    Route::delete('likes/evento/{id_evento}', 'LikesController@QuitLikeToEvento');
    Route::delete('likes/video/{id_video}', 'LikesController@QuitLikeToVideo');
    
    

    //Imagenes
    //Route::post('imagenes/delegacion/{id}','Imagenes\ImagenesDelegacionController@store')->middleware('isAdmin');

    //RTMP Controller
    Route::post('rtmp/genteOnline', 'RTMPController@genteOnline');
    Route::post('rtmp/ok', 'RTMPController@Ok');
    
    
    //Planner
    Route::get('planner', 'PlannerController@index')->middleware('isAdmin');
    
    
   
    
   //Vídeos
    Route::get('videos', 'VideoController@index')->middleware('isAdmin');        
    
    //Admin
    Route::get('admin/home','UserController@AdminHomeStats')->middleware('isAdmin');    

        




});

Route::get('v2-legacy/users/activate/{token}', 'AuthController@Activar')->middleware('BotScoutCheckIP');

//Público V3
Route::get('reproducir-video/{id}/{tipo}/{token}','VideoController@reproducir')->middleware('CheckPermissionsVerVideo')->middleware('BotScoutCheckIP');
Route::get('imagenes/video/{id}/{token}','Imagenes\ImagenesVideoController')->middleware('CheckPermissionsVerVideo')->middleware('BotScoutCheckIP');
Route::get('imagenes/delegacion/{id}/{token}','Imagenes\ImagenesDelegacionController')->middleware('BotScoutCheckIP');






    
    

