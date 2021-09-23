<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//

use gobela\Models\Administracion\tab_pago_nomina;
use gobela\Models\Administracion\tab_pago_nomina_detalle;
use gobela\Models\Administracion\tab_archivo_pago_nomina;
use gobela\Models\Administracion\tab_asignar_partida;
use gobela\Models\Administracion\tab_proceso_retencion;
use gobela\Models\Administracion\tab_retencion;
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
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
//*******************************//
use Illuminate\Http\Request;

use gobela\Http\Requests;
use gobela\Http\Controllers\Controller;

class pagoNomina extends Controller
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
    public function cargar( Request $request,$id, $ruta)
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
        
        $detalle_asignacion = tab_pago_nomina_detalle::where('id_tab_solicitud', $id)
        ->where('tx_tipo_movimiento', '=', 'A')
        ->get(); 

        $detalle_deduccion = tab_pago_nomina_detalle::where('id_tab_solicitud', $id)
        ->where('tx_tipo_movimiento', '=', 'D')                
        ->get(); 
                
        $detalle_aporte = tab_pago_nomina_detalle::where('id_tab_solicitud', $id)
        ->where('tx_tipo_movimiento', '=', 'P')                
        ->get();                 
                  
        
        $tab_pago_nomina = tab_pago_nomina::select( 'id', 'id_tab_solicitud','tx_concepto',
        DB::raw(" to_char( fe_pago, 'dd-mm-YYYY') as fe_pago"))         
        ->where('id_tab_solicitud', $id)
        ->first(); 
              
        
        if(!$tab_pago_nomina){
            
        return View::make('administracion.pagoNomina.cargar')->with([
          'solicitud' => $id,
          'ruta' => $ruta,
          'tab_proceso' => $tab_proceso,
          'tab_tipo_solicitud' => $tab_tipo_solicitud,
          'tab_solicitud' => $tab_solicitud
        ]);
        
        
        }else{
                
            
        return View::make('administracion.pagoNomina.cargarEditar')->with([
          'solicitud' => $id,
          'ruta' => $ruta,
          'tab_proceso' => $tab_proceso,
          'data' => $tab_pago_nomina,
          'tab_tipo_solicitud' => $tab_tipo_solicitud,
          'tab_solicitud' => $tab_solicitud,
          'detalle_asignacion' => $detalle_asignacion,
          'detalle_deduccion' => $detalle_deduccion,
          'detalle_aporte' => $detalle_aporte
        ]);            
            
        }
    }       
     

    public function editar( Request $request,$id, $ruta)
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
        
        $monto_asignacion = tab_pago_nomina_detalle::select(DB::raw("coalesce(sum(mo_pago),0.00) as monto_cargado"))
        ->where('tx_tipo_movimiento', '=', 'A')
        ->where('id_tab_solicitud', '=', $id)
        ->first(); 

        $monto_deduccion = tab_pago_nomina_detalle::select(DB::raw("coalesce(sum(mo_pago),0.00) as monto_cargado"))
        ->where('tx_tipo_movimiento', '=', 'D')
        ->where('id_tab_solicitud', '=', $id)                
        ->first(); 
        
        $monto = tab_archivo_pago_nomina::select(DB::raw("coalesce(sum(mo_pago),0.00) as monto"))
        ->where('id_tab_solicitud', $id)                
        ->first();         

        $tab_pago_nomina = tab_pago_nomina::select( 'id', 'id_tab_solicitud','tx_concepto',
        DB::raw(" to_char( fe_pago, 'dd-mm-YYYY') as fe_pago"))         
        ->where('id_tab_solicitud', $id)
        ->first(); 
              
        $monto_cargado = $monto_asignacion->monto_cargado - $monto_deduccion->monto_cargado;

        $diferencia = $monto_cargado - $monto->monto;      
        
        $tab_archivo_pago_nomina = tab_archivo_pago_nomina::select(DB::raw("count(nu_codigo_banco) as cantidad "),'t01.de_banco',DB::raw("coalesce(sum(mo_pago),0.00) as mo_pago"))
        ->join('administracion.tab_banco as t01','t01.nu_codigo','=','administracion.tab_archivo_pago_nomina.nu_codigo_banco')                
        ->where('id_tab_solicitud', $id)
        ->groupBy('nu_codigo_banco','t01.de_banco')
        ->get();         
            
        return View::make('administracion.pagoNomina.editar')->with([
          'solicitud' => $id,
          'ruta' => $ruta,
          'tab_proceso' => $tab_proceso,
          'data' => $tab_pago_nomina,
          'tab_tipo_solicitud' => $tab_tipo_solicitud,
          'tab_solicitud' => $tab_solicitud,
          'monto_cargado' => $monto_cargado,
          'monto' => $monto->monto,
          'diferencia' => $diferencia,
          'tab_archivo_pago_nomina' => $tab_archivo_pago_nomina            
        ]);            
            
        
    }


    public function guardar(Request $request, $id = NULL)
    {
        DB::beginTransaction();

        if($id!=''||$id!=null){

          $validador = Validator::make( $request->all(), tab_pago_nomina::$validarEditar);
          if ($validador->fails()) {

              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }
          
        $file = $request->archivo;
        if($file){
        $validador_archivo = Validator::make(
                ['archivo'      => $file, 'extension' => strtolower($file->getClientOriginalExtension()),],
                ['archivo'=> 'required', 'extension' => 'required|in:csv,xls,xlsx', ]
        );     
        
          if ($validador_archivo->fails()) {
             
              return Redirect::back()->withErrors( $validador_archivo)->withInput( $request->all());
          }          
          
        }
          
          try {
              
            $fe_pago = Carbon::parse($request->fecha_pago)->format('Y-m-d');              
              
            $tabla = tab_pago_nomina::find($id);
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->id_tab_usuario = Auth::user()->id;
            $tabla->tx_concepto = $request->tx_concepto;
            $tabla->fe_pago = $fe_pago;         
            $tabla->save();  
            
            if($file){
            //*************Inicio de Carga Masiva*************//
            $path = $file->getRealPath();
            $data = Excel::load($path, function($reader) { })->get();

            if(!empty($data)){
            foreach ($data as $key => $value) {

            $insert[] = [
                    'id_tab_solicitud' => $request->solicitud,
                    'id_tab_usuario' => Auth::user()->id,
                    'tx_tipo_movimiento' => $value->tipo_movimiento,
                    'nu_concepto_nomina' => $value->concepto_nomina,
                    'tx_descripcion' => $value->descripcion,
                    'mo_pago' => $value->monto_pago,
                    'nu_correlativo' => $value->correlativo,
                    'tx_partida' => $value->numero_partida,
                    'nu_codigo_banco' => $value->codigo_banco
                     ];

            }
            if(!empty($insert)){

            $tab_pago_nomina_detalle = tab_pago_nomina_detalle::where('id_tab_solicitud', '=', $request->solicitud)
            ->delete(); 
            
                    $i=1;

            foreach ($insert as $key => $valor) {
                    $i++;
               /**** validar insert***/

                    $validarInsert = Validator::make( $request->all(), tab_pago_nomina_detalle::$validarCrear);
                    if ($validarInsert->fails()){

                    }else{

            $tabla_detalle = new tab_pago_nomina_detalle;
            $tabla_detalle->id_tab_solicitud = $valor['id_tab_solicitud'];
            $tabla_detalle->id_tab_usuario = $valor['id_tab_usuario'];
            $tabla_detalle->tx_tipo_movimiento = $valor['tx_tipo_movimiento'];
            $tabla_detalle->nu_concepto_nomina = $valor['nu_concepto_nomina'];         
            $tabla_detalle->tx_descripcion = $valor['tx_descripcion'];
            $tabla_detalle->mo_pago = $valor['mo_pago'];
            $tabla_detalle->nu_correlativo = $valor['nu_correlativo'];
            $tabla_detalle->tx_partida =$valor['tx_partida']; 
            $tabla_detalle->nu_codigo_banco = $valor['nu_codigo_banco'];
            $tabla_detalle->save();

                                    }
                            }            
                    }
            }
            
            }
            
            $tab_pago_nomina_detalle = tab_pago_nomina_detalle::select(DB::raw("coalesce(sum(mo_pago),0.00) as mo_pago"),'tx_tipo_movimiento','nu_concepto_nomina','tx_descripcion','tx_partida')             
            ->where('id_tab_solicitud', $request->solicitud)
            ->where('tx_tipo_movimiento', '=', 'A')
            ->groupBy('tx_tipo_movimiento','nu_concepto_nomina','tx_descripcion','tx_partida')
            ->orderBy('tx_tipo_movimiento')
            ->get();   
            
            $tab_asignar_partida = tab_asignar_partida::where('id_tab_solicitud', '=', $request->solicitud)
            ->delete();             

            foreach ($tab_pago_nomina_detalle as $key => $value) {

            $tabla_asignar_partida = new tab_asignar_partida;
            $tabla_asignar_partida->id_tab_solicitud = $request->solicitud;
            $tabla_asignar_partida->mo_presupuesto = $value->mo_pago;
            $tabla_asignar_partida->in_activo = true;
            $tabla_asignar_partida->de_concepto = $value->tx_descripcion;         
            $tabla_asignar_partida->save();                
                
            }   
            
            $tab_pago_nomina_retencion = tab_pago_nomina_detalle::select(DB::raw("coalesce(sum(mo_pago),0.00) as mo_pago"),'tx_tipo_movimiento','nu_concepto_nomina')             
            ->where('id_tab_solicitud', $request->solicitud)
            ->where('tx_tipo_movimiento', '=', 'D')
            ->groupBy('tx_tipo_movimiento','nu_concepto_nomina')
            ->orderBy('nu_concepto_nomina','asc')
            ->get();    
            
            $tab_proceso_retencion = tab_proceso_retencion::where('id_tab_solicitud', '=', $request->solicitud)
            ->delete();
            
            foreach ($tab_pago_nomina_retencion as $key => $value) {
                
            $tab_retencion = tab_retencion::where('nu_concepto_nomina', $value->nu_concepto_nomina)->first();  
            
            if(!$tab_retencion){
            Session::flash('de_alert_form', 'El concepto nomina deducción '.$value->nu_concepto_nomina.' no tiene rentencion asociada! Verifique'); 
            return Redirect::back()->withInput( $request->all());                 
            }

            $tab_proceso_retencion = new tab_proceso_retencion;
            $tab_proceso_retencion->id_tab_solicitud = $request->solicitud;
            $tab_proceso_retencion->id_tab_retencion = $tab_retencion->id;
            $tab_proceso_retencion->fe_retencion = $fe_pago;
            $tab_proceso_retencion->mo_retencion = $value->mo_pago;  
            $tab_proceso_retencion->in_activo = true;
            $tab_proceso_retencion->save();                
                
            }            
            
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

          $validador = Validator::make( $request->all(), tab_pago_nomina::$validarCrear);
          if ($validador->fails()) {
             
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }
          
        $file = $request->archivo;

        $validador_archivo = Validator::make(
                ['archivo'      => $file, 'extension' => strtolower($file->getClientOriginalExtension()),],
                ['archivo'=> 'required', 'extension' => 'required|in:csv,xls,xlsx', ]
        );     
        
          if ($validador_archivo->fails()) {
             
              return Redirect::back()->withErrors( $validador_archivo)->withInput( $request->all());
          }        
        

          try {
              
            $fe_pago = Carbon::parse($request->fecha_pago)->format('Y-m-d');              
              
            $tabla = new tab_pago_nomina;
            $tabla->id_tab_solicitud = $request->solicitud;
            $tabla->id_tab_usuario = Auth::user()->id;
            $tabla->tx_concepto = $request->tx_concepto;
            $tabla->fe_pago = $fe_pago;         
            $tabla->save();                  
              
            //*************Inicio de Carga Masiva*************//
            $path = $file->getRealPath();
            $data = Excel::load($path, function($reader) { })->get();

            if(!empty($data) && $data->count()){
            foreach ($data as $key => $value) {

            $insert[] = [
                    'id_tab_solicitud' => $request->solicitud,
                    'id_tab_usuario' => Auth::user()->id,
                    'tx_tipo_movimiento' => $value->tipo_movimiento,
                    'nu_concepto_nomina' => $value->concepto_nomina,
                    'tx_descripcion' => $value->descripcion,
                    'mo_pago' => $value->monto_pago,
                    'nu_correlativo' => $value->correlativo,
                    'tx_partida' => $value->numero_partida,
                    'nu_codigo_banco' => $value->codigo_banco
                     ];
            }
            if(!empty($insert)){

               
                    $i=1;

            foreach ($insert as $key => $valor) {
                    $i++;
               /**** validar insert***/

                    $validarInsert = Validator::make( $request->all(), tab_pago_nomina_detalle::$validarCrear);
                    if ($validarInsert->fails()){

                    }else{
                        
                        

            $tabla_detalle = new tab_pago_nomina_detalle;
            $tabla_detalle->id_tab_solicitud = $valor['id_tab_solicitud'];
            $tabla_detalle->id_tab_usuario = $valor['id_tab_usuario'];
            $tabla_detalle->tx_tipo_movimiento = $valor['tx_tipo_movimiento'];
            $tabla_detalle->nu_concepto_nomina = $valor['nu_concepto_nomina'];         
            $tabla_detalle->tx_descripcion = $valor['tx_descripcion'];
            $tabla_detalle->mo_pago = $valor['mo_pago'];
            $tabla_detalle->nu_correlativo = $valor['nu_correlativo'];
            $tabla_detalle->tx_partida =$valor['tx_partida']; 
            $tabla_detalle->nu_codigo_banco = $valor['nu_codigo_banco'];
            $tabla_detalle->save();

                                    }
                            }            
                    }
            }

            $tab_pago_nomina_detalle = tab_pago_nomina_detalle::select(DB::raw("coalesce(sum(mo_pago),0.00) as mo_pago"),'tx_tipo_movimiento','nu_concepto_nomina','tx_descripcion','tx_partida')             
            ->where('id_tab_solicitud', $request->solicitud)
            ->where('tx_tipo_movimiento', '=', 'A')
            ->groupBy('tx_tipo_movimiento','nu_concepto_nomina','tx_descripcion','tx_partida')
            ->orderBy('tx_tipo_movimiento')
            ->get();   
                       

            foreach ($tab_pago_nomina_detalle as $key => $value) {

            $tabla_asignar_partida = new tab_asignar_partida;
            $tabla_asignar_partida->id_tab_solicitud = $request->solicitud;
            $tabla_asignar_partida->mo_presupuesto = $value->mo_pago;
            $tabla_asignar_partida->in_activo = true;
            $tabla_asignar_partida->de_concepto = $value->tx_descripcion;         
            $tabla_asignar_partida->save();                
                
            }
            
            $tab_pago_nomina_retencion = tab_pago_nomina_detalle::select(DB::raw("coalesce(sum(mo_pago),0.00) as mo_pago"),'tx_tipo_movimiento','nu_concepto_nomina')             
            ->where('id_tab_solicitud', $request->solicitud)
            ->where('tx_tipo_movimiento', '=', 'D')
            ->groupBy('tx_tipo_movimiento','nu_concepto_nomina')
            ->orderBy('nu_concepto_nomina','asc')
            ->get();    
            
            $tab_proceso_retencion = tab_proceso_retencion::where('id_tab_solicitud', '=', $request->solicitud)
            ->delete();
            
            foreach ($tab_pago_nomina_retencion as $key => $value) {
                
            $tab_retencion = tab_retencion::where('nu_concepto_nomina', $value->nu_concepto_nomina)->first();  
            
            if(!$tab_retencion){
            Session::flash('de_alert_form', 'El concepto nomina deducción '.$value->nu_concepto_nomina.' no tiene rentencion asociada! Verifique'); 
            return Redirect::back()->withInput( $request->all());                 
            }

            $tab_proceso_retencion = new tab_proceso_retencion;
            $tab_proceso_retencion->id_tab_solicitud = $request->solicitud;
            $tab_proceso_retencion->id_tab_retencion = $tab_retencion->id;
            $tab_proceso_retencion->fe_retencion = $fe_pago;
            $tab_proceso_retencion->mo_retencion = $value->mo_pago;   
            $tab_proceso_retencion->in_activo = true;
            $tab_proceso_retencion->save();                
                
            }            
            
            
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
    
    
    public function guardarEditar(Request $request, $id = NULL)
    {
        DB::beginTransaction();


          $validador = Validator::make( $request->all(), tab_pago_nomina::$validarCrear);
          if ($validador->fails()) {
             
              return Redirect::back()->withErrors( $validador)->withInput( $request->all());
          }
          
        $file = $request->archivo;

        $validador_archivo = Validator::make(
                ['archivo'      => $file, 'extension' => strtolower($file->getClientOriginalExtension()),],
                ['archivo'=> 'required', 'extension' => 'required|in:txt', ]
        );     
        
          if ($validador_archivo->fails()) {
             
              return Redirect::back()->withErrors( $validador_archivo)->withInput( $request->all());
          }        
        

          try {
              
                
              
            //*************Inicio de Carga Masiva*************//
            $mo_total = 0;
            $data = File::get($file);

            if(!empty($data)){
            foreach (File($file) as $key=>$line){

            $cadena = str_replace("|",'', $line);
            $array = explode(',', $cadena);
  

            $insert[] = [
                    'id_tab_solicitud' => $request->solicitud,
                    'id_tab_usuario' => Auth::user()->id,
                    'id_tab_forma_pago' => 1,
                    'nu_cedula' => trim(utf8_decode($array[0])),
                    'tx_cedula' => trim(utf8_decode($array[1])),
                    'nb_persona' => utf8_decode($array[2]),
                    'nu_cuenta_bancaria' => trim(utf8_decode($array[3])),
                    'mo_pago' => trim(utf8_decode($array[4])),
                    'nu_codigo_banco' => trim($array[5])
                     ];
            
            $mo_total+=trim($array[4]);

            }
            
           
            
            if(!empty($insert)){

            $tab_archivo_pago_nomina = tab_archivo_pago_nomina::where('id_tab_solicitud', '=', $request->solicitud)
            ->delete(); 
            
                    $i=1;

            foreach ($insert as $key => $valor) {

               /**** validar insert***/
                    $mensajes = array(
                        'nu_cedula.numeric'=>'El numero de cedula '.$valor['nu_cedula'].' debe ser numérico linea '.$i,
                        'nu_cedula.required'=>'El numero de cedula es obligatorio linea '.$i,
                        'tx_cedula.max'=>'El numero de cedula '.$valor['tx_cedula'].' debe tener menos de 9 digitos linea '.$i,
                        'tx_cedula.required'=>'El numero de cedula es obligatorio linea '.$i,
                        'nu_cuenta_bancaria.numeric'=>'El numero de cuenta bancaria '.$valor['nu_cuenta_bancaria'].' debe ser numérico linea '.$i,
                        'nu_cuenta_bancaria.min'=>'El numero de cuenta bancaria '.$valor['nu_cuenta_bancaria'].' debe tener al menos 10 digitos linea '.$i,
                        'nu_cuenta_bancaria.required'=>'El numero de cuenta bancaria es obligatorio linea '.$i,
                    );
                    $validarInsert = Validator::make( $valor, tab_archivo_pago_nomina::$validarCrear,$mensajes);
                    if ($validarInsert->fails()){
                    Session::flash('msg_alerta', 'Error!');
                    return Redirect::back()->withErrors( $validarInsert)->withInput( $request->all());
                    }else{
                        
                        

            $tab_archivo_pago_nomina = new tab_archivo_pago_nomina;
            $tab_archivo_pago_nomina->id_tab_solicitud = $valor['id_tab_solicitud'];
            $tab_archivo_pago_nomina->id_tab_usuario = $valor['id_tab_usuario'];
            $tab_archivo_pago_nomina->id_tab_forma_pago = $valor['id_tab_forma_pago'];
            $tab_archivo_pago_nomina->nu_cedula = $valor['nu_cedula'];         
            $tab_archivo_pago_nomina->tx_cedula = $valor['tx_cedula'];
            $tab_archivo_pago_nomina->nb_persona = $valor['nb_persona'];
            $tab_archivo_pago_nomina->nu_cuenta_bancaria = $valor['nu_cuenta_bancaria'];
            $tab_archivo_pago_nomina->mo_pago =$valor['mo_pago']; 
            $tab_archivo_pago_nomina->nu_codigo_banco = $valor['nu_codigo_banco'];
            $tab_archivo_pago_nomina->save();

                                    }
                    $i++;                                    
                                    
                            }            
                    }
            }      
            
            $tab_pago_nomina = tab_pago_nomina::find( $request->id_tab_pago_nomina);
            $tab_pago_nomina->mo_pago = $mo_total;
            $tab_pago_nomina->save();

            if($request->diferencia==0){
            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = true;
            $tab_ruta->save(); 
            }else{
                
                
            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = false;
            $tab_ruta->save();                 
            }

            DB::commit();
            if($request->diferencia!=0){               
            Session::flash('de_alert_form', 'El monto del txt no coincide con el cargado! Verifique');   
            }

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
