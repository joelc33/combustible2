<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Administracion\tab_movimiento_financiero;
use gobela\Models\Administracion\tab_banco;
use gobela\Models\Administracion\tab_cuenta_bancaria;
use gobela\Models\Administracion\tab_tipo_movimiento_financiero;
use gobela\Models\Administracion\tab_tipo_documento_financiero;
use gobela\Models\Administracion\tab_subtipo_documento_financiero;
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

class movimientoFinanciero extends Controller
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
        
        $tab_banco = tab_banco::select( 'id','de_banco', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();           
        
        $tab_tipo_movimiento_financiero = tab_tipo_movimiento_financiero::select( 'id','nu_tipo_movimiento','de_tipo_movimiento', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();   
        
        $tab_tipo_documento_financiero = tab_tipo_documento_financiero::select( 'id','nu_tipo_documento_financiero','de_tipo_documento_financiero', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();         
        
        $tab_movimiento_financiero = tab_movimiento_financiero::select( 'administracion.tab_movimiento_financiero.id', 'nu_documento', 'de_movimiento', 'nu_transaccion',DB::raw("to_char(fe_transaccion, 'dd/mm/YYYY') as fe_transaccion"), 'mo_transaccion', 't01.de_banco', 't02.nu_cuenta_bancaria', 't02.de_cuenta_bancaria', 'administracion.tab_movimiento_financiero.in_activo', 'administracion.tab_movimiento_financiero.created_at', 'administracion.tab_movimiento_financiero.updated_at')
        ->join('administracion.tab_banco as t01', 'administracion.tab_movimiento_financiero.id_tab_banco', '=', 't01.id')
        ->join('administracion.tab_cuenta_bancaria as t02', 'administracion.tab_movimiento_financiero.id_tab_cuenta_bancaria', '=', 't02.id')                
        ->where('administracion.tab_movimiento_financiero.in_activo', '=', true)
        ->where('administracion.tab_movimiento_financiero.id_tab_solicitud', '=', $id)
        ->search($q, $sortBy)
        ->orderBy($sortBy, $orderBy)
        ->paginate($perPage);

        return View::make('administracion.movimientoFinanciero.lista')->with([
          'solicitud' => $id,
          'ruta' => $ruta,
          'tab_proceso' => $tab_proceso,
          'tab_banco' => $tab_banco,
          'tab_tipo_solicitud' => $tab_tipo_solicitud,
          'tab_tipo_movimiento_financiero' => $tab_tipo_movimiento_financiero,
          'tab_tipo_documento_financiero' => $tab_tipo_documento_financiero,
          'tab_movimiento_financiero' => $tab_movimiento_financiero,
          'tab_solicitud' => $tab_solicitud,
          'orderBy' => $orderBy,
          'sortBy' => $sortBy,
          'perPage' => $perPage,
          'columnas' => $columnas,
          'q' => $q
        ]);
    }
    
  public function cuentaBancaria( Request $request)
  {

        $id_tab_banco        = $request->banco;

        $tab_cuenta_bancaria = tab_cuenta_bancaria::select( 'id','nu_cuenta_bancaria','de_cuenta_bancaria', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->where('id_tab_banco', '=', $id_tab_banco)
        ->orderby('id','ASC')
        ->get(); 

    return Response::json(array(
			'success' => true,
			'valido' => true,
			'data' => $tab_cuenta_bancaria
		)); 

  }       

  
  public function subtipoDocumento( Request $request)
  {

        $id_tab_tipo_documento_financiero        = $request->tipo_documento;

        $tab_subtipo_documento_financiero = tab_subtipo_documento_financiero::select( 'id','nu_subtipo_documento_financiero','de_subtipo_documento_financiero', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->where('id_tab_tipo_documento_financiero', '=', $id_tab_tipo_documento_financiero)
        ->orderby('id','ASC')
        ->get(); 

    return Response::json(array(
			'success' => true,
			'valido' => true,
			'data' => $tab_subtipo_documento_financiero
		)); 

  }   


    public function guardar(Request $request, $id = NULL)
    {
        DB::beginTransaction();

        if($id!=''||$id!=null){

          $validador = Validator::make( $request->all(), tab_movimiento_financiero::$validarEditar);
          if ($validador->fails()) {
              Session::flash('msg_alerta', 'Error!');
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {
              
            $fe_transaccion = Carbon::parse($request->fe_transaccion)->format('Y-m-d');
            
            $tabla = tab_movimiento_financiero::find($id);
            $tabla->de_movimiento = $request->descripcion;
            $tabla->nu_transaccion = $request->numero_transaccion;
            $tabla->fe_transaccion = $fe_transaccion;
            $tabla->mo_transaccion = $request->monto;
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->id_tab_banco = $request->banco;     
            $tabla->id_tab_cuenta_bancaria = $request->cuenta_bancaria;
            $tabla->id_tab_tipo_movimiento_financiero = $request->tipo_movimiento;
            $tabla->id_tab_tipo_documento_financiero = $request->tipo_documento;
            $tabla->id_tab_subtipo_documento_financiero = $request->subtipo_documento;
            $tabla->in_conciliado = false;        
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

          $validador = Validator::make( $request->all(), tab_movimiento_financiero::$validarCrear);
          if ($validador->fails()) {
            Session::flash('msg_alerta', 'Error!');              
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }

          try {
              
            $fe_transaccion = Carbon::parse($request->fe_transaccion)->format('Y-m-d');              

            $tabla = new tab_movimiento_financiero;
            $tabla->de_movimiento = $request->descripcion;
            $tabla->nu_transaccion = $request->numero_transaccion;
            $tabla->fe_transaccion = $fe_transaccion;
            $tabla->mo_transaccion = $request->monto;
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->id_tab_banco = $request->banco;     
            $tabla->id_tab_cuenta_bancaria = $request->cuenta_bancaria;
            $tabla->id_tab_tipo_movimiento_financiero = $request->tipo_movimiento;
            $tabla->id_tab_tipo_documento_financiero = $request->tipo_documento;
            $tabla->id_tab_subtipo_documento_financiero = $request->subtipo_documento;
            $tabla->in_conciliado = false; 
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


}
