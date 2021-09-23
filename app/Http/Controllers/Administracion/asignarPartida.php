<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//

use gobela\Models\Proceso\tab_solicitud;
use gobela\Models\Proceso\tab_ruta;
use gobela\Models\Configuracion\tab_solicitud as tab_tipo_solicitud;
use gobela\Models\Administracion\tab_ejecutor;
use gobela\Models\Administracion\tab_fuente_financiamiento;
use gobela\Models\Administracion\tab_asignar_partida;
use gobela\Models\Administracion\tab_asignar_partida_detalle;
use gobela\Models\Administracion\tab_catalogo_partida;
use gobela\Models\Administracion\tab_presupuesto_egreso;
use gobela\Models\Administracion\tab_accion_especifica;
use gobela\Models\Administracion\tab_partida_egreso;
use View;
use Validator;
use Response;
use DB;
use Auth;
use Session;
use Redirect;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
//*******************************//
use Illuminate\Http\Request;

use gobela\Http\Requests;
use gobela\Http\Controllers\Controller;

class asignarPartida extends Controller
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
    public function asignarPartida( $request, $id, $ruta)
    {

        $tab_solicitud = tab_solicitud::select( 'id', 'nu_solicitud','id_tab_tipo_solicitud')
        ->where('id', '=', $id)
        ->first();       
        
        $tab_proceso = tab_ruta::select( 't01.de_proceso')
        ->join('configuracion.tab_proceso as t01', 'proceso.tab_ruta.id_tab_proceso', '=', 't01.id')
        ->where('proceso.tab_ruta.id', '=', $ruta)
        ->first();        
        
        $tab_tipo_solicitud = tab_tipo_solicitud::select( 'id', 'de_solicitud')
        ->where('id', '=', $tab_solicitud->id_tab_tipo_solicitud)
        ->first();         
        

        $tab_ejecutor = tab_ejecutor::orderBy('id','asc')
        ->get();

        $tab_fuente_financiamiento = tab_fuente_financiamiento::orderBy('id','asc')
        ->get();
        
        $tab_asignar_partida_detalle = tab_asignar_partida::select( 'administracion.tab_asignar_partida.id',  'administracion.tab_asignar_partida.mo_presupuesto', 'administracion.tab_asignar_partida.id_tab_ejecutor', 
        'co_partida', 'de_partida', 't01.id as id_tab_asignar_partida_detalle', 'administracion.tab_asignar_partida.id_tab_fuente_financiamiento','de_concepto')
        ->leftJoin('administracion.tab_asignar_partida_detalle as t01','t01.id_tab_asignar_partida', '=', 'administracion.tab_asignar_partida.id') 
        ->leftJoin('administracion.tab_partida_egreso as t02', 't02.id', '=', 't01.id_tab_partida_egreso')                
        ->where('id_tab_solicitud', $id)
        ->get();


            return View::make('administracion.asignarPartida.asignarPartida')->with([
                'id' => $id,
                'ruta' => $ruta,
                'tab_proceso' => $tab_proceso,
                'tab_tipo_solicitud' => $tab_tipo_solicitud,
                'tab_solicitud' => $tab_solicitud,                
                'tab_asignar_partida'  => $tab_asignar_partida_detalle,
                'tab_ejecutor'  => $tab_ejecutor,
                'tab_fuente_financiamiento'  => $tab_fuente_financiamiento,
            ]);

        

    }
    
    
    public function guardar( Request $request)
    {
        DB::beginTransaction();
  
        try {

            $validator = Validator::make($request->all(), tab_asignar_partida_detalle::$validarCrear);

            if ($validator->fails()){
                Session::flash('msg_alerta', 'Error!');
                return Redirect::back()->withErrors( $validator)->withInput( $request->all());
            }


            if (tab_asignar_partida_detalle::where('id_tab_asignar_partida', '=', $request->id_tab_asignar_partida)->exists()) {
                
                
            $tabla = tab_asignar_partida_detalle::where('id_tab_asignar_partida', $request->id_tab_asignar_partida)->first();


            $tab_asignar_partida_detalle = tab_asignar_partida::find( $tabla->id);
            $tab_asignar_partida_detalle->id_tab_ejecutor = $request->ejecutor;
            $tab_asignar_partida_detalle->id_tab_presupuesto_egreso = $request->proyecto_ac;
            $tab_asignar_partida_detalle->id_tab_accion_especifica = $request->accion_especifica;
            $tab_asignar_partida_detalle->id_tab_partida_egreso = $request->partida;
            $tab_asignar_partida_detalle->mo_disponible = $request->monto_disponible;
            $tab_asignar_partida_detalle->in_activo = true;
            $tab_asignar_partida_detalle->save();
            
            }else{
                
            $tab_asignar_partida_detalle = new tab_asignar_partida_detalle;
            $tab_asignar_partida_detalle->id_tab_asignar_partida = $request->id_tab_asignar_partida;
            $tab_asignar_partida_detalle->id_tab_ejecutor = $request->ejecutor;
            $tab_asignar_partida_detalle->id_tab_presupuesto_egreso = $request->proyecto_ac;
            $tab_asignar_partida_detalle->id_tab_accion_especifica = $request->accion_especifica;
            $tab_asignar_partida_detalle->id_tab_partida_egreso = $request->partida;
            $tab_asignar_partida_detalle->mo_disponible = $request->monto_disponible;
            $tab_asignar_partida_detalle->in_activo = true;
            $tab_asignar_partida_detalle->save();                
                
                
            }
            
            $tab_asignar_partida = tab_asignar_partida::find( $request->id_tab_asignar_partida);
            $tab_asignar_partida->in_cargada = true;
            $tab_asignar_partida->save();            
            
            $cant_pendiente = tab_asignar_partida::where('id_tab_solicitud','=', $request->solicitud)
            ->where('in_cargada','=',false)
            ->count();


            if($cant_pendiente<=0){
            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = true;
            $tab_ruta->save();     
            }

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Guardado con Exito!');
            return Redirect::to('/proceso/ruta/datos/'.$request->solicitud);

        }catch (\Illuminate\Database\QueryException $e){

            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());

        }
    }    
    
    
    public function catalogo(Request $request)
    {
		$response['success']  = 'true';
        $response['data']  = tab_catalogo_partida::orderby('id','ASC')->get()->toArray();
		return Response::json($response, 200);
    }    
    
    public function proyectoAc(Request $request)
    {
		$response['success']  = 'true';
        $response['data']  = tab_presupuesto_egreso::where('id_tab_ejercicio_fiscal', Session::get('ejercicio'))
            ->where('id_tab_ejecutor', $request->get("ejecutor"))
            ->orderby('id','ASC')->get()->toArray();

		return Response::json($response, 200);
    }

        public function proyectoAcAe(Request $request)
    {
		$response['success']  = 'true';
        $response['data']  = tab_accion_especifica::where('id_tab_presupuesto_egreso', $request->get("proyecto_ac"))
            ->orderby('id','ASC')->get()->toArray();

		return Response::json($response, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function partida(Request $request)
    {
		$response['success']  = 'true';
        $response['data']  = tab_partida_egreso::where('id_tab_accion_especifica', $request->get("accion_especifica"))
        ->where('id_tab_ejercicio_fiscal', Session::get('ejercicio'))
        ->orderby('id','ASC')->get()->toArray();

		return Response::json($response, 200);
    }
    
    public function borrar( $id, Request $request)
    {
        DB::beginTransaction();
        try {

            $tabla = tab_asignar_partida_detalle::find( $request->get("id"));
            $tabla->delete();
            
            $tab_ruta = tab_ruta::find( $id);
            $tab_ruta->in_datos = false;
            $tab_ruta->save();            

            DB::commit();

            Session::flash('msg_side_overlay', 'Partida liberada con Exito!');
            return Redirect::to('/proceso/ruta/datos/'.$tab_ruta->id_tab_solicitud);

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }    
        

}
