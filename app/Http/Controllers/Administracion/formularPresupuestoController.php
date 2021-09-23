<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Administracion\tab_formular_presupuesto;
use gobela\Models\Administracion\tab_tipo_presupuesto;
use gobela\Models\Administracion\tab_sector_presupuesto;
use gobela\Models\Administracion\tab_ejecutor;
use gobela\Models\Administracion\tab_formular_accion_especifica;
use gobela\Models\Administracion\tab_formular_partida;
use gobela\Models\Administracion\tab_tipo_ingreso;
use gobela\Models\Administracion\tab_ambito;
use gobela\Models\Administracion\tab_aplicacion;
use gobela\Models\Administracion\tab_clasificacion_economica;
use gobela\Models\Administracion\tab_area_estrategica;
use gobela\Models\Administracion\tab_tipo_gasto;
use gobela\Models\Administracion\tab_catalogo_partida;
use gobela\Models\Administracion\tab_presupuesto_egreso;
use gobela\Models\Administracion\tab_accion_especifica;
use gobela\Models\Administracion\tab_partida_egreso;
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
//*******************************//
use Illuminate\Http\Request;

use gobela\Http\Requests;
use gobela\Http\Controllers\Controller;

class formularPresupuestoController extends Controller
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
    public function lista( Request $request)
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

        $tab_formular_presupuesto = tab_formular_presupuesto::select(DB::raw("coalesce(sum((select sum(mo_partida) from administracion.tab_formular_accion_especifica inner join administracion.tab_formular_partida on (tab_formular_partida.id_tab_formular_accion_especifica = tab_formular_accion_especifica.id) where tab_formular_accion_especifica.in_activo = true and tab_formular_partida.in_activo = true and tab_formular_accion_especifica.id_tab_formular_presupuesto=tab_formular_presupuesto.id)),0.00) as monto_cargado"), 'administracion.tab_formular_presupuesto.id','nu_presupuesto', 'de_presupuesto', 'administracion.tab_formular_presupuesto.in_activo', 'administracion.tab_formular_presupuesto.in_cargado','id_tab_ejercicio_fiscal','de_tipo_presupuesto','de_ejecutor','mo_presupuesto','nu_sector_presupuesto','de_sector_presupuesto')
        ->join('administracion.tab_tipo_presupuesto as t01','t01.id','=','administracion.tab_formular_presupuesto.id_tab_tipo_presupuesto')
        ->join('administracion.tab_ejecutor as t02','t02.id','=','administracion.tab_formular_presupuesto.id_tab_ejecutor')
        ->join('administracion.tab_sector_presupuesto as t03','t03.id','=','administracion.tab_formular_presupuesto.id_tab_sector_presupuesto')                
        ->groupBy('administracion.tab_formular_presupuesto.id','t01.de_tipo_presupuesto','t02.de_ejecutor','t03.nu_sector_presupuesto','t03.de_sector_presupuesto')
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.formularPresupuesto.lista')->with([
          'tab_formular_presupuesto' => $tab_formular_presupuesto,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }

    public function accionEspecificaLista(Request $request,$id_tab_formular_presupuesto)
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
        
        $tab_formular_presupuesto = tab_formular_presupuesto::select( 'in_cargado')              
        ->where('id', '=', $id_tab_formular_presupuesto)
        ->first();        

        $tab_formular_accion_especifica = tab_formular_accion_especifica::select(DB::raw("coalesce(sum((select sum(mo_partida) from administracion.tab_formular_partida where in_activo = true and id_tab_formular_accion_especifica=tab_formular_accion_especifica.id)),0.00) as monto_cargado"),'administracion.tab_formular_accion_especifica.id','nu_accion_especifica', 'de_accion_especifica', 'administracion.tab_formular_accion_especifica.in_activo')
        ->where('id_tab_formular_presupuesto', '=', $id_tab_formular_presupuesto)
        ->groupBy('administracion.tab_formular_accion_especifica.id')                
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.formularPresupuesto.accionEspecifica.lista')->with([
          'tab_formular_accion_especifica' => $tab_formular_accion_especifica,
          'id_tab_formular_presupuesto' => $id_tab_formular_presupuesto,
          'in_cargado' => $tab_formular_presupuesto->in_cargado,            
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }    
    
    public function partidaLista(Request $request,$id_tab_formular_accion_especifica)
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

        $tab_formular_accion_especifica = tab_formular_accion_especifica::select( 'id_tab_formular_presupuesto','in_cargado')              
        ->join('administracion.tab_formular_presupuesto as t01','t01.id','=','administracion.tab_formular_accion_especifica.id_tab_formular_presupuesto')                
        ->where('administracion.tab_formular_accion_especifica.id', '=', $id_tab_formular_accion_especifica)
        ->first();        
        
        $tab_formular_partida = tab_formular_partida::select( 'id','co_partida','de_partida', 'mo_partida', 'in_activo', 'created_at', 'updated_at')
        ->where('id_tab_formular_accion_especifica', '=', $id_tab_formular_accion_especifica)
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.formularPresupuesto.partida.lista')->with([
          'tab_formular_partida' => $tab_formular_partida,
          'id_tab_formular_presupuesto' => $tab_formular_accion_especifica->id_tab_formular_presupuesto,
          'id_tab_formular_accion_especifica' => $id_tab_formular_accion_especifica,
          'in_cargado' => $tab_formular_accion_especifica->in_cargado,
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
     * @return \Illuminate\Http\Response
     */
    public function nuevo()
    {
        $data = array( "id" => null);
        
        $tab_tipo_presupuesto = tab_tipo_presupuesto::select( 'id', 'de_tipo_presupuesto', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();      
        
        $tab_ejecutor = tab_ejecutor::select( 'id', 'de_ejecutor', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();      
              
       
        $tab_sector_presupuesto = tab_sector_presupuesto::select( 'id','nu_sector_presupuesto', 'de_sector_presupuesto', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();             

        return View::make('administracion.formularPresupuesto.nuevo')->with([
            'data'  => $data,
            'tab_tipo_presupuesto' => $tab_tipo_presupuesto,
            'tab_ejecutor' => $tab_ejecutor,
            'tab_sector_presupuesto' => $tab_sector_presupuesto
        ]);
    }

    public function accionEspecificaNuevo($id_tab_formular_presupuesto)
    {

        return View::make('administracion.formularPresupuesto.accionEspecifica.nuevo')->with([
            'id_tab_formular_presupuesto'  => $id_tab_formular_presupuesto
        ]);
    }    
    
    public function partidaNuevo($id_tab_formular_accion_especifica)
    {
        
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

        $filtro_catalogo = tab_formular_partida::select('id_tab_catalogo_partida')
        ->where('id_tab_formular_accion_especifica', '=', $id_tab_formular_accion_especifica)                
        ->get();        
        
        $tab_catalogo_partida = tab_catalogo_partida::select( 'id','co_partida', 'de_partida', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->where('id_tipo_partida', '=', 1)                
        ->where('nu_nivel', '=', 5)
        ->whereNotIn('id',$filtro_catalogo)         
        ->orderby('id','ASC')
        ->get();        

        return View::make('administracion.formularPresupuesto.partida.nuevo')->with([
            'id_tab_formular_accion_especifica'  => $id_tab_formular_accion_especifica,
            'tab_tipo_ingreso'  => $tab_tipo_ingreso,
            'tab_ambito'  => $tab_ambito,
            'tab_aplicacion'  => $tab_aplicacion,
            'tab_clasificacion_economica'  => $tab_clasificacion_economica,
            'tab_area_estrategica'  => $tab_area_estrategica,
            'tab_tipo_gasto'  => $tab_tipo_gasto,
            'tab_catalogo_partida'  => $tab_catalogo_partida
        ]);
    }        
        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function editar($id)
    {
        $tab_tipo_presupuesto = tab_tipo_presupuesto::select( 'id', 'de_tipo_presupuesto', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();      
        
        $tab_ejecutor = tab_ejecutor::select( 'id', 'de_ejecutor', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();   
        
        $tab_sector_presupuesto = tab_sector_presupuesto::select( 'id','nu_sector_presupuesto', 'de_sector_presupuesto', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();         
        
        $data = tab_formular_presupuesto::select( 'administracion.tab_formular_presupuesto.id','nu_presupuesto', 'de_presupuesto', 'administracion.tab_formular_presupuesto.in_activo', 'administracion.tab_formular_presupuesto.in_cargado','id_tab_ejercicio_fiscal','de_tipo_presupuesto','id_tab_ejecutor','de_ejecutor','mo_presupuesto','id_tab_tipo_presupuesto','nu_sector_presupuesto','id_tab_sector_presupuesto','de_sector_presupuesto')
        ->join('administracion.tab_tipo_presupuesto as t01','t01.id','=','administracion.tab_formular_presupuesto.id_tab_tipo_presupuesto')
        ->join('administracion.tab_ejecutor as t02','t02.id','=','administracion.tab_formular_presupuesto.id_tab_ejecutor')
        ->join('administracion.tab_sector_presupuesto as t03','t03.id','=','administracion.tab_formular_presupuesto.id_tab_sector_presupuesto')                
        ->where('administracion.tab_formular_presupuesto.id', '=', $id)
        ->first();
        

        return View::make('administracion.formularPresupuesto.editar')->with([
            'data'  => $data,
            'tab_tipo_presupuesto' => $tab_tipo_presupuesto,
            'tab_ejecutor' => $tab_ejecutor,            
            'tab_sector_presupuesto' => $tab_sector_presupuesto
        ]);
    }
    
    public function accionEspecificaEditar($id)
    {
        $data = tab_formular_accion_especifica::select( 'id','id_tab_formular_presupuesto', 'nu_accion_especifica', 'de_accion_especifica', 'in_activo', 'created_at', 'updated_at')
        ->where('id', '=', $id)
        ->first();

        return View::make('administracion.formularPresupuesto.accionEspecifica.editar')->with([
            'data'  => $data
        ]);
    }    

    public function PartidaEditar($id)
    {
        $data = tab_formular_partida::select( 'id','id_tab_formular_accion_especifica','id_tab_catalogo_partida', 'id_tab_ambito', 'id_tab_aplicacion', 'id_tab_tipo_ingreso','id_tab_clasificacion_economica','id_tab_area_estrategica','id_tab_tipo_gasto','mo_partida')              
        ->where('id', '=', $id)
        ->first();        
        
        $tab_tipo_ingreso = tab_tipo_ingreso::select( 'id','nu_tipo_ingreso', 'de_tipo_ingreso', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();          
        
        $tab_ambito = tab_ambito::select( 'id','nu_ambito', 'de_ambito', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();    
        
        $tab_aplicacion = tab_aplicacion::select( 'id','nu_aplicacion', 'de_aplicacion', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();  
        
        $tab_clasificacion_economica = tab_clasificacion_economica::select( 'id','tx_sigla', 'de_clasificacion_economica', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();       
        
        $tab_area_estrategica = tab_area_estrategica::select( 'id','tx_sigla', 'de_area_estrategica', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();    
        
        $tab_tipo_gasto = tab_tipo_gasto::select( 'id','tx_sigla', 'de_tipo_gasto', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();        

        $filtro_catalogo = tab_formular_partida::select('id_tab_catalogo_partida')
        ->where('id_tab_formular_accion_especifica', '=', $data->id_tab_formular_accion_especifica) 
        ->whereNotIn('id',array($data->id))                
        ->get();        
        
        $tab_catalogo_partida = tab_catalogo_partida::select( 'id','co_partida', 'de_partida', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->where('id_tipo_partida', '=', 1)                 
        ->where('nu_nivel', '=', 5)
        ->whereNotIn('id',$filtro_catalogo)         
        ->orderby('id','ASC')
        ->get();         

        return View::make('administracion.formularPresupuesto.partida.editar')->with([
            'data'  => $data,
            'id_tab_formular_accion_especifica'  => $data->id_tab_formular_accion_especifica,
            'tab_tipo_ingreso'  => $tab_tipo_ingreso,
            'tab_ambito'  => $tab_ambito,
            'tab_aplicacion'  => $tab_aplicacion,
            'tab_clasificacion_economica'  => $tab_clasificacion_economica,
            'tab_area_estrategica'  => $tab_area_estrategica,
            'tab_tipo_gasto'  => $tab_tipo_gasto,
            'tab_catalogo_partida'  => $tab_catalogo_partida
        ]);
    }    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request, $id = NULL)
    {
        DB::beginTransaction();

        if($id!=''||$id!=null){

          $validador = Validator::make( $request->all(), tab_formular_presupuesto::$validarEditar);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = tab_formular_presupuesto::find($id);
            $tabla->id_tab_tipo_presupuesto = $request->tipo_presupuesto;
            $tabla->id_tab_ejecutor = $request->ejecutor;
            $tabla->id_tab_sector_presupuesto = $request->sector_presupuesto;
            $tabla->nu_presupuesto = $request->codigo;
            $tabla->de_presupuesto = $request->descripcion; 
            $tabla->id_tab_ejercicio_fiscal = Session::get('ejercicio');
            $tabla->mo_presupuesto = $request->monto;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/administracion/formularPresupuesto/lista');

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }else{

          $validador = Validator::make( $request->all(), tab_formular_presupuesto::$validarCrear);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = new tab_formular_presupuesto;
            $tabla->id_tab_tipo_presupuesto = $request->tipo_presupuesto;
            $tabla->id_tab_ejecutor = $request->ejecutor;
            $tabla->id_tab_sector_presupuesto = $request->sector_presupuesto;
            $tabla->nu_presupuesto = $request->codigo;
            $tabla->de_presupuesto = $request->descripcion;
            $tabla->id_tab_ejercicio_fiscal = Session::get('ejercicio');
            $tabla->mo_presupuesto = $request->monto;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            return Redirect::to('/administracion/formularPresupuesto/lista');

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }
    }

    public function accionEspecificaGuardar(Request $request, $id = NULL)
    {
        DB::beginTransaction();

        if($id!=''||$id!=null){

          $validador = Validator::make( $request->all(), tab_formular_accion_especifica::$validarEditar);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = tab_formular_accion_especifica::find($id);
            $tabla->nu_accion_especifica = $request->codigo;
            $tabla->de_accion_especifica = $request->descripcion;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/administracion/formularPresupuesto/accionEspecifica/lista/'.$request->id_tab_formular_presupuesto);

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }else{

          $validador = Validator::make( $request->all(), tab_formular_accion_especifica::$validarCrear);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = new tab_formular_accion_especifica;
            $tabla->id_tab_formular_presupuesto = $request->id_tab_formular_presupuesto;
            $tabla->nu_accion_especifica = $request->codigo;
            $tabla->de_accion_especifica = $request->descripcion;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            return Redirect::to('/administracion/formularPresupuesto/accionEspecifica/lista/'.$request->id_tab_formular_presupuesto);

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }
    }    
    
    public function partidaGuardar(Request $request, $id = NULL)
    {
        
        $tab_catalogo_partida = tab_catalogo_partida::select( 'co_partida','in_movimiento','nu_nivel','nu_pa','nu_ge','nu_es','nu_se','nu_sse','de_partida')              
        ->where('id', '=', $request->partida)
        ->first();         
        
        
        DB::beginTransaction();

        if($id!=''||$id!=null){

          $validador = Validator::make( $request->all(), tab_formular_partida::$validarEditar);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = tab_formular_partida::find($id);
            $tabla->id_tab_formular_accion_especifica = $request->id_tab_formular_accion_especifica;
            $tabla->mo_partida = $request->monto;
            $tabla->co_partida = $tab_catalogo_partida->co_partida;
            $tabla->nu_nivel = $tab_catalogo_partida->nu_nivel;
            $tabla->nu_pa = $tab_catalogo_partida->nu_pa;
            $tabla->nu_ge = $tab_catalogo_partida->nu_ge;
            $tabla->nu_es = $tab_catalogo_partida->nu_es;
            $tabla->nu_se = $tab_catalogo_partida->nu_se;
            $tabla->nu_sse = $tab_catalogo_partida->nu_sse;    
            $tabla->de_partida = $tab_catalogo_partida->de_partida;
            $tabla->in_movimiento = $tab_catalogo_partida->in_movimiento;
            $tabla->id_tab_aplicacion = $request->aplicacion;
            $tabla->id_tab_catalogo_partida = $request->partida;
            $tabla->id_tab_tipo_ingreso = $request->tipo_ingreso;
            $tabla->id_tab_ambito = $request->ambito;   
            $tabla->id_tab_clasificacion_economica = $request->clasificacion_economica; 
            $tabla->id_tab_area_estrategica = $request->area_estrategica; 
            $tabla->id_tab_tipo_gasto = $request->tipo_gasto;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/administracion/formularPresupuesto/partida/lista/'.$request->id_tab_formular_accion_especifica);

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }else{

          $validador = Validator::make( $request->all(), tab_formular_partida::$validarCrear);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = new tab_formular_partida;
            $tabla->id_tab_formular_accion_especifica = $request->id_tab_formular_accion_especifica;
            $tabla->mo_partida = $request->monto;
            $tabla->co_partida = $tab_catalogo_partida->co_partida;
            $tabla->nu_nivel = $tab_catalogo_partida->nu_nivel;
            $tabla->nu_pa = $tab_catalogo_partida->nu_pa;
            $tabla->nu_ge = $tab_catalogo_partida->nu_ge;
            $tabla->nu_es = $tab_catalogo_partida->nu_es;
            $tabla->nu_se = $tab_catalogo_partida->nu_se;
            $tabla->nu_sse = $tab_catalogo_partida->nu_sse;    
            $tabla->de_partida = $tab_catalogo_partida->de_partida;
            $tabla->in_movimiento = $tab_catalogo_partida->in_movimiento;
            $tabla->id_tab_aplicacion = $request->aplicacion;
            $tabla->id_tab_catalogo_partida = $request->partida;
            $tabla->id_tab_tipo_ingreso = $request->tipo_ingreso;
            $tabla->id_tab_ambito = $request->ambito;  
            $tabla->id_tab_clasificacion_economica = $request->clasificacion_economica;
            $tabla->id_tab_area_estrategica = $request->area_estrategica;
            $tabla->id_tab_tipo_gasto = $request->tipo_gasto;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            return Redirect::to('/administracion/formularPresupuesto/partida/lista/'.$request->id_tab_formular_accion_especifica);

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }
    }    
    
    public function generar(Request $request)
    {
        DB::beginTransaction();

          try {
              
            $tab_formular_presupuesto = tab_formular_presupuesto::where('id','=',$request->get("id"))->first();
            
            $tab_presupuesto_egreso = new tab_presupuesto_egreso;
            $tab_presupuesto_egreso->id_tab_tipo_presupuesto = $tab_formular_presupuesto->id_tab_tipo_presupuesto;
            $tab_presupuesto_egreso->id_tab_ejecutor = $tab_formular_presupuesto->id_tab_ejecutor;
            $tab_presupuesto_egreso->id_tab_sector_presupuesto = $tab_formular_presupuesto->id_tab_sector_presupuesto;
            $tab_presupuesto_egreso->nu_presupuesto = $tab_formular_presupuesto->nu_presupuesto;
            $tab_presupuesto_egreso->de_presupuesto = $tab_formular_presupuesto->de_presupuesto;
            $tab_presupuesto_egreso->id_tab_ejercicio_fiscal = $tab_formular_presupuesto->id_tab_ejercicio_fiscal;
            $tab_presupuesto_egreso->save();
            
            $tab_formular_accion_especifica = tab_formular_accion_especifica::where('id_tab_formular_presupuesto','=',$request->get("id"))->where('in_activo','=',true)->get();            

            foreach ($tab_formular_accion_especifica as $formular_accion_especifica) { 

            $tab_accion_especifica = new tab_accion_especifica;
            $tab_accion_especifica->id_tab_presupuesto_egreso = $tab_presupuesto_egreso->id;
            $tab_accion_especifica->nu_accion_especifica = $formular_accion_especifica->nu_accion_especifica;
            $tab_accion_especifica->de_accion_especifica = $formular_accion_especifica->de_accion_especifica;
            $tab_accion_especifica->save();            
            
            $tab_formular_partida = tab_formular_partida::where('id_tab_formular_accion_especifica','=',$formular_accion_especifica->id)->where('in_activo','=',true)->get();            
            
            foreach ($tab_formular_partida as $formular_partida) {
                
            $nu_partida =  $formular_partida->nu_pa.$formular_partida->nu_ge.$formular_partida->nu_es.$formular_partida->nu_se.$formular_partida->nu_sse.'L0000';   
            
            $tab_ejecutor = tab_ejecutor::where('id','=',$tab_formular_presupuesto->id_tab_ejecutor)->first();            
            $tab_sector_presupuesto = tab_sector_presupuesto::where('id','=',$tab_formular_presupuesto->id_tab_sector_presupuesto)->first();                        
            $co_categoria =  $tab_ejecutor->nu_ejecutor.'.'.$tab_sector_presupuesto->nu_sector_presupuesto.'.'.$tab_formular_presupuesto->nu_presupuesto.'.00.'.$formular_accion_especifica->nu_accion_especifica.'.'.$formular_partida->nu_pa.'.'.$formular_partida->nu_es.'.'.$formular_partida->nu_es.'.'.$formular_partida->nu_se.'.'.$formular_partida->nu_sse.'.L0000';               

            $tab_partida_egreso = new tab_partida_egreso;                
            $tab_partida_egreso->id_tab_accion_especifica = $tab_accion_especifica->id;
            $tab_partida_egreso->co_partida = $formular_partida->co_partida;
            $tab_partida_egreso->nu_partida = $nu_partida;            
            $tab_partida_egreso->de_partida = $formular_partida->de_partida;            
            $tab_partida_egreso->nu_pa = $formular_partida->nu_pa;
            $tab_partida_egreso->nu_ge = $formular_partida->nu_ge;
            $tab_partida_egreso->nu_es = $formular_partida->nu_es;
            $tab_partida_egreso->nu_se = $formular_partida->nu_se;
            $tab_partida_egreso->nu_sse = $formular_partida->nu_sse;    
            $tab_partida_egreso->nu_nivel = $formular_partida->nu_nivel;  
            $tab_partida_egreso->co_categoria = $co_categoria;
            $tab_partida_egreso->id_tab_nu_financiamiento = 1;
            $tab_partida_egreso->nu_financiamiento = 'L0000';            
            $tab_partida_egreso->id_tab_aplicacion = $formular_partida->id_tab_aplicacion;
            $tab_partida_egreso->id_tab_catalogo_partida = $formular_partida->id_tab_catalogo_partida;
            $tab_partida_egreso->id_tab_tipo_ingreso = $formular_partida->id_tab_tipo_ingreso;
            $tab_partida_egreso->id_tab_ambito = $formular_partida->id_tab_ambito; 
            $tab_partida_egreso->id_tab_clasificacion_economica = $formular_partida->id_tab_clasificacion_economica;
            $tab_partida_egreso->id_tab_area_estrategica = $formular_partida->id_tab_area_estrategica;
            $tab_partida_egreso->id_tab_tipo_gasto = $formular_partida->id_tab_tipo_gasto;                
            $tab_partida_egreso->id_tab_ejecutor = $tab_formular_presupuesto->id_tab_ejecutor;            
            $tab_partida_egreso->id_tab_sector_presupuesto = $tab_formular_presupuesto->id_tab_sector_presupuesto;
            $tab_partida_egreso->id_tab_ejercicio_fiscal = $tab_formular_presupuesto->id_tab_ejercicio_fiscal;
            $tab_partida_egreso->mo_inicial = $formular_partida->mo_partida;
            $tab_partida_egreso->mo_modificado = 0;
            $tab_partida_egreso->mo_aprobado = $formular_partida->mo_partida;    
            $tab_partida_egreso->mo_comprometido = 0;            
            $tab_partida_egreso->mo_causado = 0;
            $tab_partida_egreso->mo_pagado = 0;
            $tab_partida_egreso->mo_disponible = 0;  
            $tab_partida_egreso->mo_aumento = 0;
            $tab_partida_egreso->mo_disminucion = 0;              
            $tab_partida_egreso->save();           
            
            }
            }
            
            $tab_formular_presupuesto->in_cargado = true;
            $tab_formular_presupuesto->save();
            
            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Generado con Exito!');
            return Redirect::to('/administracion/formularPresupuesto/lista');

          }catch (\Illuminate\Database\QueryException $e)
          {
              dd($e->getMessage());
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        
    }    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar( Request $request)
    {
      DB::beginTransaction();
      try {

        $tabla = tab_formular_presupuesto::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
        return Redirect::to('/administracion/formularPresupuesto/lista');

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }
    
    public function accionEspecificaEliminar( Request $request)
    {
      DB::beginTransaction();
      try {

        $tab_formular_partida = tab_formular_partida::select('id')
        ->where('id_tab_formular_accion_especifica', '=', $request->get("id"))                
        ->get();             
          
        foreach ($tab_formular_partida as $c) {
        $tabla_partida = tab_formular_partida::find( $c->id);
        $tabla_partida->delete();
        }        
        
        $tabla = tab_formular_accion_especifica::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
        return Redirect::to('/administracion/formularPresupuesto/accionEspecifica/lista/'.$tabla->id_tab_formular_presupuesto);

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }    
    
    public function partidaEliminar( Request $request)
    {
      DB::beginTransaction();
      try {

        $tabla = tab_formular_partida::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
        return Redirect::to('/administracion/formularPresupuesto/partida/lista/'.$tabla->id_tab_formular_accion_especifica);

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }    

            /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deshabilitar( $id)
    {
      DB::beginTransaction();
      try {

        $tabla = tab_formular_presupuesto::find( $id);
        $tabla->in_activo = false;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
        return Redirect::to('/administracion/formularPresupuesto/lista');

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }

    public function accionEspecificaDeshabilitar( $id)
    {
      DB::beginTransaction();
      try {

        $tabla = tab_formular_accion_especifica::find( $id);
        $tabla->in_activo = false;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
        return Redirect::to('/administracion/formularPresupuesto/accionEspecifica/lista/'.$tabla->id_tab_formular_presupuesto);

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }    
    
    public function partidaDeshabilitar( $id)
    {
      DB::beginTransaction();
      try {

        $tabla = tab_formular_partida::find( $id);
        $tabla->in_activo = false;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
        return Redirect::to('/administracion/formularPresupuesto/partida/lista/'.$tabla->id_tab_formular_accion_especifica);

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }       
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function habilitar( $id)
    {
      DB::beginTransaction();
      try {

        $tabla = tab_formular_presupuesto::find( $id);
        $tabla->in_activo = true;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
        return Redirect::to('/administracion/formularPresupuesto/lista');

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }
    
    public function accionEspecificaHabilitar( $id)
    {
      DB::beginTransaction();
      try {

        $tabla = tab_formular_accion_especifica::find( $id);
        $tabla->in_activo = true;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
        return Redirect::to('/administracion/formularPresupuesto/accionEspecifica/lista/'.$tabla->id_tab_formular_presupuesto);

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }    
    
    public function partidaHabilitar( $id)
    {
      DB::beginTransaction();
      try {

        $tabla = tab_formular_partida::find( $id);
        $tabla->in_activo = true;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
        return Redirect::to('/administracion/formularPresupuesto/partida/lista/'.$tabla->id_tab_formular_accion_especifica);

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }    
}
