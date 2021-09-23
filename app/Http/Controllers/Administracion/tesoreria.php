<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Proceso\tab_solicitud;
use gobela\Models\Configuracion\tab_solicitud_usuario;
use gobela\Models\Configuracion\tab_solicitud as tab_tipo_solicitud;
use gobela\Models\Proceso\tab_ruta;
use gobela\Models\Configuracion\tab_ruta as tab_configuracion_ruta;
use gobela\Models\Configuracion\tab_proceso_usuario;
use gobela\Models\Configuracion\tab_proceso;
use gobela\Models\Administracion\tab_forma_pago;
use gobela\Models\Administracion\tab_banco;
use gobela\Models\Administracion\tab_liquidacion;
use gobela\Models\Administracion\tab_pago;
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
use Auth;
use Carbon\Carbon;
//*******************************//
use Illuminate\Http\Request;

use gobela\Http\Requests;
use gobela\Http\Controllers\Controller;

class tesoreria extends Controller
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
    public function detallePendiente( Request $request, $id)
    {
        $tab_solicitud = tab_solicitud::select( 'id', 'nu_solicitud','id_tab_tipo_solicitud')
        ->where('id', '=', $id)
        ->first();       
        
        $tab_proceso = tab_proceso::select( 'de_proceso')
        ->where('id', '=', 16)
        ->first();        
        
        $tab_tipo_solicitud = tab_tipo_solicitud::select( 'id', 'de_solicitud')
        ->where('id', '=', $tab_solicitud->id_tab_tipo_solicitud)
        ->first();  
        
        $tab_forma_pago = tab_forma_pago::select( 'id', 'de_forma_pago')
        ->where('in_activo', '=', true)
        ->get();  
        
        $tab_banco = tab_banco::select( 'id', 'de_banco')
        ->where('in_activo', '=', true)
        ->get();        
        
        $tab_liquidacion = tab_liquidacion::select( 'id', 'id_tab_solicitud', 'nu_liquidacion', 'fe_pago', 'mo_pago', 'mo_pendiente', 'mo_pagado')
        ->where('id_tab_solicitud', '=', $id)
        ->get();        

        $tab_pago = tab_pago::select( 'administracion.tab_pago.id', 'administracion.tab_pago.id_tab_solicitud', 'administracion.tab_pago.nu_pago', 
       DB::raw(" to_char( administracion.tab_pago.fe_pago, 'dd-mm-YYYY') as fe_pago"), 'administracion.tab_pago.mo_pago','t01.de_banco','t02.nu_cuenta_bancaria','t02.de_cuenta_bancaria')
       ->join('administracion.tab_banco as t01', 'administracion.tab_pago.id_tab_banco', '=', 't01.id')
       ->join('administracion.tab_cuenta_bancaria as t02', 'administracion.tab_pago.id_tab_cuenta_bancaria', '=', 't02.id')
        ->where('id_tab_solicitud', '=', $id)
        ->get();         
        
            return View::make('administracion.tesoreria.detallePendiente')->with([
                'id' => $id,
                'tab_proceso' => $tab_proceso,
                'tab_forma_pago' => $tab_forma_pago,
                'tab_banco' => $tab_banco,
                'tab_liquidacion' => $tab_liquidacion,
                'tab_pago' => $tab_pago,
                'tab_tipo_solicitud' => $tab_tipo_solicitud,
                'tab_solicitud' => $tab_solicitud
            ]);
    }
    
    public function detalleProcesado( Request $request, $id)
    {
        $tab_solicitud = tab_solicitud::select( 'id', 'nu_solicitud','id_tab_tipo_solicitud')
        ->where('id', '=', $id)
        ->first();       
        
        $tab_proceso = tab_proceso::select( 'de_proceso')
        ->where('id', '=', 16)
        ->first();        
        
        $tab_tipo_solicitud = tab_tipo_solicitud::select( 'id', 'de_solicitud')
        ->where('id', '=', $tab_solicitud->id_tab_tipo_solicitud)
        ->first();  
    

        $tab_pago = tab_pago::select( 'administracion.tab_pago.id', 'administracion.tab_pago.id_tab_solicitud', 'administracion.tab_pago.nu_pago', 
       DB::raw(" to_char( administracion.tab_pago.fe_pago, 'dd-mm-YYYY') as fe_pago"), 'administracion.tab_pago.mo_pago','t01.de_banco','t02.nu_cuenta_bancaria','t02.de_cuenta_bancaria')
       ->join('administracion.tab_banco as t01', 'administracion.tab_pago.id_tab_banco', '=', 't01.id')
       ->join('administracion.tab_cuenta_bancaria as t02', 'administracion.tab_pago.id_tab_cuenta_bancaria', '=', 't02.id')
        ->where('id_tab_solicitud', '=', $id)
        ->get();         
        
            return View::make('administracion.tesoreria.detalleProcesado')->with([
                'id' => $id,
                'tab_proceso' => $tab_proceso,
                'tab_pago' => $tab_pago,
                'tab_tipo_solicitud' => $tab_tipo_solicitud,
                'tab_solicitud' => $tab_solicitud
            ]);
    }    

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function pendiente( Request $request)
    {
        $sortBy = 'id';
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

        $tab_solicitud = tab_solicitud::select( 'proceso.tab_solicitud.id', 'de_solicitud', 'nu_identificador',
        'nu_solicitud', 'nb_usuario',
        'id_tab_ejercicio_fiscal', DB::raw("to_char(proceso.tab_solicitud.created_at, 'dd/mm/YYYY hh12:mi AM') as fe_creado"),
        'de_proceso')
        ->join('proceso.tab_ruta as t01', 'proceso.tab_solicitud.id', '=', 't01.id_tab_solicitud')
        ->join('configuracion.tab_proceso as t02', 't02.id', '=', 't01.id_tab_proceso')
        ->join('autenticacion.tab_usuario as t03', 't03.id', '=', 't01.id_tab_usuario')
        ->join('configuracion.tab_solicitud as t04', 't04.id', '=', 'proceso.tab_solicitud.id_tab_tipo_solicitud')
        ->where('in_actual', '=', true)
        ->where('proceso.tab_solicitud.in_activo', '=', true)
        ->where('t01.id_tab_estatus', '=', 1)
        ->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
        ->whereIn('t01.id_tab_proceso', $proceso)
        ->whereIn('proceso.tab_solicitud.id_tab_tipo_solicitud', $tramite)
        ->where('t01.id_tab_proceso', '=', 16)
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.tesoreria.pendiente')->with([
          'tab_solicitud' => $tab_solicitud,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }
    
    public function procesado( Request $request)
    {
        $sortBy = 'id';
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

        $tab_solicitud = tab_solicitud::select( 'proceso.tab_solicitud.id', 'de_solicitud', 'nu_identificador',
        'nu_solicitud', 'nb_usuario',
        'id_tab_ejercicio_fiscal', DB::raw("to_char(proceso.tab_solicitud.created_at, 'dd/mm/YYYY hh12:mi AM') as fe_creado"),
        'de_proceso')
        ->join('proceso.tab_ruta as t01', 'proceso.tab_solicitud.id', '=', 't01.id_tab_solicitud')
        ->join('configuracion.tab_proceso as t02', 't02.id', '=', 't01.id_tab_proceso')
        ->join('autenticacion.tab_usuario as t03', 't03.id', '=', 't01.id_tab_usuario')
        ->join('configuracion.tab_solicitud as t04', 't04.id', '=', 'proceso.tab_solicitud.id_tab_tipo_solicitud')
        ->where('in_actual', '=', false)
        ->where('proceso.tab_solicitud.in_activo', '=', true)
        ->where('t01.id_tab_estatus', '=', 2)
        ->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
        ->whereIn('t01.id_tab_proceso', $proceso)
        ->whereIn('proceso.tab_solicitud.id_tab_tipo_solicitud', $tramite)
        ->where('t01.id_tab_proceso', '=', 16)
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.tesoreria.procesado')->with([
          'tab_solicitud' => $tab_solicitud,
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
    public function guardar( Request $request)
    {
        DB::beginTransaction();


            try {

                $validator= Validator::make($request->all(), tab_pago::$validarCrear);

                if ($validator->fails()){
                    Session::flash('msg_alerta', 'Error!');
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                $monto_pendiente = $request->monto_pendiente - $request->monto;
                $monto_pagado = $request->monto_pagado + $request->monto;
                
                if($request->monto > $request->monto_pendiente){
                    Session::flash('msg_alerta', 'Error!');
                    return Redirect::back()->withErrors(['de_alert_form' => 'El monto a pagar no puede ser mayor al monto pendiente'])->withInput( $request->all());   
                }
                
                $tab_liquidacion = tab_liquidacion::find($request->id_tab_liquidacion);
                $tab_liquidacion->mo_pendiente = $monto_pendiente;
                $tab_liquidacion->mo_pagado = $monto_pagado; 
                $tab_liquidacion->save();
                
                $fe_pago = Carbon::parse($request->fe_pago)->format('Y-m-d');

                $tabla = new tab_pago;
                $tabla->id_tab_liquidacion = $request->id_tab_liquidacion;
                $tabla->id_tab_solicitud = $request->solicitud;
                $tabla->id_tab_usuario = Auth::user()->id;
                $tabla->id_tab_forma_pago = $request->forma_pago;
                $tabla->id_tab_banco = $request->banco;
                $tabla->id_tab_cuenta_bancaria = $request->cuenta_bancaria;     
                $tabla->fe_pago = $fe_pago;
                $tabla->nu_pago = $request->numero_transaccion;
                $tabla->mo_pago = $request->monto;
                $tabla->save();                
                
                if($monto_pendiente==0){
                    
                $ruta = tab_ruta::where('id_tab_solicitud', $request->solicitud)->where('id_tab_proceso','=', 16)->first();                    
                    
                $tab_ruta = tab_ruta::find( $ruta->id);
                $tab_ruta->id_tab_estatus = 2;
                $tab_ruta->save();                     
                }
               
                
                DB::commit();
                
                if($monto_pendiente==0){
                    
                Session::flash('msg_side_overlay', 'Registro Guardado con Exito!');
                return Redirect::to('/administracion/tesoreria/detalleProcesado/'.$request->solicitud);     
                
                }else{
                    
                Session::flash('msg_side_overlay', 'Registro Guardado con Exito!');
                return Redirect::to('/administracion/tesoreria/detallePendiente/'.$request->solicitud);       
                
                }                


            }catch (\Illuminate\Database\QueryException $e){
                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());
            }
  
        
    }

}
