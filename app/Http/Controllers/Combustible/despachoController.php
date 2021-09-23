<?php

namespace App\Http\Controllers\Combustible;
//*******agregar esta linea******//
use App\Models\Proceso\tab_persona;
use App\Models\Proceso\tab_solicitud;
use App\Models\Configuracion\tab_nacionalidad;
use App\Models\Configuracion\tab_gerencia;
use App\Models\Combustible\tab_vehiculo;
use App\Models\Configuracion\tab_estacion_servicio;
use App\Models\Combustible\tab_despacho;
use App\Models\Combustible\tab_despacho_vehiculo;
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
use Auth;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class despachoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function registrar( Request $request)
    {
        $sortBy = 'id_estacion';
        $orderBy = 'desc';
        $perPage = 5;
        $q = null;
        $columnas = [
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Origen'],
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Destino']
        ];

        if ($request->has('orderBy')){
            $orderBy = $request->query('orderBy');
        }
        if ($request->has('sortBy')){
            $sortBy = $request->query('sortBy');
        } 
        if ($request->has('perPage')){
            $perPage = $request->query('perPage');
        } 
        if ($request->has('q')){
            $q = $request->query('q');
        }        
       

        $tab_despacho = tab_despacho::select( 'combustible.tab_despacho.id', DB::raw("to_char(fecha, 'dd/mm/YYYY') as fecha"), 'de_estacion_servicio',DB::raw("coalesce((select sum(nu_litro) from combustible.tab_despacho_vehiculo where id_tab_despacho = combustible.tab_despacho.id),0) as nu_total_litro"))
        ->join('configuracion.tab_estacion_servicio as t01','t01.id','=','combustible.tab_despacho.id_estacion');

        if(!empty($q)){     
             $tab_despacho = $tab_despacho->where('t01.de_estacion_servicio', 'like','%'.$q.'%');
        }

        $tab_despacho = $tab_despacho->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('combustible.planificacionDespacho')->with([
          'tab_despacho' => $tab_despacho,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }
  
    public function nuevo(Request $request){

         $tab_estacion_servicio = tab_estacion_servicio::select( 'id','de_estacion_servicio')
        ->orderby('id','ASC')
        ->get();     
       
        return View::make('combustible.despacho.nuevo')->with([
           'tab_estacion_servicio' => $tab_estacion_servicio
        ]);
    }

    public function agregarApoyo(Request $request,$id){

        $tab_nacionalidad = tab_nacionalidad::select( 'id','de_nacionalidad')
        ->orderby('id','ASC')
        ->get();     
              
        return View::make('combustible.apoyo.agregarApoyo')->with([
           'tab_nacionalidad' => $tab_nacionalidad,
           'id' => $id
        ]);  
       
      
    }

    public function agregarGerencia(Request $request,$id){

         $tab_gerencia = tab_gerencia::select( 'id','de_gerencia')
        ->orderby('de_gerencia','ASC')
        ->get();     
       
        return View::make('combustible.despacho.agregarGerencia')->with([
           'tab_gerencia' => $tab_gerencia,
           'id' => $id
        ]);
    }

     /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function detalleVehiculo( Request $request,$id_gerencia,$id)
    {
        $sortBy = 'cedula';
        $orderBy = 'desc';
        $perPage = 5;
        $q = null;
        $columnas = [
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Origen'],
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Destino']
        ];

        if ($request->has('orderBy')){
            $orderBy = $request->query('orderBy');
        }
        if ($request->has('sortBy')){
            $sortBy = $request->query('sortBy');
        } 
        if ($request->has('perPage')){
            $perPage = $request->query('perPage');
        } 
        if ($request->has('q')){
            $q = $request->query('q');
        }        
       

        $tab_gerencia = tab_gerencia::where('id', '=',$id_gerencia)->first();

        $tab_vehiculo = tab_vehiculo::select( 't02.id as id_despacho','t01.id as id_propietario', 'cedula', 'nombres', 'apellidos','id_nacionalidad','combustible.tab_vehiculo.id as id_vehiculo', 'de_placa', 'de_modelo','de_marca','de_color','nu_litro')
        ->join('telemedicina.tab_persona as t01','t01.id','=','combustible.tab_vehiculo.id_propietario')
        ->join('combustible.tab_despacho_vehiculo as t02','t02.id_vehiculo','=','combustible.tab_vehiculo.id')
        ->where('t02.id_tab_despacho','=',$id)
        ->where('t02.id_gerencia','=',$id_gerencia);
       

        if(!empty($q)){

            if(is_numeric($q)){
                    if (tab_persona::where('cedula', '=', $q)->exists()) {
                       $tab_vehiculo = $tab_vehiculo->where('t01.cedula', '=',$q);
                    }   
            }else{
                
                 $tab_vehiculo = $tab_vehiculo->where('combustible.tab_vehiculo.de_placa', 'like', "%".strtoupper($q)."%");
                
            }
        }

        $tab_vehiculo = $tab_vehiculo->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('combustible.despacho.detalleVehiculo')->with([
          'tab_vehiculo' => $tab_vehiculo,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'gerencia' => $tab_gerencia->de_gerencia,
          'q' => $q,
          'id' => $id
        ]);
    }

    public function listaGerencia(Request $request,$id){

        $sortBy = 'de_gerencia';
        $orderBy = 'desc';
        $perPage = 5;
        $q = null;
        $columnas = [
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Origen'],
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Destino']
        ];

        if ($request->has('orderBy')){
            $orderBy = $request->query('orderBy');
        }
        if ($request->has('sortBy')){
            $sortBy = $request->query('sortBy');
        } 
        if ($request->has('perPage')){
            $perPage = $request->query('perPage');
        } 
        if ($request->has('q')){
            $q = $request->query('q');
        }        



        $tab_gerencia = tab_gerencia::select( 'configuracion.tab_gerencia.id', 'de_gerencia',DB::raw("sum(nu_litro) as nu_litro"))
        ->join('combustible.tab_despacho_vehiculo as t01','t01.id_gerencia','=','configuracion.tab_gerencia.id')
        ->distinct()
        ->groupBy('configuracion.tab_gerencia.id', 'de_gerencia')
        ->where('t01.id_tab_despacho','=',$id);

        if(!empty($q)){     
             $tab_gerencia = $tab_gerencia->where('de_gerencia', 'like','%'.$q.'%');
        }

        $tab_gerencia = $tab_gerencia->orderBy($sortBy, $orderBy)
        ->paginate($perPage);



        return View::make('combustible.despacho.listaGerencia')->with([
          'tab_gerencia' => $tab_gerencia,
          'id' => $id,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }

        /**
    * Update the specified resource in storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function guardar( Request $request, $id = NULL )
    {
        DB::beginTransaction();      
  
            try {

               // $validator= Validator::make($request->all(), tab_solicitud::$validarEditar);

                //if ($validator->fails()){
                //    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                //}

                if(!empty($request->id_despacho)){
                    $tab_despacho = tab_despacho::find($request->id_despacho);
                }else{
                    $tab_despacho = new tab_despacho;
                }

                            
                $tab_despacho->id_estacion          = strtoupper($request->estacion_servicio);
                $tab_despacho->id_usuario           = Auth::user()->id; 
                $tab_despacho->fecha                = $request->fecha; 
                $tab_despacho->save();

              
                DB::commit();
                
                Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
                return Redirect::to('/combustible/despacho/registrar');

            }catch (\Illuminate\Database\QueryException $e){
                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());
            }
  
       
    }

         /**
    * Update the specified resource in storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function guardarApoyo( Request $request, $id = NULL )
    {
        DB::beginTransaction();      
  
            try {

               // $validator= Validator::make($request->all(), tab_solicitud::$validarEditar);

                //if ($validator->fails()){
                //    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                //}

                if(!empty($request->id_propietario)){
                    $tab_persona = tab_persona::find($request->id_propietario);
                }else{
                    $tab_persona = new tab_persona;
                }


                $tab_persona->cedula          = $request->cedula;
                $tab_persona->nombres         = $request->nombres;
                $tab_persona->apellidos       = $request->apellido;
                $tab_persona->id_nacionalidad = $request->nacionalidad;
                $tab_persona->id_gerencia     = $request->gerencia;
                $tab_persona->save();

                if(!empty($request->id_vehiculo)){
                    $tab_vehiculo = tab_vehiculo::find($request->id_vehiculo);
                }else{
                    $tab_vehiculo = new tab_vehiculo;
                }

             
                $tab_vehiculo->de_placa                = strtoupper($request->placa);
                $tab_vehiculo->id_usuario              = Auth::user()->id; 
                $tab_vehiculo->de_marca                = $request->marca; 
                $tab_vehiculo->de_modelo               = $request->modelo; 
                $tab_vehiculo->de_color                = $request->color;
                $tab_vehiculo->id_propietario          = $tab_persona->id;
                $tab_vehiculo->save();


                $tab_despacho_vehiculo = new tab_despacho_vehiculo;
                $tab_despacho_vehiculo->id_vehiculo             = $tab_vehiculo->id;
                $tab_despacho_vehiculo->id_usuario              = Auth::user()->id; 
                $tab_despacho_vehiculo->id_tab_despacho         = $request->id_tab_despacho; 
                $tab_despacho_vehiculo->id_gerencia             = 1; 
                $tab_despacho_vehiculo->nu_litro                = $request->litro; 
                $tab_despacho_vehiculo->save();
              
               
                

                DB::commit();
                
                Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
                return Redirect::to('/combustible/despacho/listaApoyo/'.$request->id_tab_despacho);

            }catch (\Illuminate\Database\QueryException $e){
                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());
            }
  
       
    }

    public function guardarDespachoGerencia( Request $request)
    {
        DB::beginTransaction();      
  
            try {

               // $validator= Validator::make($request->all(), tab_solicitud::$validarEditar);

                //if ($validator->fails()){
                //    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                //}

                $tab_vehiculo = tab_vehiculo::select('combustible.tab_vehiculo.id')
                ->join('telemedicina.tab_persona as t01','t01.id','=','combustible.tab_vehiculo.id_propietario')
                ->where('t01.id_gerencia','=',$request->gerencia)
                ->get();

                foreach($tab_vehiculo as $key => $value){
                    $tab_despacho_vehiculo = new tab_despacho_vehiculo;
                    $tab_despacho_vehiculo->id_vehiculo             = $value->id;
                    $tab_despacho_vehiculo->id_usuario              = Auth::user()->id; 
                    $tab_despacho_vehiculo->id_tab_despacho         = $request->id_tab_despacho; 
                    $tab_despacho_vehiculo->id_gerencia             = $request->gerencia; 
                    $tab_despacho_vehiculo->nu_litro                = $request->litro; 
                    $tab_despacho_vehiculo->save();
                }
              
                DB::commit();
                
                Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
                return Redirect::to('/combustible/despacho/listaGerencia/'.$request->id_tab_despacho);

            }catch (\Illuminate\Database\QueryException $e){
                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());
            }
  
       
    }

    

    public function buscar(Request $request)
    {

      if (tab_vehiculo::where('de_placa', '=', strtoupper($request->placa))
      ->exists()) {

        $tab_vehiculo = tab_vehiculo::select( 't01.id as id_propietario', 'cedula', 'nombres', 'apellidos','id_gerencia','id_nacionalidad','combustible.tab_vehiculo.id as id_vehiculo', 'de_placa', 'de_modelo','de_marca','de_color')
        ->join('telemedicina.tab_persona as t01','t01.id','=','combustible.tab_vehiculo.id_propietario')
        ->where('de_placa', '=', strtoupper($request->placa))->first()->toArray();

        $response['success']  = 'true';
        $response['data']  = $tab_vehiculo;

        return Response::json($response, 200);

      }else{

        $response['success']  = 'false';
        $response['data']  = '';
        $response['msg']  = '';

        return Response::json($response, 200);

      }

    } 

    public function eliminar( Request $request)
    {
      DB::beginTransaction();
      try {


        $tabla = tab_despacho_vehiculo::find($request->get("id"));

        $id_gerencia = $tabla->id_gerencia; 
        $id_tab_despacho = $tabla->id_tab_despacho; 


        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
        return Redirect::to('/combustible/despacho/detalleVehiculo'.'/'.$id_gerencia.'/'.$id_tab_despacho);

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }

    public function eliminarGerencia( Request $request)
    {
      DB::beginTransaction();
      try {

        list($id_gerencia,$id_tab_despacho) = explode("-", $request->get("id"));

        $tabla = tab_despacho_vehiculo::where("id_gerencia","=",$id_gerencia)
        ->where("id_tab_despacho","=",$id_tab_despacho);
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
        return Redirect::to('/combustible/despacho/listaGerencia'.'/'.$id_tab_despacho);

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }


    /*************************Apoyo*************************************************/

    public function apoyo( Request $request)
    {
        $sortBy = 'id_estacion';
        $orderBy = 'desc';
        $perPage = 5;
        $q = null;
        $columnas = [
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Origen'],
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Destino']
        ];

        if ($request->has('orderBy')){
            $orderBy = $request->query('orderBy');
        }
        if ($request->has('sortBy')){
            $sortBy = $request->query('sortBy');
        } 
        if ($request->has('perPage')){
            $perPage = $request->query('perPage');
        } 
        if ($request->has('q')){
            $q = $request->query('q');
        }        
       

        $tab_despacho = tab_despacho::select( 'combustible.tab_despacho.id', DB::raw("to_char(fecha, 'dd/mm/YYYY') as fecha"), 'de_estacion_servicio',DB::raw("coalesce((select sum(nu_litro) from combustible.tab_despacho_vehiculo where id_tab_despacho = combustible.tab_despacho.id),0) as nu_total_litro"))
        ->join('configuracion.tab_estacion_servicio as t01','t01.id','=','combustible.tab_despacho.id_estacion');

        if(!empty($q)){     
             $tab_despacho = $tab_despacho->where('t01.de_estacion_servicio', 'like','%'.$q.'%');
        }

        $tab_despacho = $tab_despacho->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('combustible.apoyo.lista')->with([
          'tab_despacho' => $tab_despacho,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }

    public function listaApoyo(Request $request,$id){

        $sortBy = 'de_gerencia';
        $orderBy = 'desc';
        $perPage = 5;
        $q = null;
        $columnas = [
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Origen'],
          ['valor'=>'bnumberdialed', 'texto'=>'Número de Destino']
        ];

        if ($request->has('orderBy')){
            $orderBy = $request->query('orderBy');
        }
        if ($request->has('sortBy')){
            $sortBy = $request->query('sortBy');
        } 
        if ($request->has('perPage')){
            $perPage = $request->query('perPage');
        } 
        if ($request->has('q')){
            $q = $request->query('q');
        }        



        $tab_gerencia = tab_gerencia::select( 'configuracion.tab_gerencia.id', 'de_gerencia',DB::raw("sum(nu_litro) as nu_litro"))
        ->join('combustible.tab_despacho_vehiculo as t01','t01.id_gerencia','=','configuracion.tab_gerencia.id')
        ->distinct()
        ->groupBy('configuracion.tab_gerencia.id', 'de_gerencia')
        ->where('t01.id_tab_despacho','=',$id);

        if(!empty($q)){     
             $tab_gerencia = $tab_gerencia->where('de_gerencia', 'like','%'.$q.'%');
        }

        $tab_gerencia = $tab_gerencia->orderBy($sortBy, $orderBy)
        ->paginate($perPage);



        return View::make('combustible.apoyo.listaApoyo')->with([
          'tab_gerencia' => $tab_gerencia,
          'id' => $id,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }
   
}
