<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//

use gobela\Models\Proceso\tab_solicitud;
use gobela\Models\Proceso\tab_ruta;
use gobela\Models\Configuracion\tab_solicitud as tab_tipo_solicitud;
use gobela\Models\Administracion\tab_asignar_partida;
use gobela\Models\Administracion\tab_tipo_orden_pago;
use gobela\Models\Administracion\tab_orden_pago;
use gobela\Models\Administracion\tab_liquidacion;
use gobela\Models\Administracion\tab_cuenta_contable_documento;
use gobela\Models\Administracion\tab_asiento_contable;
use View;
use Validator;
use Response;
use DB;
use Auth;
use Session;
use Redirect;
use HelperReporte;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
//*******************************//
use Illuminate\Http\Request;

use gobela\Http\Requests;
use gobela\Http\Controllers\Controller;

class ordenPago extends Controller
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
    public function odp( $request, $id, $ruta)
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
        
        $tab_tipo_orden_pago = tab_tipo_orden_pago::orderBy('id','asc')
        ->where('in_activo', '=', true)
        ->get();
        
        $tab_orden_pago = tab_orden_pago::select( 'id', 'id_tab_solicitud', 'id_tab_tipo_orden_pago', 'nu_orden_pago', 'de_concepto_pago', 'mo_pago','mo_retencion','tx_documento',
        DB::raw(" to_char( fe_pago, 'dd-mm-YYYY') as fe_pago"))  
        ->where('id_tab_solicitud', $id)
        ->first();        
        
        $tab_asignar_partida = tab_asignar_partida::select(DB::raw("coalesce(sum(mo_presupuesto),0.00) as mo_pago"))             
        ->where('id_tab_solicitud', $id)
        ->where('in_activo', '=', true)
        ->groupBy('id_tab_solicitud')
        ->first();        
        
        $tab_asignar_partida_detalle = tab_asignar_partida::select( 'administracion.tab_asignar_partida.id',  'administracion.tab_asignar_partida.mo_presupuesto', 'administracion.tab_asignar_partida.id_tab_ejecutor', 
        'co_partida', 'de_partida', 't01.id as id_tab_asignar_partida_detalle', 'administracion.tab_asignar_partida.id_tab_fuente_financiamiento','de_concepto')
        ->leftJoin('administracion.tab_asignar_partida_detalle as t01','t01.id_tab_asignar_partida', '=', 'administracion.tab_asignar_partida.id') 
        ->leftJoin('administracion.tab_partida_egreso as t02', 't02.id', '=', 't01.id_tab_partida_egreso')                
        ->where('id_tab_solicitud', $id)
        ->get();

            if(!$tab_orden_pago){
            return View::make('administracion.ordenPago.generar')->with([
                'id' => $id,
                'ruta' => $ruta,
                'tab_proceso' => $tab_proceso,
                'tab_tipo_solicitud' => $tab_tipo_solicitud,
                'tab_solicitud' => $tab_solicitud, 
                'mo_pago' => $tab_asignar_partida->mo_pago,
                'tab_asignar_partida'  => $tab_asignar_partida_detalle,
                'tab_tipo_orden_pago'  => $tab_tipo_orden_pago
            ]);
            }else{
            return View::make('administracion.ordenPago.generarEditar')->with([
                'id' => $id,
                'ruta' => $ruta,
                'tab_proceso' => $tab_proceso,
                'data' => $tab_orden_pago,
                'tab_tipo_solicitud' => $tab_tipo_solicitud,
                'tab_solicitud' => $tab_solicitud, 
                'tab_asignar_partida'  => $tab_asignar_partida_detalle,
                'tab_tipo_orden_pago'  => $tab_tipo_orden_pago
            ]);    
            }
        

    }
    
    
    public function guardar( Request $request)
    {
        DB::beginTransaction();
  
        try {

            $validator = Validator::make($request->all(), tab_orden_pago::$validarCrear);

            if ($validator->fails()){

                return Redirect::back()->withErrors( $validator)->withInput( $request->all());
            }

            $tab_asignar_partida = tab_asignar_partida::select(DB::raw("coalesce(sum(mo_presupuesto),0.00) as mo_pago"))             
            ->where('id_tab_solicitud', $request->solicitud)
            ->where('in_activo', '=', true)
            ->groupBy('id_tab_solicitud')
            ->first();             
            
            $fe_pago = Carbon::parse($request->fecha_pago)->format('Y-m-d');
            
            if (tab_orden_pago::where('id_tab_solicitud', '=', $request->solicitud)->exists()) {
                
                $tabla = tab_orden_pago::where('id_tab_solicitud', $request->solicitud)->first();

                $tab_orden_pago = tab_orden_pago::find( $tabla->id);
                $tab_orden_pago->id_tab_usuario = Auth::user()->id;
                $tab_orden_pago->id_tab_tipo_orden_pago = $request->tipo_orden_pago;
                $tab_orden_pago->de_concepto_pago = $request->tx_concepto;
                $tab_orden_pago->fe_pago = $fe_pago;
                $tab_orden_pago->mo_pago = $tab_asignar_partida->mo_pago;
                $tab_orden_pago->mo_retencion = 0;
                $tab_orden_pago->save();
                
                $monto = $tab_asignar_partida->mo_pago - 0;
                $liquidacion = tab_liquidacion::where('id_tab_solicitud', $request->solicitud)->first();
                
                $tab_liquidacion = tab_liquidacion::find( $liquidacion->id);
                $tab_liquidacion->id_tab_solicitud = $request->solicitud;
                $tab_liquidacion->id_tab_usuario = Auth::user()->id;
                $tab_liquidacion->fe_pago = $fe_pago;
                $tab_liquidacion->mo_pago = $monto;
                $tab_liquidacion->mo_pendiente = $monto;
                $tab_liquidacion->mo_pagado = 0;
                $tab_liquidacion->save();            
            
            }else{

                $tab_orden_pago = new tab_orden_pago;
                $tab_orden_pago->id_tab_solicitud = $request->solicitud;
                $tab_orden_pago->id_tab_usuario = Auth::user()->id;
                $tab_orden_pago->id_tab_tipo_orden_pago = $request->tipo_orden_pago;
                $tab_orden_pago->de_concepto_pago = $request->tx_concepto;
                $tab_orden_pago->fe_pago = $fe_pago;
                $tab_orden_pago->mo_pago = $tab_asignar_partida->mo_pago;
                $tab_orden_pago->mo_retencion = 0;
                $tab_orden_pago->save();                
                    
                $monto = $tab_asignar_partida->mo_pago - 0;
                
                $tab_liquidacion = new tab_liquidacion;
                $tab_liquidacion->id_tab_solicitud = $request->solicitud;
                $tab_liquidacion->id_tab_usuario = Auth::user()->id;
                $tab_liquidacion->fe_pago = $fe_pago;
                $tab_liquidacion->mo_pago = $monto;
                $tab_liquidacion->mo_pendiente = $monto;
                $tab_liquidacion->mo_pagado = 0;
                $tab_liquidacion->save();             
                
            }
            
            
            $model = tab_asiento_contable::where( 'id_tab_solicitud', $request->solicitud)->where( 'id_tab_tipo_asiento', 2);
            $model->delete();

            $cuenta_documento = tab_cuenta_contable_documento::getCuentaDocumento( $request->ruta);

            if (empty($cuenta_documento)){
                return Redirect::back()->withErrors( [
                    'da_alert_form' => 'Aviso: !No se ha configurado la ruta del tramite para los asientos contables!'
                ])->withInput( $request->all());
            }

            $tab_asiento_contable = new tab_asiento_contable;
            $tab_asiento_contable->id_tab_solicitud = $request->solicitud;
            $tab_asiento_contable->id_tab_cuenta_contable = $cuenta_documento->id_cc_gasto_pago;
            $tab_asiento_contable->id_tab_tipo_asiento = 2;
            $tab_asiento_contable->mo_debe = $monto;
            $tab_asiento_contable->id_tab_usuario = Auth::user()->id;
            $tab_asiento_contable->in_activo = true;
            $tab_asiento_contable->save();

            $tab_asiento_contable = new tab_asiento_contable;
            $tab_asiento_contable->id_tab_solicitud = $request->solicitud;
            $tab_asiento_contable->id_tab_cuenta_contable = $cuenta_documento->id_cc_odp;
            $tab_asiento_contable->id_tab_tipo_asiento = 2;
            $tab_asiento_contable->mo_haber = $monto;
            $tab_asiento_contable->id_tab_usuario = Auth::user()->id;
            $tab_asiento_contable->in_activo = true;
            $tab_asiento_contable->save();
            

            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = true;
            $tab_ruta->save();     
            
            HelperReporte::generarReporte($request->solicitud);

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
    
    
   
        

}
