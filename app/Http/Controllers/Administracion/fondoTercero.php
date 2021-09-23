<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//

use gobela\Models\Administracion\tab_retencion;
use gobela\Models\Administracion\tab_tipo_retencion;
use gobela\Models\Administracion\tab_fondo_tercero_detalle;
use gobela\Models\Administracion\tab_fondo_tercero;
use gobela\Models\Administracion\tab_liquidacion;
use gobela\Models\Administracion\tab_proceso_retencion;
use gobela\Models\Proceso\tab_solicitud;
use gobela\Models\Proceso\tab_ruta;
use gobela\Models\Configuracion\tab_documento;
use gobela\Models\Configuracion\tab_solicitud as tab_tipo_solicitud;
use View;
use Validator;
use Response;
use DB;
use Auth;
use Session;
use Redirect;
use Carbon\Carbon;
//*******************************//
use Illuminate\Http\Request;

use gobela\Http\Requests;
use gobela\Http\Controllers\Controller;

class fondoTercero extends Controller
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
    public function index( Request $request,$id, $ruta)
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
        
        $tab_documento = tab_documento::orderBy('id','asc')
        ->where('in_activo', '=', true)
        ->get();   
        
        $tab_tipo_retencion = tab_tipo_retencion::orderBy('id','asc')
        ->where('in_activo', '=', true)
        ->get();        
                 
        
        $tab_fondo_tercero = tab_fondo_tercero::select( 'administracion.tab_fondo_tercero.id', 'mo_pago','id_tab_proveedor','id_tab_documento', 'nu_documento', 'de_proveedor', 'tx_direccion',
        DB::raw(" to_char( fe_pago, 'dd-mm-YYYY') as fe_pago"),'id_tab_solicitud', 'id_tab_usuario','administracion.tab_fondo_tercero.tx_observacion')
        ->join('administracion.tab_proveedor as t01', 'administracion.tab_fondo_tercero.id_tab_proveedor', '=', 't01.id')              
        ->where('id_tab_solicitud', $id)
        ->first(); 
        
        $tab_fondo_tercero_detalle = tab_fondo_tercero_detalle::select( 'administracion.tab_fondo_tercero_detalle.id', 'id_tab_solicitud','de_retencion','monto',
        DB::raw(" to_char( fe_desde, 'dd-mm-YYYY') as fe_desde"),DB::raw(" to_char( fe_hasta, 'dd-mm-YYYY') as fe_hasta"),'tx_observacion')
        ->join('administracion.tab_retencion as t01', 'administracion.tab_fondo_tercero_detalle.id_tab_retencion', '=', 't01.id')
        ->where('id_tab_solicitud', '=', $id)
        ->get();        
        
        if(!$tab_fondo_tercero){
            
        return View::make('administracion.fondoTercero.retenciones')->with([
          'solicitud' => $id,
          'ruta' => $ruta,
          'tab_documento'  => $tab_documento,
          'tab_tipo_retencion'  => $tab_tipo_retencion,
          'tab_proceso' => $tab_proceso,
          'tab_fondo_tercero_detalle' => $tab_fondo_tercero_detalle,
          'tab_tipo_solicitud' => $tab_tipo_solicitud,
          'tab_solicitud' => $tab_solicitud
        ]);
        
        
        }else{
                
            
        return View::make('administracion.fondoTercero.retencionesEditar')->with([
          'solicitud' => $id,
          'ruta' => $ruta,
          'tab_documento'  => $tab_documento,
          'tab_tipo_retencion'  => $tab_tipo_retencion,
          'tab_proceso' => $tab_proceso,
          'data' => $tab_fondo_tercero,
          'tab_fondo_tercero_detalle' => $tab_fondo_tercero_detalle,
          'tab_tipo_solicitud' => $tab_tipo_solicitud,
          'tab_solicitud' => $tab_solicitud
        ]);            
            
        }
    }       
     

      public function retencion( Request $request)
  {

        $id_tab_tipo_retencion        = $request->tipo_retencion;
        
        $filtro_retencion = tab_fondo_tercero_detalle::select('id_tab_retencion')
        ->where('id_tab_solicitud', '=', $request->solicitud) 
        ->orderby('id','ASC')
        ->get();        

        $tab_retencion = tab_retencion::select( 'id','de_retencion')
        ->where('in_activo', '=', true)
        ->where('id_tab_tipo_retencion', '=', $id_tab_tipo_retencion)
        ->whereNotIn('id',$filtro_retencion)
        ->orderby('id','ASC')
        ->get(); 

    return Response::json(array(
			'success' => true,
			'valido' => true,
			'data' => $tab_retencion
		)); 

  }
  
      public function montoRetencion( Request $request)
  {
        $mo_retencion = 0;
        $fecha_inicio = Carbon::parse($request->fecha_inicio)->format('Y-m-d');
        $fecha_fin        = Carbon::parse($request->fecha_fin)->format('Y-m-d');   
        $id_tab_retencion        = $request->id_tab_retencion;
        
        if($request->fecha_fin==''||$request->fecha_fin==null){
        $fecha_fin  =  Carbon::parse($request->fecha_inicio)->format('Y-m-d'); 
        }        
        
        if($request->fecha_inicio!='' && $request->fecha_fin!='' && $request->id_tab_retencion!=''){
            
        $tab_proceso_retencion = tab_proceso_retencion::select(DB::raw("coalesce(sum(mo_retencion),0.00) as mo_retencion"))
        ->where('in_activo', '=', true)
        ->where('id_tab_retencion', '=', $id_tab_retencion)
        ->whereBetween('fe_retencion', [$fecha_inicio, $fecha_fin])
        ->first();
        $mo_retencion =  $tab_proceso_retencion->mo_retencion;
        }         
    
        

    return Response::json(array(
			'success' => true,
			'valido' => true,
			'data' => $mo_retencion
		)); 

  }  


    public function guardar(Request $request, $id = NULL)
    {
        DB::beginTransaction();

        if($id!=''||$id!=null){

          $validador = Validator::make( $request->all(), tab_fondo_tercero::$validarEditar);
          if ($validador->fails()) {

              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }
          try {
              

             
            $fe_pago = Carbon::parse($request->fecha_pago)->format('Y-m-d');
            $mo_pago = tab_fondo_tercero_detalle::moPago($request->solicitud);
            
            $tabla = tab_fondo_tercero::find($id);
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->id_tab_proveedor = $request->proveedor;
            $tabla->id_tab_usuario = Auth::user()->id;
            $tabla->nu_declaracion = $request->nu_declaracion;
            $tabla->fe_pago = $fe_pago;
            $tabla->mo_pago = $mo_pago;
            $tabla->tx_observacion = $request->tx_observacion;            
            $tabla->save();
            
            if (tab_fondo_tercero_detalle::where('id_tab_solicitud', '=', $request->solicitud)->exists()) {
            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = true;
            $tab_ruta->save();                  
            }else{
            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = false;
            $tab_ruta->save();                
            }  
            

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/proceso/ruta/lista/'.$request->solicitud);

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }else{

          $validador = Validator::make( $request->all(), tab_fondo_tercero::$validarCrear);
          if ($validador->fails()) {
             
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }
        

          try {
              
              
            $fe_pago = Carbon::parse($request->fecha_pago)->format('Y-m-d');           

            $tabla = new tab_fondo_tercero;
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->id_tab_proveedor = $request->proveedor;
            $tabla->id_tab_usuario = Auth::user()->id;
            $tabla->nu_declaracion = $request->nu_declaracion;
            $tabla->fe_pago = $fe_pago;
            $tabla->mo_pago = 0;
            $tabla->tx_observacion = $request->tx_observacion;            
            $tabla->save();            
            
            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = false;
            $tab_ruta->save();                 
                
                        

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            return Redirect::to('/proceso/ruta/datos/'.$request->solicitud);

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }
    }

   
    public function guardarRetencion(Request $request, $id = NULL)
    {
        DB::beginTransaction();

          $validador = Validator::make( $request->all(), tab_fondo_tercero_detalle::$validarCrear);
          if ($validador->fails()) {
              Session::flash('msg_alerta', 'Error!');
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }
        

          try {
              
           
              
            $fecha_inicio = Carbon::parse($request->fecha_inicio)->format('Y-m-d');   
            $fecha_fin = Carbon::parse($request->fecha_fin)->format('Y-m-d');
            
            
            $tabla = new tab_fondo_tercero_detalle;
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->id_tab_retencion = $request->retencion;
            $tabla->monto = $request->monto;
            $tabla->fe_desde = $fecha_inicio;
            $tabla->fe_hasta = $fecha_fin;
            $tabla->tx_observacion = $request->tx_observacion_retencion;            
            $tabla->save();
            
            $mo_pago = tab_fondo_tercero_detalle::moPago($request->solicitud);
            
            $fondo_tercero = tab_fondo_tercero::orderBy('id','asc')
            ->where('id_tab_solicitud', '=', $request->solicitud)
            ->first();            
            
            $tabla_fondo_tercero = tab_fondo_tercero::find($fondo_tercero->id);            
            $tabla_fondo_tercero->mo_pago = $mo_pago;           
            $tabla_fondo_tercero->save();
            
            if (tab_liquidacion::where('id_tab_solicitud', '=', $request->solicitud)->exists()) {            
            
            $liquidacion = tab_liquidacion::where('id_tab_solicitud', $request->solicitud)->first();
            
            $tab_liquidacion = tab_liquidacion::find( $liquidacion->id);
            $tab_liquidacion->id_tab_solicitud = $request->solicitud;
            $tab_liquidacion->id_tab_usuario = Auth::user()->id;
            $tab_liquidacion->fe_pago = $tabla_fondo_tercero->fe_pago;
            $tab_liquidacion->mo_pago = $mo_pago;
            $tab_liquidacion->mo_pendiente = $mo_pago;
            $tab_liquidacion->mo_pagado = 0;
            $tab_liquidacion->save();             
            
            }else{
                
            $tab_liquidacion = new tab_liquidacion;
            $tab_liquidacion->id_tab_solicitud = $request->solicitud;
            $tab_liquidacion->id_tab_usuario = Auth::user()->id;
            $tab_liquidacion->fe_pago = $tabla_fondo_tercero->fe_pago;
            $tab_liquidacion->mo_pago = $mo_pago;
            $tab_liquidacion->mo_pendiente = $mo_pago;
            $tab_liquidacion->mo_pagado = 0;
            $tab_liquidacion->save();                
                
            }
            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = true;
            $tab_ruta->save();            
            
            

            DB::commit();

            Session::flash('msg_side_overlay', 'Retencion agregada con Exito!');
            return Redirect::to('/proceso/ruta/datos/'.$request->solicitud);

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        
    }  
    
    public function eliminarRetencion( $id,$ruta, Request $request)
    {
        DB::beginTransaction();
        try {

            $tabla = tab_fondo_tercero_detalle::find( $request->get("id"));
            $tabla->delete();
            
            if (tab_fondo_tercero::where('id_tab_solicitud', '=', $id)->exists()) {   
            
            $mo_pago = tab_fondo_tercero_detalle::moPago($id);    
            $fondo_tercero = tab_fondo_tercero::orderBy('id','asc')
            ->where('id_tab_solicitud', '=', $id)
            ->first();            
            
            $tabla_fondo_tercero = tab_fondo_tercero::find($fondo_tercero->id);            
            $tabla_fondo_tercero->mo_pago = $mo_pago;           
            $tabla_fondo_tercero->save();
            
            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = true;
            $tab_ruta->save();                 
                
            }

            DB::commit();
            
            if (tab_fondo_tercero_detalle::where('id_tab_solicitud', '=', $id)->exists()) { 
              
            $tab_ruta = tab_ruta::find( $ruta);
            $tab_ruta->in_datos = true;
            $tab_ruta->save();                 
                
            }else{
                
            $tab_ruta = tab_ruta::find( $ruta);
            $tab_ruta->in_datos = false;
            $tab_ruta->save();                    
                
            } 
            
            DB::commit();

            Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
            return Redirect::to('/proceso/ruta/datos/'.$id);

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }    

}
