<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Administracion\tab_transferencia_cuenta;
use gobela\Models\Administracion\tab_banco;
use gobela\Models\Administracion\tab_retencion;
use gobela\Models\Administracion\tab_cuenta_bancaria;
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

class transferenciaCuenta extends Controller
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
    public function agregar( Request $request,$id, $ruta)
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
        
        $tab_banco = tab_banco::select( 'id','de_banco', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();   
               
        $tab_retencion = tab_retencion::select( 'id','de_retencion', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();         
        
        $tab_transferencia_cuenta = tab_transferencia_cuenta::select( 'administracion.tab_transferencia_cuenta.id', 'id_tab_cuenta_bancaria_debito', 't01.id_tab_banco as id_tab_banco_debito', 't01.mo_disponible as saldo_disponible_debito','t02.id_tab_banco as id_tab_banco_credito','t02.mo_disponible as saldo_disponible_credito', 'mo_transferencia',
        DB::raw(" to_char( fe_transferencia, 'dd-mm-YYYY') as fe_transferencia"),'id_tab_cuenta_bancaria_credito', 'id_tab_solicitud', 'id_tab_retencion','tx_observacion')
        ->join('administracion.tab_cuenta_bancaria as t01', 'administracion.tab_transferencia_cuenta.id_tab_cuenta_bancaria_debito', '=', 't01.id')
        ->join('administracion.tab_cuenta_bancaria as t02', 'administracion.tab_transferencia_cuenta.id_tab_cuenta_bancaria_credito', '=', 't02.id')                
        ->where('id_tab_solicitud', $id)
        ->first();        
        
        if( !$tab_transferencia_cuenta){
            
        return View::make('administracion.transferenciaCuenta.agregar')->with([
          'solicitud' => $id,
          'ruta' => $ruta,
          'tab_proceso' => $tab_proceso,
          'tab_banco' => $tab_banco,
          'tab_retencion' => $tab_retencion,
          'tab_tipo_solicitud' => $tab_tipo_solicitud,
          'tab_solicitud' => $tab_solicitud
        ]);
        
        
        }else{
            
        $tab_cuenta_bancaria_debito = tab_cuenta_bancaria::select( 'id','nu_cuenta_bancaria', 'de_cuenta_bancaria', 'mo_disponible', 'in_fondo_tercero', 'in_activo', 'created_at', 'updated_at')
        ->where('id_tab_banco', '=', $tab_transferencia_cuenta->id_tab_banco_debito)
        ->orderby('id','ASC')
        ->get();         
        
        $tab_cuenta_bancaria_credito = tab_cuenta_bancaria::select( 'id','nu_cuenta_bancaria', 'de_cuenta_bancaria', 'mo_disponible', 'in_fondo_tercero', 'in_activo', 'created_at', 'updated_at')
        ->where('id_tab_banco', '=', $tab_transferencia_cuenta->id_tab_banco_credito)
        ->orderby('id','ASC')
        ->get();            
            
        return View::make('administracion.transferenciaCuenta.agregarEditar')->with([
          'solicitud' => $id,
          'ruta' => $ruta,
          'data' => $tab_transferencia_cuenta,
          'tab_proceso' => $tab_proceso,
          'tab_banco' => $tab_banco,
          'tab_retencion' => $tab_retencion,
          'tab_cuenta_bancaria_debito' => $tab_cuenta_bancaria_debito,
          'tab_cuenta_bancaria_credito' => $tab_cuenta_bancaria_credito,
          'tab_tipo_solicitud' => $tab_tipo_solicitud,
          'tab_solicitud' => $tab_solicitud
        ]);            
            
        }
    }
    
    public function aprobacion( Request $request,$id, $ruta)
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
        
        $tab_banco = tab_banco::select( 'id','de_banco', 'in_activo', 'created_at', 'updated_at')
        ->where('in_activo', '=', true)
        ->orderby('id','ASC')
        ->get();   
                
        
        $tab_transferencia_cuenta = tab_transferencia_cuenta::select( 'administracion.tab_transferencia_cuenta.id', 'id_tab_cuenta_bancaria_debito', 't01.id_tab_banco as id_tab_banco_debito', 't01.nu_cuenta_bancaria as nu_cuenta_bancaria_debito', 't01.de_cuenta_bancaria as de_cuenta_bancaria_debito', 't01.mo_disponible as saldo_disponible_debito', 't02.id_tab_banco as id_tab_banco_credito', 't02.nu_cuenta_bancaria as nu_cuenta_bancaria_credito', 't02.de_cuenta_bancaria as de_cuenta_bancaria_credito', 't02.mo_disponible as saldo_disponible_credito', 't03.de_banco as de_banco_debito', 't04.de_banco as de_banco_credito', 'mo_transferencia',
        DB::raw(" to_char( fe_transferencia, 'dd-mm-YYYY') as fe_transferencia"),'id_tab_cuenta_bancaria_credito', 'id_tab_solicitud', 'id_tab_retencion','de_retencion','tx_observacion')
        ->join('administracion.tab_cuenta_bancaria as t01', 'administracion.tab_transferencia_cuenta.id_tab_cuenta_bancaria_debito', '=', 't01.id')
        ->join('administracion.tab_cuenta_bancaria as t02', 'administracion.tab_transferencia_cuenta.id_tab_cuenta_bancaria_credito', '=', 't02.id') 
        ->join('administracion.tab_banco as t03', 't01.id_tab_banco', '=', 't03.id')
        ->join('administracion.tab_banco as t04', 't02.id_tab_banco', '=', 't04.id')
        ->leftJoin('administracion.tab_retencion as t05','t05.id', '=', 'administracion.tab_transferencia_cuenta.id_tab_retencion')                
        ->where('id_tab_solicitud', $id)
        ->first();        
        
            
        $tab_cuenta_bancaria_debito = tab_cuenta_bancaria::select( 'id','nu_cuenta_bancaria', 'de_cuenta_bancaria', 'mo_disponible', 'in_fondo_tercero', 'in_activo', 'created_at', 'updated_at')
        ->where('id_tab_banco', '=', $tab_transferencia_cuenta->id_tab_banco_debito)
        ->orderby('id','ASC')
        ->get();         
        
        $tab_cuenta_bancaria_credito = tab_cuenta_bancaria::select( 'id','nu_cuenta_bancaria', 'de_cuenta_bancaria', 'mo_disponible', 'in_fondo_tercero', 'in_activo', 'created_at', 'updated_at')
        ->where('id_tab_banco', '=', $tab_transferencia_cuenta->id_tab_banco_credito)
        ->orderby('id','ASC')
        ->get();            
            
        return View::make('administracion.transferenciaCuenta.aprobar')->with([
          'solicitud' => $id,
          'ruta' => $ruta,
          'data' => $tab_transferencia_cuenta,
          'tab_proceso' => $tab_proceso,
          'tab_banco' => $tab_banco,
          'tab_cuenta_bancaria_debito' => $tab_cuenta_bancaria_debito,
          'tab_cuenta_bancaria_credito' => $tab_cuenta_bancaria_credito,
          'tab_tipo_solicitud' => $tab_tipo_solicitud,
          'tab_solicitud' => $tab_solicitud
        ]);            
            
        
    }    
    
  public function cuentaBancaria( Request $request)
  {

        $id_tab_banco        = $request->banco;

        $tab_cuenta_bancaria = tab_cuenta_bancaria::select( 'id','nu_cuenta_bancaria','de_cuenta_bancaria','mo_disponible', 'in_fondo_tercero', 'in_activo', 'created_at', 'updated_at')
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

    


    public function guardarAgregar(Request $request, $id = NULL)
    {
        DB::beginTransaction();

        if($id!=''||$id!=null){

          $validador = Validator::make( $request->all(), tab_transferencia_cuenta::$validarEditar);
          if ($validador->fails()) {

              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }
          if($request->in_fondo_tercero==1){
          if($request->id_tab_retencion==0){
          return Redirect::back()->withErrors(array('error'=>'id_tab_retencion'))->withInput( $request->all());    
          }
          }
          try {
              
            $fe_transaccion = Carbon::parse($request->fecha_transferencia)->format('Y-m-d');
            
            $tabla = tab_transferencia_cuenta::find($id);
            $tabla->id_tab_cuenta_bancaria_debito = $request->cuenta_bancaria_debito;
            $tabla->mo_transferencia = $request->monto_transferencia;
            $tabla->fe_transferencia = $fe_transaccion;
            $tabla->id_tab_cuenta_bancaria_credito = $request->cuenta_bancaria_credito;
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->id_tab_retencion = $request->id_tab_retencion;
            $tabla->tx_observacion = $request->tx_observacion;            
            $tabla->save();
            
            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = true;
            $tab_ruta->save();            

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

          $validador = Validator::make( $request->all(), tab_transferencia_cuenta::$validarCrear);
          if ($validador->fails()) {
             
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }
          
          if($request->in_fondo_tercero==1){
          if($request->id_tab_retencion==0){
          return Redirect::back()->withErrors(array('error'=>'id_tab_retencion'))->withInput( $request->all());    
          }
          }          

          try {
              
            $fe_transaccion = Carbon::parse($request->fecha_transferencia)->format('Y-m-d');              

            $tabla = new tab_transferencia_cuenta;
            $tabla->id_tab_cuenta_bancaria_debito = $request->cuenta_bancaria_debito;
            $tabla->mo_transferencia = $request->monto_transferencia;
            $tabla->fe_transferencia = $fe_transaccion;
            $tabla->id_tab_cuenta_bancaria_credito = $request->cuenta_bancaria_credito;
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->id_tab_retencion = $request->id_tab_retencion;
            $tabla->tx_observacion = $request->tx_observacion;
            $tabla->save();
            
            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = true;
            $tab_ruta->save();             

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro creado con Exito!');
            return Redirect::to('/proceso/ruta/lista/'.$request->solicitud);

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
              
            
            $tabla = tab_transferencia_cuenta::find($request->get("id"));
            $tabla->in_aprobado = true;            
            $tabla->save();
            
            $tab_ruta_solicitud = tab_ruta::select( 'id')
            ->where('id_tab_solicitud', '=', $tabla->id_tab_solicitud)
            ->where('in_actual', '=', true)
            ->first();
            
            $tab_ruta = tab_ruta::find( $tab_ruta_solicitud->id);
            $tab_ruta->in_datos = true;
            $tab_ruta->id_tab_estatus = 2;
            $tab_ruta->save();            

            DB::commit();

            Session::flash('msg_side_overlay', 'Proceso Aprobado con Exito!');
            return Redirect::to('/proceso/solicitud/pendiente/');

          }catch (\Illuminate\Database\QueryException $e)
          {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
          }

        
    }    

}
