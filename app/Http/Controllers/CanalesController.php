<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Canal;

class CanalesController extends Controller
{
    
    /**
     * @OA\Get(
     *     path="/api/canales",
     *      tags={"Canales"},
     *     summary="Listar canales",
     *     description="Obtener la lista de canales",
     *      @OA\Parameter(
     *          name="page",
     *          description="Número de Página",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista resultado de los canales"
     *     )
     * )
     */
    
    public function index(Request $request){

        if(isset($request->page)) {
            $canales = Canal::where('id', '>=', 1)->with(['Delegacion'])->orderBy('id','ASC')->paginate(25);
            return response(json_encode($canales), 200);
        }
        else
        {
            return response(json_encode(Canales::all()),200);
        }
    }
    
    /**
     * @OA\Get(
     *      path="/api/canales/{id}",
     *      tags={"Canales"},
     *      summary="Obtener un canal",
     *      description="Obtener un canal",
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
     *      @OA\Response(response=404, description="Canal no encontrado"),
     * )
     * */
    
    public function show(Request $request, $id){
        $canal = Canal::where('id',$id)->with(['Delegacion'])->first();
        if(!is_null($canal)) {
            return response(json_encode($canal), 200);
        }
        else{
            return response(json_encode('Canal no encontrado'), 404);
        }


    }

    /**
     * @OA\Post(
     *      path="/api/canales",
     *      tags={"Canales"},
     *      summary="Guardar un canal",
     *      description="Guardar un canal",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/x-www-form-urlencoded",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="nombre",
     *                   description="Nombre de el canal",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="estado",
     *                   description="Estado del canal ['ONLINE','OFFLINE']",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="ruta",
     *                   description="Ruta de la emisión",
     *                   type="string"
     *               ),     
     *               @OA\Property(
     *                   property="delegacion_id",
     *                   description="Id de la delegación",
     *                   type="integer"
     *               ),
     *           )
     *       )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=404, description="Canal no encontrado"),
     * )
     * */
    
    public function store(Request $request)
    {

           $canal =  Canal::create([
                'nombre' => $request->nombre,
                'estado' => $request->estado,
                'ruta' => $request->ruta,
                'delegacion' => $request->delegacion,

            ]);

        return response(json_encode($canal),200);
    }

    /**
     * @OA\Put(
     *      path="/api/canales/{id}",
     *      tags={"Canales"},
     *      summary="Actualizar un canal",
     *      description="Actualizar un canal",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/x-www-form-urlencoded",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="nombre",
     *                   description="Nombre de el canal",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="estado",
     *                   description="Estado del canal ['ONLINE','OFFLINE']",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="ruta",
     *                   description="Ruta de la emisión",
     *                   type="string"
     *               ),     
     *               @OA\Property(
     *                   property="delegacion_id",
     *                   description="Id de la delegación",
     *                   type="integer"
     *               ),
     *           )
     *       )
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     * )
     * */
    
    public function update(Request $request,$id)
    {
        $canal = Canal::find($id);
        if(!is_null($canal)) {


            $canal->fill([
                'nombre' => $request->nombre,
                'estado' => $request->estado,
                'ruta' => $request->ruta,
                'delegacion' => $request->delegacion,

            ]);


            $canal->save();
            return response(json_encode($canal), 200);

        }
        else{
            return response(json_encode('Canal no encontrado'), 404);
        }
    }

    
    /**
     * @OA\Delete(
     *      path="/api/canales",
     *      tags={"Canales"},
     *      summary="Eliminar un canal",
     *      description="Eliminar un canal",
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
     *     @OA\Response(response=404, description="Canal no encontrado"),
     * )
     * */

    
    public function destroy($id){
        $canal = Canal::find($id);
        if(!is_null($canal)) {
                $canal->delete();
                return response(json_encode('Canal eliminado'),200);

        }
        else {
            return response(json_encode('Canal no encontrado'),404);
        }



    }
    
    
}