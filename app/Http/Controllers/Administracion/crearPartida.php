<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Administracion\tab_creacion_partida;
use gobela\Models\Administracion\tab_partida_egreso;
use gobela\Models\Administracion\tab_fuente_financiamiento;
use gobela\Models\Administracion\tab_nu_financiamiento;
use gobela\Models\Administracion\tab_ejecutor;
use gobela\Models\Administracion\tab_tipo_ingreso;
use gobela\Models\Administracion\tab_ambito;
use gobela\Models\Administracion\tab_aplicacion;
use gobela\Models\Administracion\tab_clasificacion_economica;
use gobela\Models\Administracion\tab_area_estrategica;
use gobela\Models\Administracion\tab_tipo_gasto;
use gobela\Models\Administracion\tab_presupuesto_egreso;
use gobela\Models\Administracion\tab_sector_presupuesto;
use gobela\Models\Administracion\tab_accion_especifica;
use gobela\Models\Administracion\tab_catalogo_partida;
use gobela\Models\Proceso\tab_solicitud;
use gobela\Models\Proceso\tab_ruta;
use gobela\Models\Configuracion\tab_solicitud as tab_tipo_solicitud;
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
use Carbon\Carbon;
//*******************************//
use Illuminate\Http\Request;

use gobela\Http\Requests;
use gobela\Http\Controllers\Controller;

class crearPartida extends Controller
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
    public function lista( Request $request,$id, $ruta)
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
        
        $tab_creacion_partida = tab_creacion_partida::where('id_tab_solicitud', '=', $id)
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.crearPartida.lista')->with([
          'solicitud' => $id,
          'ruta' => $ruta,
          'tab_proceso' => $tab_proceso,
          'tab_tipo_solicitud' => $tab_tipo_solicitud,
          'tab_fuente_financiamiento'  => $tab_fuente_financiamiento,
          'tab_ejecutor' => $tab_ejecutor,
          'tab_tipo_ingreso' => $tab_tipo_ingreso,
          'tab_ambito' => $tab_ambito,
          'tab_aplicacion' => $tab_aplicacion,
          'tab_clasificacion_economica' => $tab_clasificacion_economica,
          'tab_area_estrategica' => $tab_area_estrategica,
          'tab_tipo_gasto' => $tab_tipo_gasto,
          'tab_creacion_partida' => $tab_creacion_partida,
          'tab_solicitud' => $tab_solicitud,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }
    
    public function listaAprobar( Request $request,$id, $ruta)
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

        $tab_solicitud = tab_solicitud::select( 'id', 'nu_solicitud','id_tab_tipo_solicitud')
        ->where('id', '=', $id)
        ->first();       
        
        $tab_proceso = tab_ruta::select( 't01.de_proceso')
        ->join('configuracion.tab_proceso as t01', 'proceso.tab_ruta.id_tab_proceso', '=', 't01.id')
        ->where('proceso.tab_ruta.id', '=', $ruta)
        ->first(); 
        
        $tab_ruta = tab_ruta::where('id', '=', $ruta)
        ->first();         
        
        $tab_tipo_solicitud = tab_tipo_solicitud::select( 'id', 'de_solicitud')
        ->where('id', '=', $tab_solicitud->id_tab_tipo_solicitud)
        ->first(); 
         
        
        $tab_creacion_partida = tab_creacion_partida::where('id_tab_solicitud', '=', $id)
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.crearPartida.listaAprobar')->with([
          'solicitud' => $id,
          'ruta' => $ruta,
          'tab_proceso' => $tab_proceso,
          'tab_ruta' => $tab_ruta,
          'tab_tipo_solicitud' => $tab_tipo_solicitud,
          'tab_creacion_partida' => $tab_creacion_partida,
          'tab_solicitud' => $tab_solicitud,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }    
    
       public function nu_financiamiento( Request $request)
  {

        $id_tab_fuente_financiamiento        = $request->fuente_financiamiento;

        $tab_nu_financiamiento = tab_nu_financiamiento::select( 'id','nu_financiamiento')
        ->where('in_activo', '=', true)
        ->where('id_tab_fuente_financiamiento', '=', $id_tab_fuente_financiamiento)
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

        $tab_partida_egreso = tab_catalogo_partida::select( 'id','nu_partida','de_partida')
        ->where('in_activo', '=', true)
        ->where('id_tipo_partida', '=', 1)                
        ->where('nu_nivel', '=', 5)              
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

          $validador = Validator::make( $request->all(), tab_creacion_partida::$validarEditar);
          if ($validador->fails()) {
              Session::flash('msg_alerta', 'Error!');
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {
              
            $tab_catalogo_partida = tab_catalogo_partida::where('id', '=', $request->partida_gasto)
            ->first(); 
            
            $tab_nu_financiamiento = tab_nu_financiamiento::where('id', '=', $request->nu_financiamiento)
            ->first();            
            
            $de_partida = $tab_catalogo_partida->nu_pa.'.'.$tab_catalogo_partida->nu_ge.'.'.$tab_catalogo_partida->nu_es.'.'.$tab_catalogo_partida->nu_se.' - '.$tab_catalogo_partida->de_partida;
            $nu_partida = $tab_catalogo_partida->nu_pa.$tab_catalogo_partida->nu_ge.$tab_catalogo_partida->nu_es.$tab_catalogo_partida->nu_se.$request->desagregado.$tab_nu_financiamiento->nu_financiamiento;
                    
                    
            $tabla = new tab_creacion_partida;
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->id_tab_fuente_financiamiento = $request->fuente_financiamiento;
            $tabla->id_tab_nu_financiamiento = $request->nu_financiamiento;
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
            $tabla->desagregado = $request->desagregado;
            $tabla->de_partida = $de_partida;
            $tabla->nu_partida = $nu_partida;
            $tabla->id_tab_ejercicio_fiscal = Session::get('ejercicio'); 
            $tabla->save();
            
            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = true;
            $tab_ruta->save();            

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/proceso/ruta/datos/'.$request->solicitud);

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }else{

          $validador = Validator::make( $request->all(), tab_creacion_partida::$validarCrear);
          if ($validador->fails()) {
            Session::flash('msg_alerta', 'Error!');              
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {
              
            $tab_catalogo_partida = tab_catalogo_partida::where('id', '=', $request->partida_gasto)
            ->first(); 
            
            $tab_nu_financiamiento = tab_nu_financiamiento::where('id', '=', $request->nu_financiamiento)
            ->first();            
            
            $de_partida = $tab_catalogo_partida->nu_pa.'.'.$tab_catalogo_partida->nu_ge.'.'.$tab_catalogo_partida->nu_es.'.'.$tab_catalogo_partida->nu_se.' - '.$tab_catalogo_partida->de_partida;
            $nu_partida = $tab_catalogo_partida->nu_pa.$tab_catalogo_partida->nu_ge.$tab_catalogo_partida->nu_es.$tab_catalogo_partida->nu_se.$request->desagregado.$tab_nu_financiamiento->nu_financiamiento;
                    
                    
            $tabla = new tab_creacion_partida;
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->id_tab_fuente_financiamiento = $request->fuente_financiamiento;
            $tabla->id_tab_nu_financiamiento = $request->nu_financiamiento;
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
            $tabla->desagregado = $request->desagregado;
            $tabla->de_partida = $de_partida;
            $tabla->nu_partida = $nu_partida;
            $tabla->id_tab_ejercicio_fiscal = Session::get('ejercicio'); 
            $tabla->save();
            
            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = true;
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
    
    public function guardarAprobar(Request $request)
    {
        DB::beginTransaction();

          try {
              
            $partidas = tab_creacion_partida::where('id_tab_solicitud', '=', $request->id)
            ->get();              
              
            foreach ($partidas as $key => $value) {
            
            $tab_catalogo_partida = tab_catalogo_partida::where('id', '=', $value->id_tab_catalogo_partida)
            ->first(); 
            
            $tab_nu_financiamiento = tab_nu_financiamiento::where('id', '=', $value->id_tab_nu_financiamiento)
            ->first();   
            
            $tab_ejecutor = tab_ejecutor::where('id','=',$value->id_tab_ejecutor)->first();
            $nu_partida = $tab_catalogo_partida->nu_pa.$tab_catalogo_partida->nu_ge.$tab_catalogo_partida->nu_es.$tab_catalogo_partida->nu_se.$value->desagregado.$tab_nu_financiamiento->nu_financiamiento;
            $tab_presupuesto_egreso = tab_presupuesto_egreso::where('id','=',$value->id_tab_tab_presupuesto_egreso)->first();
            $tab_sector_presupuesto = tab_sector_presupuesto::where('id','=',$tab_presupuesto_egreso->id_tab_sector_presupuesto)->first();
            $tab_accion_especifica = tab_accion_especifica::where('id','=',$value->id_tab_accion_especifica)->first();

            $co_categoria =  $tab_ejecutor->nu_ejecutor.'.'.$tab_sector_presupuesto->nu_sector_presupuesto.'.'.$tab_presupuesto_egreso->nu_presupuesto.'.00.'.$tab_accion_especifica->nu_accion_especifica.'.'.$tab_catalogo_partida->nu_pa.'.'.$tab_catalogo_partida->nu_es.'.'.$tab_catalogo_partida->nu_es.'.'.$tab_catalogo_partida->nu_se.'.'.$value->desagregado.'.'.$tab_nu_financiamiento->nu_financiamiento;
                        
            
            $co_partida = $tab_catalogo_partida->nu_pa.'.'.$tab_catalogo_partida->nu_ge.'.'.$tab_catalogo_partida->nu_es.'.'.$tab_catalogo_partida->nu_se.'.'.$value->desagregado;
                    
                    
            $tab_partida_egreso = new tab_partida_egreso;                
            $tab_partida_egreso->id_tab_accion_especifica = $value->id_tab_accion_especifica;
            $tab_partida_egreso->co_partida = $co_partida;
            $tab_partida_egreso->nu_partida = $nu_partida;            
            $tab_partida_egreso->de_partida = $tab_catalogo_partida->de_partida;            
            $tab_partida_egreso->nu_pa = $tab_catalogo_partida->nu_pa;
            $tab_partida_egreso->nu_ge = $tab_catalogo_partida->nu_ge;
            $tab_partida_egreso->nu_es = $tab_catalogo_partida->nu_es;
            $tab_partida_egreso->nu_se = $tab_catalogo_partida->nu_se;
            $tab_partida_egreso->nu_sse = $value->desagregado;    
            $tab_partida_egreso->nu_nivel = $tab_catalogo_partida->nu_nivel;  
            $tab_partida_egreso->co_categoria = $co_categoria;
            $tab_partida_egreso->id_tab_nu_financiamiento = $tab_nu_financiamiento->id;
            $tab_partida_egreso->nu_financiamiento = $tab_nu_financiamiento->nu_financiamiento;          
            $tab_partida_egreso->id_tab_aplicacion = $value->id_tab_aplicacion;
            $tab_partida_egreso->id_tab_catalogo_partida = $value->id_tab_catalogo_partida;
            $tab_partida_egreso->id_tab_tipo_ingreso = $value->id_tab_tipo_ingreso;
            $tab_partida_egreso->id_tab_ambito = $value->id_tab_ambito; 
            $tab_partida_egreso->id_tab_clasificacion_economica = $value->id_tab_clasificacion_economica;
            $tab_partida_egreso->id_tab_area_estrategica = $value->id_tab_area_estrategica;
            $tab_partida_egreso->id_tab_tipo_gasto = $value->id_tab_tipo_gasto;                
            $tab_partida_egreso->id_tab_ejecutor = $value->id_tab_ejecutor;            
            $tab_partida_egreso->id_tab_sector_presupuesto = $tab_presupuesto_egreso->id_tab_sector_presupuesto;
            $tab_partida_egreso->id_tab_ejercicio_fiscal = Session::get('ejercicio');
            $tab_partida_egreso->mo_inicial = 0;
            $tab_partida_egreso->mo_modificado = 0;
            $tab_partida_egreso->mo_aprobado = 0;    
            $tab_partida_egreso->mo_comprometido = 0;            
            $tab_partida_egreso->mo_causado = 0;
            $tab_partida_egreso->mo_pagado = 0;
            $tab_partida_egreso->mo_disponible = 0;  
            $tab_partida_egreso->mo_aumento = 0;
            $tab_partida_egreso->mo_disminucion = 0;              
            $tab_partida_egreso->save();             
            
            $tab_creacion_partida = tab_creacion_partida::find( $value->id);
            $tab_creacion_partida->in_procesado = true;
            $tab_creacion_partida->save();             
            
            }
            
            $ruta = tab_ruta::where('id_tab_solicitud', '=', $request->id)
            ->where('in_actual', '=', true)
            ->first();            

            
            $tab_ruta = tab_ruta::find( $ruta->id);
            $tab_ruta->in_datos = true;
            $tab_ruta->id_tab_estatus = 2;
            $tab_ruta->save();            
            
            DB::commit();

            Session::flash('msg_side_overlay', 'Proceso Aprobado con Exito!');
            return Redirect::to('/proceso/ruta/lista/'.$request->id);

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        
    }    
    
    
    public function guardarRechazar(Request $request)
    {
        DB::beginTransaction();

          try {
              

            
            $ruta = tab_ruta::where('id_tab_solicitud', '=', $request->id)
            ->where('in_actual', '=', true)
            ->first();            

            $tab_ruta = tab_ruta::find( $ruta->id);
            $tab_ruta->id_tab_estatus = 3;
            $tab_ruta->save();            
            
            DB::commit();

            Session::flash('msg_side_overlay', 'Proceso Rechazado con Exito!');
            return Redirect::to('/proceso/ruta/lista/'.$request->id);

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        
    }    
    
    public function eliminar( Request $request)
    {
      DB::beginTransaction();
      try {

        $tabla = tab_creacion_partida::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
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
