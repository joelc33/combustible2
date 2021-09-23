<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//


use gobela\Models\Administracion\tab_ejecutor;
use gobela\Models\Administracion\tab_fuente_financiamiento;
use gobela\Models\Administracion\tab_nu_financiamiento;
use gobela\Models\Administracion\tab_partida_egreso;
use gobela\Models\Administracion\tab_credito_adicional;
use gobela\Models\Administracion\tab_credito_adicional_partidas;
use gobela\Models\Administracion\tab_tipo_credito_adicional;
use gobela\Models\Administracion\tab_partida_ingreso;
use gobela\Models\Administracion\tab_presupuesto_egreso;
use gobela\Models\Administracion\tab_accion_especifica;
use gobela\Models\Administracion\tab_catalogo_partida;
use gobela\Models\Administracion\tab_sector_presupuesto;
use gobela\Models\Administracion\tab_tipo_ingreso;
use gobela\Models\Administracion\tab_ambito;
use gobela\Models\Administracion\tab_aplicacion;
use gobela\Models\Administracion\tab_clasificacion_economica;
use gobela\Models\Administracion\tab_area_estrategica;
use gobela\Models\Administracion\tab_tipo_gasto;
use gobela\Models\Proceso\tab_solicitud;
use gobela\Models\Proceso\tab_ruta;
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

class creditoAdicional extends Controller
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
        
        $tab_fuente_financiamiento = tab_fuente_financiamiento::orderBy('id','asc')
        ->where('in_activo', '=', true)
        ->get();     
        
        $tab_tipo_credito_adicional = tab_tipo_credito_adicional::orderBy('id','asc')
        ->where('in_activo', '=', true)
        ->get();          
        
        $tab_nu_financiamiento = tab_nu_financiamiento::orderBy('id','asc')
        ->get();   
        
        $filtro_ingreso = tab_credito_adicional_partidas::select('id_tab_partida_ingreso')
        ->whereNotNull('id_tab_partida_ingreso')
        ->where('id_tab_solicitud', '=', $id)
        ->get();            
                 
        $tab_partida_ingreso = tab_partida_ingreso::orderBy('id','asc')
        ->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
        ->whereNotIn('id',$filtro_ingreso)                
        ->get();        
        
        $tab_ejecutor = tab_ejecutor::orderBy('id','asc')
        ->get(); 
        
        
        $tab_tipo_ingreso = tab_tipo_ingreso::select( 'id','nu_tipo_ingreso', 'de_tipo_ingreso', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();         
        
        
        $tab_ambito = tab_ambito::select( 'id','nu_ambito', 'de_ambito', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();      
        
        $tab_aplicacion = tab_aplicacion::select( 'id','nu_aplicacion', 'de_aplicacion', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();   
        
        $tab_clasificacion_economica = tab_clasificacion_economica::select( 'id','tx_sigla', 'de_clasificacion_economica', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();      
        
        $tab_area_estrategica = tab_area_estrategica::select( 'id','tx_sigla', 'de_area_estrategica', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();  
        
        $tab_tipo_gasto = tab_tipo_gasto::select( 'id','tx_sigla', 'de_tipo_gasto', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();        
        
        $tab_credito_adicional = tab_credito_adicional::select( 'administracion.tab_credito_adicional.id', 'id_tab_solicitud','administracion.tab_credito_adicional.id_tab_fuente_financiamiento','id_tab_nu_financiamiento', 'id_tab_tipo_credito_adicional', 'de_articulo', 'de_credito',              
        DB::raw(" to_char( fe_credito, 'dd-mm-YYYY') as fe_credito"),DB::raw(" to_char( fe_oficio, 'dd-mm-YYYY') as fe_oficio"),'de_justificacion','in_procesado','de_fuente_financiamiento','nu_financiamiento','de_tipo_credito_adicional') 
        ->join('administracion.tab_fuente_financiamiento as t01', 'administracion.tab_credito_adicional.id_tab_fuente_financiamiento', '=', 't01.id') 
        ->join('administracion.tab_nu_financiamiento as t02', 'administracion.tab_credito_adicional.id_tab_nu_financiamiento', '=', 't02.id')
        ->join('administracion.tab_tipo_credito_adicional as t03', 'administracion.tab_credito_adicional.id_tab_tipo_credito_adicional', '=', 't03.id')
        ->where('id_tab_solicitud', $id)
        ->first(); 
        
        $mo_ingreso = tab_credito_adicional_partidas::moIngreso($id); 

        $mo_gasto = tab_credito_adicional_partidas::moGasto($id);  
        
       
        
        $tab_credito_adicional_ingreso = tab_credito_adicional_partidas::select( 'administracion.tab_credito_adicional_partidas.id', 't01.nu_partida','t01.de_partida','monto')
        ->join('administracion.tab_partida_ingreso as t01', 'administracion.tab_credito_adicional_partidas.id_tab_partida_ingreso', '=', 't01.id')                
        ->whereNotNull('id_tab_partida_ingreso')
        ->where('id_tab_solicitud', '=', $id)
        ->get(); 
        
        $tab_credito_adicional_gasto = tab_credito_adicional_partidas::select( 'administracion.tab_credito_adicional_partidas.id', 't01.nu_partida','t01.de_partida','monto','t02.nu_ejecutor','t02.de_ejecutor')
        ->join('administracion.tab_catalogo_partida as t01', 'administracion.tab_credito_adicional_partidas.id_tab_catalogo_partida', '=', 't01.id')
        ->join('administracion.tab_ejecutor as t02', 'administracion.tab_credito_adicional_partidas.id_tab_ejecutor', '=', 't02.id')                
        ->whereNotNull('id_tab_catalogo_partida')
        ->where('id_tab_solicitud', '=', $id)
        ->get();        
               
        
        if(!$tab_credito_adicional){
            
        return View::make('administracion.creditoAdicional.index')->with([
          'solicitud' => $id,
          'ruta' => $ruta,
          'tab_fuente_financiamiento'  => $tab_fuente_financiamiento,
          'tab_tipo_credito_adicional'  => $tab_tipo_credito_adicional,
          'tab_proceso' => $tab_proceso,
          'tab_tipo_solicitud' => $tab_tipo_solicitud,
          'tab_solicitud' => $tab_solicitud
        ]);
        
        
        }else{
                
            
        return View::make('administracion.creditoAdicional.editar')->with([
          'solicitud' => $id,
          'ruta' => $ruta,
          'tab_fuente_financiamiento'  => $tab_fuente_financiamiento,
          'tab_nu_financiamiento'  => $tab_nu_financiamiento,
          'tab_tipo_credito_adicional'  => $tab_tipo_credito_adicional,
          'tab_partida_ingreso'  => $tab_partida_ingreso,            
          'tab_proceso' => $tab_proceso,
          'data' => $tab_credito_adicional,
          'mo_ingreso' => $mo_ingreso,
          'mo_gasto' => $mo_gasto,
          'tab_ejecutor' => $tab_ejecutor,
          'tab_credito_adicional_ingreso' => $tab_credito_adicional_ingreso,
          'tab_credito_adicional_gasto' => $tab_credito_adicional_gasto,
          'tab_tipo_solicitud' => $tab_tipo_solicitud,
          'tab_solicitud' => $tab_solicitud,
          'tab_tipo_ingreso' => $tab_tipo_ingreso,
          'tab_ambito' => $tab_ambito,
          'tab_aplicacion' => $tab_aplicacion,
          'tab_clasificacion_economica' => $tab_clasificacion_economica,
          'tab_area_estrategica' => $tab_area_estrategica,
          'tab_tipo_gasto' => $tab_tipo_gasto
        ]);            
            
        }
    }       
     

      public function nu_financiamiento( Request $request)
  {

        $id_tab_fuente_financiamiento        = $request->fuente_financiamiento;
        
        $filtro_nu_financiamiento = tab_partida_egreso::select('id_tab_nu_financiamiento')
        ->where('id_tab_ejercicio_fiscal', '=',  Session::get('ejercicio')) 
        ->whereNotNull('id_tab_nu_financiamiento') 
        ->groupBy('id_tab_nu_financiamiento')
        ->get();

       

        $tab_nu_financiamiento = tab_nu_financiamiento::select( 'id','nu_financiamiento')
        ->where('in_activo', '=', true)
        ->where('id_tab_fuente_financiamiento', '=', $id_tab_fuente_financiamiento)
        ->whereNotIn('id',$filtro_nu_financiamiento)
        ->orderby('id','ASC')
        ->get(); 

    return Response::json(array(
			'success' => true,
			'valido' => true,
			'data' => $tab_nu_financiamiento
		)); 

  }
  
      public function proyecto_ac( Request $request)
  {

        $ejecutor        = $request->ejecutor;

        $tab_presupuesto_egreso = tab_presupuesto_egreso::select( 'id','nu_presupuesto','de_presupuesto')
        ->where('id_tab_ejecutor', '=', $ejecutor)
        ->where('id_tab_ejercicio_fiscal', '=', Session::get('ejercicio'))
        ->orderby('id','ASC')
        ->get(); 

    return Response::json(array(
			'success' => true,
			'valido' => true,
			'data' => $tab_presupuesto_egreso
		)); 

  }  
  
      public function proyecto_ae( Request $request)
  {

        $proyecto_ac        = $request->proyecto_ac;

        $tab_accion_especifica = tab_accion_especifica::select( 'id','nu_accion_especifica','de_accion_especifica')
        ->where('id_tab_presupuesto_egreso', '=', $proyecto_ac)
        ->orderby('id','ASC')
        ->get(); 

    return Response::json(array(
			'success' => true,
			'valido' => true,
			'data' => $tab_accion_especifica
		)); 

  } 
  
      public function partida_gasto( Request $request)
  {
        
        $filtro_gasto = tab_credito_adicional_partidas::select('id_tab_catalogo_partida')
        ->whereNotNull('id_tab_catalogo_partida')
        ->where('id_tab_solicitud', '=', $request->solicitud)
        ->get();        

        $tab_partida_egreso = tab_catalogo_partida::select( 'id','nu_partida','de_partida')
        ->where('in_activo', '=', true)
        ->where('id_tipo_partida', '=', 1)                
        ->where('nu_nivel', '=', 5)
        ->whereNotIn('id',$filtro_gasto)                
        ->orderby('id','ASC')
        ->get(); 

    return Response::json(array(
			'success' => true,
			'valido' => true,
			'data' => $tab_partida_egreso
		)); 

  }  

    public function guardar(Request $request, $id = NULL)
    {
        DB::beginTransaction();

        if($id!=''||$id!=null){

          $validador = Validator::make( $request->all(), tab_credito_adicional::$validarEditar);
          if ($validador->fails()) {

              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }
          try {
              

             
            $fecha_credito = Carbon::parse($request->fecha_credito)->format('Y-m-d');           
            $fecha_oficio = Carbon::parse($request->fecha_oficio)->format('Y-m-d');  
            
            $tabla = tab_credito_adicional::find($id);
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->id_tab_fuente_financiamiento = $request->fuente_financiamiento;
            $tabla->id_tab_nu_financiamiento = $request->nu_financiamiento;
            $tabla->id_tab_tipo_credito_adicional = $request->tipo_credito;
            $tabla->id_tab_usuario =  Auth::user()->id;
            $tabla->fe_credito = $fecha_credito;   
            $tabla->de_articulo = $request->articulo_ley;
            $tabla->fe_oficio = $fecha_oficio;
            $tabla->de_credito = $request->tx_descripcion;
            $tabla->de_justificacion = $request->tx_justificacion;            
            $tabla->id_tab_ejercicio_fiscal = Session::get('ejercicio'); 
            $tabla->save();  
            

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

          $validador = Validator::make( $request->all(), tab_credito_adicional::$validarCrear);
          if ($validador->fails()) {
             
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }
        

          try {
              
              
            $fecha_credito = Carbon::parse($request->fecha_credito)->format('Y-m-d');           
            $fecha_oficio = Carbon::parse($request->fecha_oficio)->format('Y-m-d');  
            
            $tabla = new tab_credito_adicional;
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->id_tab_fuente_financiamiento = $request->fuente_financiamiento;
            $tabla->id_tab_nu_financiamiento = $request->nu_financiamiento;
            $tabla->id_tab_tipo_credito_adicional = $request->tipo_credito;
            $tabla->id_tab_usuario =  Auth::user()->id;
            $tabla->fe_credito = $fecha_credito;   
            $tabla->de_articulo = $request->articulo_ley;
            $tabla->fe_oficio = $fecha_oficio;
            $tabla->de_credito = $request->tx_descripcion;
            $tabla->de_justificacion = $request->tx_justificacion;            
            $tabla->id_tab_ejercicio_fiscal = Session::get('ejercicio'); 
            $tabla->save();                         
                
                        

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

   
    public function guardarPartidaIngreso(Request $request, $id = NULL)
    {
        DB::beginTransaction();

          $validador = Validator::make( $request->all(), tab_credito_adicional_partidas::$validarCrearIngreso);
          if ($validador->fails()) {
              Session::flash('msg_alerta_ingreso', 'Error!');
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }
        

          
          try {
              
            $tab_partida_ingreso = tab_partida_ingreso::where('id', '=', $request->partida_ingreso)
            ->first(); 
            
            $tabla = new tab_credito_adicional_partidas;
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->id_tab_partida_ingreso = $request->partida_ingreso;
            $tabla->id_tab_usuario = Auth::user()->id;
            $tabla->nu_partida = $tab_partida_ingreso->nu_partida;
            $tabla->monto = $request->monto_ingreso;         
            $tabla->save();
            
            DB::commit();

            Session::flash('msg_side_overlay', 'Partida agregada con Exito!');
            return Redirect::to('/proceso/ruta/datos/'.$request->solicitud);

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        
    }  
    
    public function guardarPartidaGasto(Request $request, $id = NULL)
    {
        DB::beginTransaction();

          $validador = Validator::make( $request->all(), tab_credito_adicional_partidas::$validarCrearGasto);
          if ($validador->fails()) {
              Session::flash('msg_alerta_gasto', 'Error!');
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }
        

          
          try {
              
            $tab_catalogo_partida = tab_catalogo_partida::where('id', '=', $request->partida_gasto)
            ->first(); 
            
            $tabla = new tab_credito_adicional_partidas;
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->id_tab_ejecutor = $request->ejecutor;
            $tabla->id_tab_tab_presupuesto_egreso = $request->proyecto_ac;
            $tabla->id_tab_accion_especifica = $request->accion_especifica;
            $tabla->id_tab_catalogo_partida = $request->partida_gasto;
            $tabla->id_tab_tipo_ingreso = $request->tipo_ingreso;
            $tabla->id_tab_aplicacion = $request->aplicacion;
            $tabla->id_tab_ambito = $request->ambito;
            $tabla->id_tab_clasificacion_economica = $request->clasificacion_economica;
            $tabla->id_tab_area_estrategica = $request->area_estrategica;
            $tabla->id_tab_tipo_gasto = $request->tipo_gasto;
            $tabla->id_tab_usuario = Auth::user()->id;
            $tabla->nu_partida = $tab_catalogo_partida->nu_partida;
            $tabla->monto = $request->monto_gasto;         
            $tabla->save();
            
            DB::commit();

            Session::flash('msg_side_overlay', 'Partida agregada con Exito!');
            return Redirect::to('/proceso/ruta/datos/'.$request->solicitud);

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        
    } 
    
    public function generar($id,Request $request)
    {
        DB::beginTransaction();
        try {

            $tab_credito_adicional = tab_credito_adicional::find( $request->get("id"));
            $tab_credito_adicional->in_procesado = true;
            $tab_credito_adicional->save();
            
            $tab_nu_financiamiento = tab_nu_financiamiento::where('id', '=', $tab_credito_adicional->id_tab_nu_financiamiento)
            ->first();             
            
            $partidas_gasto = tab_credito_adicional_partidas::whereNotNull('id_tab_catalogo_partida')
            ->where('id_tab_solicitud', '=', $tab_credito_adicional->id_tab_solicitud)
            ->get();   
        
        
            foreach ($partidas_gasto as $key => $value) {
                
            $tab_catalogo_partida = tab_catalogo_partida::where('id', '=', $value->id_tab_catalogo_partida)
            ->first(); 
            $tab_ejecutor = tab_ejecutor::where('id','=',$value->id_tab_ejecutor)->first();
            $nu_partida =  $tab_catalogo_partida->nu_pa.$tab_catalogo_partida->nu_ge.$tab_catalogo_partida->nu_es.$tab_catalogo_partida->nu_se.$tab_catalogo_partida->nu_sse.$tab_nu_financiamiento->nu_financiamiento;
            $tab_presupuesto_egreso = tab_presupuesto_egreso::where('id','=',$value->id_tab_tab_presupuesto_egreso)->first();
            $tab_sector_presupuesto = tab_sector_presupuesto::where('id','=',$tab_presupuesto_egreso->id_tab_sector_presupuesto)->first();
            $tab_accion_especifica = tab_accion_especifica::where('id','=',$value->id_tab_accion_especifica)->first();

            $co_categoria =  $tab_ejecutor->nu_ejecutor.'.'.$tab_sector_presupuesto->nu_sector_presupuesto.'.'.$tab_presupuesto_egreso->nu_presupuesto.'.00.'.$tab_accion_especifica->nu_accion_especifica.'.'.$tab_catalogo_partida->nu_pa.'.'.$tab_catalogo_partida->nu_es.'.'.$tab_catalogo_partida->nu_es.'.'.$tab_catalogo_partida->nu_se.'.'.$tab_catalogo_partida->nu_sse.'.'.$tab_nu_financiamiento->nu_financiamiento;
            
            $tab_partida_egreso = new tab_partida_egreso;                
            $tab_partida_egreso->id_tab_accion_especifica = $value->id_tab_accion_especifica;
            $tab_partida_egreso->co_partida = $tab_catalogo_partida->co_partida;
            $tab_partida_egreso->nu_partida = $nu_partida;            
            $tab_partida_egreso->de_partida = $tab_catalogo_partida->de_partida;            
            $tab_partida_egreso->nu_pa = $tab_catalogo_partida->nu_pa;
            $tab_partida_egreso->nu_ge = $tab_catalogo_partida->nu_ge;
            $tab_partida_egreso->nu_es = $tab_catalogo_partida->nu_es;
            $tab_partida_egreso->nu_se = $tab_catalogo_partida->nu_se;
            $tab_partida_egreso->nu_sse = $tab_catalogo_partida->nu_sse;    
            $tab_partida_egreso->nu_nivel = $tab_catalogo_partida->nu_nivel;  
            $tab_partida_egreso->co_categoria = $co_categoria;
            $tab_partida_egreso->id_tab_nu_financiamiento = $tab_nu_financiamiento->id;
            $tab_partida_egreso->nu_financiamiento = $tab_nu_financiamiento->nu_financiamiento;          
            $tab_partida_egreso->id_tab_aplicacion = $value->id_tab_aplicacion;
            $tab_partida_egreso->id_tab_catalogo_partida = $value->id_tab_catalogo_partida;
            $tab_partida_egreso->id_tab_tipo_ingreso =  $value->id_tab_tipo_ingreso;
            $tab_partida_egreso->id_tab_ambito = $value->id_tab_ambito; 
            $tab_partida_egreso->id_tab_clasificacion_economica = $value->id_tab_clasificacion_economica;
            $tab_partida_egreso->id_tab_area_estrategica = $value->id_tab_area_estrategica;
            $tab_partida_egreso->id_tab_tipo_gasto = $value->id_tab_tipo_gasto;                
            $tab_partida_egreso->id_tab_ejecutor = $value->id_tab_ejecutor;            
            $tab_partida_egreso->id_tab_sector_presupuesto = $tab_presupuesto_egreso->id_tab_sector_presupuesto;
            $tab_partida_egreso->id_tab_ejercicio_fiscal = Session::get('ejercicio');
            $tab_partida_egreso->mo_inicial = 0;
            $tab_partida_egreso->mo_modificado = $value->monto;
            $tab_partida_egreso->mo_aprobado = $value->monto;    
            $tab_partida_egreso->mo_comprometido = 0;            
            $tab_partida_egreso->mo_causado = 0;
            $tab_partida_egreso->mo_pagado = 0;
            $tab_partida_egreso->mo_disponible = $value->monto;  
            $tab_partida_egreso->mo_aumento = $value->monto;
            $tab_partida_egreso->mo_disminucion = 0;              
            $tab_partida_egreso->save();                 

             
                
            } 
            
            $partidas_ingreso = tab_credito_adicional_partidas::select('id_tab_partida_ingreso','monto')
            ->whereNotNull('id_tab_partida_ingreso')
            ->where('id_tab_solicitud', '=', $tab_credito_adicional->id_tab_solicitud)
            ->get(); 
            
            
            foreach ($partidas_ingreso as $key => $value) {

            $tab_partida_ingreso = tab_partida_ingreso::find( $value->id_tab_partida_ingreso);
            $tab_partida_ingreso->mo_modificado = $tab_partida_ingreso->mo_modificado + $value->monto;
            $tab_partida_ingreso->mo_devengado = $tab_partida_ingreso->mo_devengado + $value->monto;    
            $tab_partida_ingreso->save();                
                
            }     
            
            $tab_ruta = tab_ruta::find( $id);
            $tab_ruta->in_datos = true;
            $tab_ruta->save();            

            DB::commit();

            Session::flash('msg_side_overlay', 'Credito Adicional Generado con Exito!');
            return Redirect::to('/proceso/ruta/lista/'.$tab_credito_adicional->id_tab_solicitud);

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }     
    
    public function eliminarPartida(Request $request)
    {
        DB::beginTransaction();
        try {

            $tabla = tab_credito_adicional_partidas::find( $request->get("id"));
            $tabla->delete();
            


            DB::commit();

            Session::flash('msg_side_overlay', 'Partida borrada con Exito!');
            return Redirect::to('/proceso/ruta/datos/'.$tabla->id_tab_solicitud);

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }    

}
