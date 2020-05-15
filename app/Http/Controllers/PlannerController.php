<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Planner;

class PlannerController extends Controller
{
    
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
                        $usuarios = Planner::with($with)->where($filtro)->with($width)->orderBy('id',$request->order)->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                                                 
                    }
                    else
                    {
                        $usuarios = Planner::with($with)->where($filtro)->with($width)->orderBy('id',$request->order)->paginate(25);
                        return response(json_encode($usuarios), 200);                                               
                    }
                                     
                }
                else
                {
                    if(isset($request->limite))
                    {
                        $usuarios = Planner::with($with)->where($filtro)->with($width)->orderBy('id','DESC')->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                         
                        
                    }
                    else
                    {
                        $usuarios = Planner::with($with)->where($filtro)->with($width)->orderBy('id','DESC')->paginate(25);
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
                        $usuarios = Planner::where($filtro)->orderBy('id',$request->order)->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                                                 
                    }
                    else
                    {
                        $usuarios = Planner::where($filtro)->orderBy('id',$request->order)->paginate(25);
                        return response(json_encode($usuarios), 200);                                               
                    }
                                     
                }
                else
                {
                    if(isset($request->limite))
                    {
                        $usuarios = Planner::where($filtro)->orderBy('id','DESC')->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                         
                        
                    }
                    else
                    {
                        $usuarios = Planner::where($filtro)->orderBy('id','DESC')->paginate(25);
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
                        $usuarios = Planner::where('id','>=',1)->orderBy('id',$request->order)->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                                                 
                    }
                    else
                    {
                        $usuarios = Planner::where('id','>=',1)->orderBy('id',$request->order)->paginate(25);
                        return response(json_encode($usuarios), 200);                                               
                    }
                                     
                }
                else
                {
                    if(isset($request->limite))
                    {
                        $usuarios = Planner::where('id','>=',1)->orderBy('id','DESC')->paginate($request->limite);
                        return response(json_encode($usuarios), 200);                         
                        
                    }
                    else
                    {
                        $usuarios = Planner::where('id','>=',1)->orderBy('id','DESC')->paginate(25);
                        return response(json_encode($usuarios), 200);                         
                    }  
                }                
                
            }

        }
    }
    else
    {
        return response(json_encode(Planner::all()),200);
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
        
    //
}
