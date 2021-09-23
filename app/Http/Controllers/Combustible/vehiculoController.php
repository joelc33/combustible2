<?php

namespace App\Http\Controllers\Combustible;
//*******agregar esta linea******//
use App\Models\Telemedicina\tab_persona;
use App\Models\Proceso\tab_solicitud;
use App\Models\Configuracion\tab_nacionalidad;
use App\Models\Configuracion\tab_gerencia;
use App\Models\Combustible\tab_vehiculo;
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

class vehiculoController extends Controller
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
    public function listaVehiculo( Request $request)
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
       

        $tab_vehiculo = tab_vehiculo::select( 't01.id as id_propietario', 'cedula', 'nombres', 'apellidos','id_gerencia','id_nacionalidad','combustible.tab_vehiculo.id as id_vehiculo', 'de_placa', 'de_modelo','de_marca','de_color','de_gerencia')
        ->join('telemedicina.tab_persona as t01','t01.id','=','combustible.tab_vehiculo.id_propietario')
        ->join('configuracion.tab_gerencia as t02','t02.id','=','t01.id_gerencia');
       

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

        return View::make('combustible.listaVehiculo')->with([
          'tab_vehiculo' => $tab_vehiculo,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function pendiente( Request $request)
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

        $proceso = tab_proceso_usuario::getListaProcesoAsignado(Auth::user()->id);
        $tramite = tab_solicitud_usuario::getListaTramiteAsignado(Auth::user()->id);
       
        $tab_solicitud = tab_persona::select( 'proceso.tab_solicitud.id', 'de_solicitud', 'nu_identificador',
        'nu_solicitud', 'nb_usuario','proceso.tab_solicitud.id_persona',
        'id_tab_ejercicio_fiscal', DB::raw("to_char(proceso.tab_solicitud.created_at, 'dd/mm/YYYY hh12:mi AM') as fe_creado"),'de_proceso','nombres','apellidos','cedula',"t01.id as id_ruta","de_instituto","de_municipio")
        ->join('proceso.tab_solicitud', 'telemedicina.tab_persona.id', '=', 'proceso.tab_solicitud.id_persona')
        ->join('proceso.tab_ruta as t01', 'proceso.tab_solicitud.id', '=', 't01.id_tab_solicitud')
        ->join('configuracion.tab_proceso as t02', 't02.id', '=', 't01.id_tab_proceso')
        ->join('autenticacion.tab_usuario as t03', 't03.id', '=', 't01.id_tab_usuario')
        ->join('configuracion.tab_solicitud as t04', 't04.id', '=', 'proceso.tab_solicitud.id_tab_tipo_solicitud')
        ->leftjoin('configuracion.tab_instituto as t05', 't05.id', '=', 'proceso.tab_solicitud.id_centro_asistencial')
        ->leftjoin('configuracion.tab_municipio as t06', 't06.id', '=', 'telemedicina.tab_persona.id_municipio')
        ->where('in_actual', '=', true)
        ->where('proceso.tab_solicitud.in_activo', '=', true)
        ->where('t01.id_tab_estatus', '=', 1)
        ->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
        ->whereIn('t01.id_tab_proceso', $proceso)
        ->whereIn('proceso.tab_solicitud.id_tab_tipo_solicitud', $tramite)
        ->search($q, $sortBy)
        ->orderBy('nu_solicitud', $orderBy)
        ->paginate($perPage);

        return View::make('proceso.combustible.pendiente')->with([
          'tab_solicitud' => $tab_solicitud,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }

   

   
        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function nuevo(Request $request){

        $tab_nacionalidad = tab_nacionalidad::select( 'id','de_nacionalidad')
        ->orderby('id','ASC')
        ->get();     

        $tab_gerencia = tab_gerencia::select( 'id','de_gerencia')
        ->where('id','<>','1')
        ->orderby('de_gerencia','ASC')
        ->get();       
       
        return View::make('combustible.nuevo')->with([
           'tab_nacionalidad' => $tab_nacionalidad,
           'tab_gerencia'     => $tab_gerencia
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

                $validator= Validator::make($request->all(), tab_vehiculo::$validar);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }


                $validator= Validator::make($request->all(), tab_persona::$validar);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

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

              
               
                

                DB::commit();
                
                Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
                return Redirect::to('/combustible/lista');

            }catch (\Illuminate\Database\QueryException $e){
                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());
            }
  
       
    }

    public function buscar(Request $request)
    {

      if (tab_vehiculo::where(DB::raw("upper(de_placa)"), '=', strtoupper($request->placa))
      ->exists()) {

        $tab_vehiculo = tab_vehiculo::select( 't01.id as id_propietario', 'cedula', 'nombres', 'apellidos','id_gerencia','id_nacionalidad','combustible.tab_vehiculo.id as id_vehiculo', 'de_placa', 'de_modelo','de_marca','de_color')
        ->join('telemedicina.tab_persona as t01','t01.id','=','combustible.tab_vehiculo.id_propietario')
        ->where(DB::raw("upper(de_placa)"), '=', strtoupper($request->placa))->first()->toArray();

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

    public function buscarPersona(Request $request)
    {

      if (tab_persona::where('cedula', '=', $request->cedula)
      ->exists()) {

        $tab_persona = tab_persona::select('id', 'cedula', 'nombres', 'apellidos', 'id_sexo', 'telefono', 'direccion','correo','id_municipio','id_nacionalidad','id_gerencia')
        ->where('cedula', '=', $request->cedula)
        ->first()->toArray();

        $response['success']  = 'true';
        $response['data']  = $tab_persona;

        return Response::json($response, 200);

      }else{

        $response['success']  = 'false';
        $response['data']  = '';
        $response['msg']  = '';

        return Response::json($response, 200);

      }
    }


   
}
