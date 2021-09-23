<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Administracion\tab_cuenta_contable_documento;
use gobela\Models\Administracion\tab_cuenta_contable;
use gobela\Models\Configuracion\tab_solicitud;
use gobela\Models\Configuracion\tab_ruta;
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

class cuentaContableDocumentoController extends Controller
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
        $perPage = 10;
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

        $tab_cuenta_contable_documento = tab_cuenta_contable_documento::select( 'administracion.tab_cuenta_contable_documento.id', 'de_cc_documento', 
        'id_cc_gasto_pago', 'id_cc_odp', 'de_sigla', 'nu_cuenta_gasto', 'nu_cuenta_odp', 'de_proceso', 'de_solicitud', 't03.co_cuenta_contable as cuenta_gasto',
        't04.co_cuenta_contable as cuenta_odp')
        ->join('configuracion.tab_proceso as t01','t01.id','=','administracion.tab_cuenta_contable_documento.id_tab_proceso')   
        ->join('configuracion.tab_solicitud as t02','t02.id','=','administracion.tab_cuenta_contable_documento.id_tab_solicitud')        
        ->join('administracion.tab_cuenta_contable as t03','t03.id','=','administracion.tab_cuenta_contable_documento.id_cc_gasto_pago')
        ->join('administracion.tab_cuenta_contable as t04','t04.id','=','administracion.tab_cuenta_contable_documento.id_cc_odp')   
        //->where('in_activo', '=', true)
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.cuentaContableDocumento.lista')->with([
          'tab_cuenta_contable_documento' => $tab_cuenta_contable_documento,
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

        $tab_solicitud = tab_solicitud::select( 'id', 'id_tab_proceso', 'de_solicitud', 'in_ver', 'in_activo', 'created_at', 
        'updated_at', 'nu_identificador')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();

        $tab_cuenta_contable = tab_cuenta_contable::select( 'id', 'co_cuenta_contable', 'nu_cuenta_contable', 'de_cuenta_contable', 
        'nu_nivel', 'acu_deb', 'acu_cre', 'mes_deb', 'mes_cre', 'pre_deb', 'pre_cre', 
        'id_tab_anexo_contable', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();     

        return View::make('administracion.cuentaContableDocumento.nuevo')->with([
            'data'  => $data,
            'tab_solicitud'  => $tab_solicitud,
            'tab_cuenta_contable'  => $tab_cuenta_contable            
        ]);
    }  
        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function editar($id)
    {       
        
        $data = tab_cuenta_contable_documento::select( 'id', 'de_cc_documento', 'id_cc_gasto_pago', 'id_cc_odp', 'id_tab_proceso', 
        'id_tab_solicitud', 'de_sigla', 'nu_cuenta_gasto', 'nu_cuenta_odp', 'in_activo', 
        'created_at', 'updated_at')
        ->where('id', '=', $id)
        ->first();
        
        $tab_solicitud = tab_solicitud::select( 'id', 'id_tab_proceso', 'de_solicitud', 'in_ver', 'in_activo', 'created_at', 
        'updated_at', 'nu_identificador')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();

        $tab_cuenta_contable = tab_cuenta_contable::select( 'id', 'co_cuenta_contable', 'nu_cuenta_contable', 'de_cuenta_contable', 
        'nu_nivel', 'acu_deb', 'acu_cre', 'mes_deb', 'mes_cre', 'pre_deb', 'pre_cre', 
        'id_tab_anexo_contable', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();  

        return View::make('administracion.cuentaContableDocumento.editar')->with([
            'data'  => $data,
            'tab_solicitud'  => $tab_solicitud,
            'tab_cuenta_contable'  => $tab_cuenta_contable
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

          $validador = Validator::make( $request->all(), tab_cuenta_contable_documento::$validarEditar);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = tab_cuenta_contable_documento::find($id);
            $tabla->de_cc_documento = $request->descripcion;
            $tabla->id_cc_gasto_pago = $request->cuenta_gasto;         
            $tabla->id_cc_odp = $request->cuenta_orden_pago;
            $tabla->id_tab_proceso = $request->ruta;
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->de_sigla = $request->siglas;
            //$tabla->nu_cuenta_gasto = $request->nu_se;
            //$tabla->nu_cuenta_odp = $request->nu_sse;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
            return Redirect::to('/administracion/cuentaContableDocumento/lista');

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        }else{

          $validador = Validator::make( $request->all(), tab_cuenta_contable_documento::$validarCrear);
          if ($validador->fails()) {
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {

            $tabla = new tab_cuenta_contable_documento;
            $tabla->de_cc_documento = $request->descripcion;
            $tabla->id_cc_gasto_pago = $request->cuenta_gasto;         
            $tabla->id_cc_odp = $request->cuenta_orden_pago;
            $tabla->id_tab_proceso = $request->ruta;
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->de_sigla = $request->siglas;
            //$tabla->nu_cuenta_gasto = $request->nu_se;
            //$tabla->nu_cuenta_odp = $request->nu_sse;
            $tabla->in_activo = true;
            $tabla->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            return Redirect::to('/administracion/cuentaContableDocumento/lista');

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

        $tabla = tab_catalogo_partida::find( $request->get("id"));
        $tabla->delete();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
        return Redirect::to('/administracion/catalogoPartida/lista');

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

        $tabla = tab_catalogo_partida::find( $id);
        $tabla->in_activo = false;
        $tabla->save();

        DB::commit();

        Session::flash('msg_side_overlay', 'Registro despublicado con Exito!');
        return Redirect::to('/administracion/catalogoPartida/lista');

      }catch (\Illuminate\Database\QueryException $e)
      {
        DB::rollback();
        return Redirect::back()->withErrors([
            'da_alert_form' => $e->getMessage()
        ])->withInput( $request->all());
      }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function ruta(Request $request)
    {

      $response['success']  = 'true';

      $response['data'] = tab_ruta::select( 'id_tab_proceso as id', 'de_proceso', 'id_tab_solicitud', 'nu_orden', 'in_datos', 'nb_controlador', 
      'nb_accion', 'nb_reporte')
      ->join('configuracion.tab_proceso as t01','t01.id','=','configuracion.tab_ruta.id_tab_proceso')
      ->where('id_tab_solicitud', $request->get("solicitud"))
      ->orderby('nu_orden','ASC')
      ->get()->toArray();

      return Response::json($response, 200);

    }

}
