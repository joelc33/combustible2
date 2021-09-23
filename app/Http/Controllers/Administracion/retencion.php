<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Administracion\tab_tipo_retencion;
use gobela\Models\Administracion\tab_retencion;
use gobela\Models\Administracion\tab_cuenta_contable;
use gobela\Models\Configuracion\tab_documento;
use gobela\Models\Administracion\tab_concepto_retencion;
use gobela\Models\Administracion\tab_ramo;
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

class retencion extends Controller
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

        $tab_retencion = tab_retencion::select( 'administracion.tab_retencion.id', 'administracion.tab_retencion.de_retencion', 't01.de_tipo_retencion', 'administracion.tab_retencion.de_cuenta_contable_retencion', 'administracion.tab_retencion.de_cuenta_contable_deposito_tercero', 'administracion.tab_retencion.in_activo', 'administracion.tab_retencion.created_at', 'administracion.tab_retencion.updated_at')
        ->join('administracion.tab_tipo_retencion as t01','t01.id','=','administracion.tab_retencion.id_tab_tipo_retencion')
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.retencion.lista')->with([
          'tab_retencion' => $tab_retencion,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }
    
    public function listaConcepto( Request $request, $id)
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

        $tab_retencion = tab_retencion::orderBy('id','asc')
        ->where('id', '=', $id)
        ->first();  
        
        $tab_concepto_retencion = tab_concepto_retencion::select( 'administracion.tab_concepto_retencion.id', 't01.tx_documento', 't02.de_ramo', 'administracion.tab_concepto_retencion.porcentaje_retencion', 'administracion.tab_concepto_retencion.mo_minimo', 'administracion.tab_concepto_retencion.de_concepto', 'administracion.tab_concepto_retencion.nu_concepto', 'administracion.tab_concepto_retencion.mo_sustraendo')
        ->join('configuracion.tab_documento as t01','t01.id','=','administracion.tab_concepto_retencion.id_tab_documento')
        ->leftjoin('administracion.tab_ramo as t02','t02.id','=','administracion.tab_concepto_retencion.id_tab_ramo')
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.retencion.listaConcepto')->with([
          'tab_retencion' => $tab_retencion,
          'tab_concepto_retencion' => $tab_concepto_retencion,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q,
          'id' => $id            
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
        
        $tab_tipo_retencion = tab_tipo_retencion::select( 'id','de_tipo_retencion', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();          
        
        $tab_cuenta_contable_retencion = tab_cuenta_contable::select( 'administracion.tab_cuenta_contable.id','administracion.tab_cuenta_contable.nu_cuenta_contable', 'administracion.tab_cuenta_contable.de_cuenta_contable', 'administracion.tab_cuenta_contable.nu_nivel', 't01.nu_anexo_contable', 'administracion.tab_cuenta_contable.in_activo', 'administracion.tab_cuenta_contable.created_at', 'administracion.tab_cuenta_contable.updated_at')
        ->leftjoin('administracion.tab_anexo_contable as t01','t01.id','=','administracion.tab_cuenta_contable.id_tab_anexo_contable')
        ->where('administracion.tab_cuenta_contable.in_activo', '=', true)
        ->where('administracion.tab_cuenta_contable.nu_cuenta_contable', 'like', '2010104%')
        ->where('administracion.tab_cuenta_contable.nu_nivel', '=', 6)
        ->orderby('administracion.tab_cuenta_contable.id','ASC')
        ->get();      
        
        $tab_cuenta_contable_tercero = tab_cuenta_contable::select( 'administracion.tab_cuenta_contable.id','administracion.tab_cuenta_contable.nu_cuenta_contable', 'administracion.tab_cuenta_contable.de_cuenta_contable', 'administracion.tab_cuenta_contable.nu_nivel', 't01.nu_anexo_contable', 'administracion.tab_cuenta_contable.in_activo', 'administracion.tab_cuenta_contable.created_at', 'administracion.tab_cuenta_contable.updated_at')
        ->leftjoin('administracion.tab_anexo_contable as t01','t01.id','=','administracion.tab_cuenta_contable.id_tab_anexo_contable')
        ->where('administracion.tab_cuenta_contable.in_activo', '=', true)
        ->where('administracion.tab_cuenta_contable.nu_cuenta_contable', 'like', '2010504%')
        ->where('administracion.tab_cuenta_contable.nu_nivel', '=', 6)
        ->orderby('administracion.tab_cuenta_contable.id','ASC')
        ->get();            

        return View::make('administracion.retencion.nuevo')->with([
            'data'  => $data,
            'tab_tipo_retencion'  => $tab_tipo_retencion,
            'tab_cuenta_contable_retencion'  => $tab_cuenta_contable_retencion,
            'tab_cuenta_contable_tercero'  => $tab_cuenta_contable_tercero
        ]);
    }
    
    public function nuevoConcepto($id)
    {
        $data = array( "id" => null);
        
        $tab_documento = tab_documento::select( 'id','de_inicial', 'tx_documento', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();      
        
        $filtro_ramo = tab_concepto_retencion::select('id_tab_ramo')
        ->where('id_tab_retencion', '=', $id)
        ->orderby('id','ASC')
        ->get();      
        
        $tab_ramo = tab_ramo::select( 'id','de_ramo', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->whereNotIn('id',$filtro_ramo)                
        ->orderby('id','ASC')
        ->get();        
        
        $tab_retencion = tab_retencion::orderBy('id','asc')
        ->where('id', '=', $id)
        ->first();          
                  

        return View::make('administracion.retencion.nuevoConcepto')->with([
            'data'  => $data,
            'tab_retencion' => $tab_retencion,
            'tab_ramo' => $tab_ramo,
            'tab_documento'  => $tab_documento
        ]);
    }    

        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function editar($id)
    {
        
        $tab_tipo_retencion = tab_tipo_retencion::select( 'id','de_tipo_retencion', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();    
        
        $tab_cuenta_contable_retencion = tab_cuenta_contable::select( 'administracion.tab_cuenta_contable.id','administracion.tab_cuenta_contable.nu_cuenta_contable', 'administracion.tab_cuenta_contable.de_cuenta_contable', 'administracion.tab_cuenta_contable.nu_nivel', 't01.nu_anexo_contable', 'administracion.tab_cuenta_contable.in_activo', 'administracion.tab_cuenta_contable.created_at', 'administracion.tab_cuenta_contable.updated_at')
        ->leftjoin('administracion.tab_anexo_contable as t01','t01.id','=','administracion.tab_cuenta_contable.id_tab_anexo_contable')
        ->where('administracion.tab_cuenta_contable.in_activo', '=', true)
        ->where('administracion.tab_cuenta_contable.nu_cuenta_contable', 'like', '2010104%')
        ->where('administracion.tab_cuenta_contable.nu_nivel', '=', 6)
        ->orderby('administracion.tab_cuenta_contable.id','ASC')
        ->get();      
        
        $tab_cuenta_contable_tercero = tab_cuenta_contable::select( 'administracion.tab_cuenta_contable.id','administracion.tab_cuenta_contable.nu_cuenta_contable', 'administracion.tab_cuenta_contable.de_cuenta_contable', 'administracion.tab_cuenta_contable.nu_nivel', 't01.nu_anexo_contable', 'administracion.tab_cuenta_contable.in_activo', 'administracion.tab_cuenta_contable.created_at', 'administracion.tab_cuenta_contable.updated_at')
        ->leftjoin('administracion.tab_anexo_contable as t01','t01.id','=','administracion.tab_cuenta_contable.id_tab_anexo_contable')
        ->where('administracion.tab_cuenta_contable.in_activo', '=', true)
        ->where('administracion.tab_cuenta_contable.nu_cuenta_contable', 'like', '2010504%')
        ->where('administracion.tab_cuenta_contable.nu_nivel', '=', 6)
        ->orderby('administracion.tab_cuenta_contable.id','ASC')
        ->get();         
        
        $data = tab_retencion::select( 'administracion.tab_retencion.id', 'de_retencion', 'id_tab_tipo_retencion', 'id_tab_cuenta_contable_retencion', 'de_cuenta_contable_retencion', 'id_tab_cuenta_contable_deposito_tercero', 'de_cuenta_contable_deposito_tercero', 'nu_concepto_nomina','in_activo', 'created_at', 'updated_at')
        ->where('id', '=', $id)
        ->first();

        return View::make('administracion.retencion.editar')->with([
            'data'  => $data,
            'tab_tipo_retencion'  => $tab_tipo_retencion,
            'tab_cuenta_contable_retencion'  => $tab_cuenta_contable_retencion,
            'tab_cuenta_contable_tercero'  => $tab_cuenta_contable_tercero
        ]);
    }
    
    public function editarConcepto($id)
    {        
        
        $data = tab_concepto_retencion::orderBy('id','asc')
        ->where('id', '=', $id)
        ->first();
        
        $tab_documento = tab_documento::select( 'id','de_inicial', 'tx_documento', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();      
             
        
        $tab_ramo = tab_ramo::select( 'id','de_ramo', 'in_activo', 'created_at', 'updated_at')
        //->where('in_activo', '=', true)              
        ->orderby('id','ASC')
        ->get();        
        
        $tab_retencion = tab_retencion::orderBy('id','asc')
        ->where('id', '=', $data->id_tab_retencion)
        ->first();         

        return View::make('administracion.retencion.editarConcepto')->with([
            'data'  => $data,
            'tab_retencion' => $tab_retencion,
            'tab_ramo' => $tab_ramo,
            'tab_documento'  => $tab_documento
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

          $validador = Validator::make( $request->all(), tab_retencion::$validarEditar);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = tab_retencion::find($id);
            $tabla->de_retencion = $request->descripcion;
            $tabla->id_tab_tipo_retencion = $request->tipo_retencion;
            $tabla->id_tab_cuenta_contable_retencion = $request->id_tab_cuenta_contable_retencion_por_pagar;
            $tabla->de_cuenta_contable_retencion = $request->nu_cuenta_contable_retencion_por_pagar;
            $tabla->id_tab_cuenta_contable_deposito_tercero = $request->id_tab_cuenta_contable_deposito_tercero;
            $tabla->de_cuenta_contable_deposito_tercero = $request->nu_cuenta_contable_deposito_tercero;
            $tabla->nu_concepto_nomina = $request->nu_concepto_nomina;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/administracion/retencion/lista');

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }else{

          $validador = Validator::make( $request->all(), tab_retencion::$validarCrear);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = new tab_retencion;
            $tabla->de_retencion = $request->descripcion;
            $tabla->id_tab_tipo_retencion = $request->tipo_retencion;
            $tabla->id_tab_cuenta_contable_retencion = $request->id_tab_cuenta_contable_retencion_por_pagar;
            $tabla->de_cuenta_contable_retencion = $request->nu_cuenta_contable_retencion_por_pagar;
            $tabla->id_tab_cuenta_contable_deposito_tercero = $request->id_tab_cuenta_contable_deposito_tercero;
            $tabla->de_cuenta_contable_deposito_tercero = $request->nu_cuenta_contable_deposito_tercero;
            $tabla->nu_concepto_nomina = $request->nu_concepto_nomina;
            $tabla->in_activo = true;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            return Redirect::to('/administracion/retencion/lista');

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }
    }
    
    public function guardarConcepto(Request $request, $id = NULL)
    {
        DB::beginTransaction();

        if($id!=''||$id!=null){

          $validador = Validator::make( $request->all(), tab_concepto_retencion::$validarEditar);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = tab_concepto_retencion::find($id);
            $tabla->id_tab_retencion = $request->id_tab_retencion;
            $tabla->id_tab_documento = $request->tipo_documento;
            $tabla->id_tab_ramo = $request->ramos;
            $tabla->porcentaje_retencion = $request->porcentaje_retencion;
            $tabla->mo_minimo = $request->monto_minimo;
            $tabla->de_concepto = $request->concepto;
            $tabla->nu_concepto = $request->numero_concepto;
            $tabla->mo_sustraendo = $request->sustraendo;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/administracion/retencion/concepto/lista/'.$request->id_tab_retencion);

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }else{

          $validador = Validator::make( $request->all(), tab_concepto_retencion::$validarCrear);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = new tab_concepto_retencion;
            $tabla->id_tab_retencion = $request->id_tab_retencion;
            $tabla->id_tab_documento = $request->tipo_documento;
            $tabla->id_tab_ramo = $request->ramos;
            $tabla->porcentaje_retencion = $request->porcentaje_retencion;
            $tabla->mo_minimo = $request->monto_minimo;
            $tabla->de_concepto = $request->concepto;
            $tabla->nu_concepto = $request->numero_concepto;
            $tabla->mo_sustraendo = $request->sustraendo;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            return Redirect::to('/administracion/retencion/concepto/lista/'.$request->id_tab_retencion);

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

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

        $tabla = tab_retencion::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
        return Redirect::to('/administracion/retencion/lista');

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }
    
    public function eliminarConcepto( Request $request,$id)
    {
      DB::beginTransaction();
      try {

        $tabla = tab_concepto_retencion::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
        return Redirect::to('/administracion/retencion/concepto/lista/'.$id);

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

        $tabla = tab_retencion::find( $id);
        $tabla->in_activo = false;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
        return Redirect::to('/administracion/retencion/lista');

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

        $tabla = tab_retencion::find( $id);
        $tabla->in_activo = true;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro publicado con Exito!');
        return Redirect::to('/administracion/retencion/lista');

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }
}
