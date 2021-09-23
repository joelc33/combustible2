<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Proceso\tab_ruta;
use gobela\Models\Administracion\tab_compra;
use gobela\Models\Administracion\tab_ramo_proveedor;
use gobela\Models\Administracion\tab_compra_detalle;
use gobela\Models\Administracion\tab_retencion_proveedor;
use gobela\Models\Administracion\tab_proceso_retencion;
use gobela\Models\Administracion\tab_proceso_retencion_factura;
use gobela\Models\Administracion\tab_proceso_retencion_detalle;
use gobela\Models\Administracion\tab_cuenta_contable_documento;
use gobela\Models\Administracion\tab_asiento_contable;
use View;
use Validator;
use Response;
use DB;
use Session;
use Auth;
use Redirect;
use HelperReporte;
//*******************************//
use Illuminate\Http\Request;

use gobela\Http\Requests;
use gobela\Http\Controllers\Controller;

class compraContabilidad extends Controller
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
    public function editar( $request, $id, $ruta)
    {

        $tab_compra = tab_compra::select( 'administracion.tab_compra.id', 'id_tab_solicitud', 'id_tab_proveedor', 'id_tab_tipo_contrato', 'nu_orden_pre_impresa', 
        DB::raw(" to_char( fe_ini, 'dd-mm-YYYY') as fe_ini"), DB::raw(" to_char( fe_fin, 'dd-mm-YYYY') as fe_fin"), DB::raw(" to_char( fe_entrega, 'dd-mm-YYYY') as fe_entrega"), 'in_compromiso_rs', 'mo_contrato', 'de_garantia', 'de_observacion', DB::raw(" to_char( fe_compra, 'dd-mm-YYYY') as fe_compra"), 'id_tab_iva_factura', 
        'id_tab_ejecutor_entrega', 'administracion.tab_compra.in_activo', 'administracion.tab_compra.created_at', 'administracion.tab_compra.updated_at',
        'id_tab_documento', 'nu_documento', 'de_proveedor', 'tx_direccion', 'de_inicial', 'nu_iva_factura', 'nu_ejecutor', 'de_ejecutor', 
        'de_tipo_contrato', 't06.nu_iva_retencion')
        ->join('administracion.tab_proveedor as t01', 'administracion.tab_compra.id_tab_proveedor', '=', 't01.id')
        ->join('configuracion.tab_documento as t02', 't01.id_tab_documento', '=', 't02.id')
        ->join('administracion.tab_iva_factura as t03', 'administracion.tab_compra.id_tab_iva_factura', '=', 't03.id')
        ->join('administracion.tab_ejecutor as t04', 'administracion.tab_compra.id_tab_ejecutor_entrega', '=', 't04.id')
        ->join('administracion.tab_tipo_contrato as t05', 'administracion.tab_compra.id_tab_tipo_contrato', '=', 't05.id')
        ->leftJoin('administracion.tab_iva_retencion as t06', 't01.id_tab_iva_retencion', '=', 't06.id')
        ->where('id_tab_solicitud', $id)
        ->first();

        $tab_ramo_proveedor = tab_ramo_proveedor::select( 't01.id', 'de_ramo')
        ->join('administracion.tab_ramo as t01', 'administracion.tab_ramo_proveedor.id_tab_ramo', '=', 't01.id')
        ->where('id_tab_proveedor', $tab_compra->id_tab_proveedor)
        ->orderBy('id','asc')
        ->get();

        $filtro_retencion = tab_proceso_retencion_factura::select('id_tab_compra_detalle')
        ->join('administracion.tab_proceso_retencion as t01', 'administracion.tab_proceso_retencion_factura.id_tab_proceso_retencion', '=', 't01.id')
        ->where('id_tab_solicitud', '=', $id) 
        ->orderby('administracion.tab_proceso_retencion_factura.id','ASC')
        ->get();  

        $tab_compra_detalle = tab_compra_detalle::select( 'administracion.tab_compra_detalle.id', 'nu_cantidad', 'de_especificacion', 'nu_producto',
        'de_producto', 'de_unidad_medida', 'mo_precio_unitario', 'mo_precio_total')
        ->join('administracion.tab_compra as t01', 'administracion.tab_compra_detalle.id_tab_compra', '=', 't01.id')
        ->join('administracion.tab_producto as t02', 'administracion.tab_compra_detalle.id_tab_producto', '=', 't02.id')
        ->join('administracion.tab_unidad_medida as t03', 'administracion.tab_compra_detalle.id_tab_unidad_medida', '=', 't03.id')
        ->where('administracion.tab_compra_detalle.in_activo', '=', true)
        ->where('id_tab_solicitud', '=', $id)
        ->whereNotIn('administracion.tab_compra_detalle.id', $filtro_retencion)
        ->get();

        $tab_proceso_retencion_factura = tab_proceso_retencion_factura::select( 'administracion.tab_proceso_retencion_factura.id', 'nu_factura',
        DB::raw(" to_char( fe_emision, 'dd-mm-YYYY') as fe_emision"), 'mo_base_imponible', 'mo_iva', 'mo_iva_retencion', 'mo_total', 'mo_retencion')
        ->join('administracion.tab_proceso_retencion as t01', 'administracion.tab_proceso_retencion_factura.id_tab_proceso_retencion', '=', 't01.id')
        ->where('administracion.tab_proceso_retencion_factura.in_activo', '=', true)
        ->where('id_tab_solicitud', '=', $id)
        ->get();

        return View::make('administracion.compra.compraContabilidad')->with([
            'id' => $id,
            'ruta' => $ruta,
            'data'  => $tab_compra,
            'tab_ramo_proveedor'  => $tab_ramo_proveedor,
            'tab_compra_detalle'  => $tab_compra_detalle,
            'tab_proceso_retencion_factura'  => $tab_proceso_retencion_factura,
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

            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = true;
            $tab_ruta->save();

            HelperReporte::generarReporte($request->solicitud);

            DB::commit();

            Session::flash('msg_side_overlay', 'Retenciones guardadas con Exito!');
            return Redirect::to('/proceso/ruta/lista/'.$request->solicitud);

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }

    }

        /**
    * Update the specified resource in storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function guardarDetalle( Request $request, $id = NULL)
    {

        DB::beginTransaction();
  
        try {

            $validator = Validator::make($request->all(), tab_proceso_retencion_factura::$validarCrear);

            if ($validator->fails()){
                Session::flash('msg_alerta', 'Error!');
                return Redirect::back()->withErrors( $validator)->withInput( $request->all());
            }

            if (tab_proceso_retencion::where('id_tab_solicitud', '=', $request->solicitud)->exists()) {

                $tab_proceso_retencion = tab_proceso_retencion::where('id_tab_solicitud', $request->solicitud)->first();

                $proceso_retencion = $tab_proceso_retencion->id;

                $tab_proceso_retencion = tab_proceso_retencion::find( $proceso_retencion);
                //$tab_proceso_retencion->id_tab_proveedor = $request->proveedor;
                //$tab_proceso_retencion->id_tab_compra = $request->compra;
                $tab_proceso_retencion->id_tab_ramo = $request->ramo_detalle;
                $tab_proceso_retencion->save();

            }else{

                $tab_proceso_retencion = new tab_proceso_retencion;
                $tab_proceso_retencion->id_tab_solicitud = $request->solicitud;
                $tab_proceso_retencion->id_tab_proveedor = $request->proveedor;
                $tab_proceso_retencion->id_tab_compra = $request->compra;
                $tab_proceso_retencion->id_tab_ramo = $request->ramo_detalle;
                $tab_proceso_retencion->in_activo = true;
                $tab_proceso_retencion->save();

                $proceso_retencion = $tab_proceso_retencion->id;

            }

            /*$retencion_detalle = tab_proceso_retencion_factura::where('id_tab_proceso_retencion', '=', $proceso_retencion)
            ->delete();*/

            $tab_proceso_retencion_factura = new tab_proceso_retencion_factura;
            $tab_proceso_retencion_factura->id_tab_proceso_retencion = $proceso_retencion;
            $tab_proceso_retencion_factura->nu_factura = $request->numero_factura;
            $tab_proceso_retencion_factura->nu_control = $request->numero_control;
            $tab_proceso_retencion_factura->fe_emision = $request->fecha_emision;
            $tab_proceso_retencion_factura->id_tab_compra_detalle = $request->producto;
            $tab_proceso_retencion_factura->nu_cantidad = $request->cant_requerida;
            $tab_proceso_retencion_factura->mo_valor_unitario = $request->valor_unitario;
            $tab_proceso_retencion_factura->nu_cant_factura = $request->cant_factura;
            $tab_proceso_retencion_factura->mo_factura = $request->monto_factura;
            $tab_proceso_retencion_factura->mo_base_imponible = $request->base_imponible;
            $tab_proceso_retencion_factura->nu_iva = $request->iva;
            $tab_proceso_retencion_factura->mo_iva = $request->iva_monto;
            $tab_proceso_retencion_factura->nu_iva_retencion = $request->iva_retencion;
            $tab_proceso_retencion_factura->mo_iva_retencion= $request->iva_retencion_monto;
            $tab_proceso_retencion_factura->mo_total= $request->total_pagar;
            $tab_proceso_retencion_factura->de_concepto= $request->concepto;
            $tab_proceso_retencion_factura->in_activo = true;
            $tab_proceso_retencion_factura->save();

            $arreglo_retener = $request->retener;
            $filtro = array();

            if ($request->has('retener')){
                foreach($arreglo_retener as $key => $valor){
                    $filtro[] = $key;
                }
            }

            $tab_retencion_proveedor = tab_retencion_proveedor::select( 'administracion.tab_retencion_proveedor.id', 't03.id_tab_documento', 't01.id_tab_tipo_retencion',
            'de_retencion', 'de_tipo_retencion', 'mo_minimo', 'porcentaje_retencion', 'mo_sustraendo')
            ->join('administracion.tab_retencion as t01', 'administracion.tab_retencion_proveedor.id_tab_retencion', '=', 't01.id')
            ->join('administracion.tab_tipo_retencion as t02', 't01.id_tab_tipo_retencion', '=', 't02.id')
            ->join('administracion.tab_proveedor as t03', 'administracion.tab_retencion_proveedor.id_tab_proveedor', '=', 't03.id')
            ->join('administracion.tab_concepto_retencion as t04',function ($j) {
                $j->on('t04.id_tab_retencion','=','administracion.tab_retencion_proveedor.id_tab_retencion')
                ->on('t04.id_tab_documento','=', 't03.id_tab_documento');
            })
            ->where('id_tab_proveedor', '=', $request->proveedor)
            ->where('id_tab_ramo', '=', $request->ramo_detalle)
            ->whereIn('administracion.tab_retencion_proveedor.id', $filtro)
            ->get();

            $tipo = array();
            $monto_retencion = 0;
            $base_imponible = $request->cant_requerida * $request->valor_unitario;
            $total_pago = $request->total_pagar;
    
            foreach($tab_retencion_proveedor as $reg){
    
                if($reg["id_tab_documento"] == 1 && $total_pago > $reg["mo_minimo"] ){              
                    $valor = ($base_imponible * ($reg["porcentaje_retencion"]/100)) - $reg["mo_sustraendo"];    
                }else{  
                    $valor = $base_imponible *($reg["porcentaje_retencion"]/100);
                }

                $tab_proceso_retencion_detalle = new tab_proceso_retencion_detalle;
                $tab_proceso_retencion_detalle->id_tab_proceso_retencion_factura = $tab_proceso_retencion_factura->id;
                $tab_proceso_retencion_detalle->id_tab_retencion_proveedor = $reg["id"];
                $tab_proceso_retencion_detalle->nu_porcentaje_retencion = $reg["porcentaje_retencion"];
                $tab_proceso_retencion_detalle->mo_retencion = $valor;
                $tab_proceso_retencion_detalle->id_tab_tipo_retencion = $reg["id_tab_tipo_retencion"];
                $tab_proceso_retencion_detalle->de_retencion = $reg["de_retencion"];
                $tab_proceso_retencion_detalle->de_tipo_retencion = $reg["de_tipo_retencion"];
                $tab_proceso_retencion_detalle->in_activo = true;
                $tab_proceso_retencion_detalle->save();

                $monto_retencion = $monto_retencion + $valor;
    
            }

            $tabla = tab_proceso_retencion_factura::find($tab_proceso_retencion_factura->id);
            $tabla->mo_retencion = $monto_retencion;
            $tabla->save();

            $model = tab_asiento_contable::where( 'id_tab_solicitud', $request->solicitud)->where( 'id_tab_tipo_asiento', 1);
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
            $tab_asiento_contable->id_tab_tipo_asiento = 1;
            $tab_asiento_contable->mo_debe = $total_pago;
            $tab_asiento_contable->id_tab_usuario = Auth::user()->id;
            $tab_asiento_contable->in_activo = true;
            $tab_asiento_contable->save();

            $tab_asiento_contable = new tab_asiento_contable;
            $tab_asiento_contable->id_tab_solicitud = $request->solicitud;
            $tab_asiento_contable->id_tab_cuenta_contable = $cuenta_documento->id_cc_odp;
            $tab_asiento_contable->id_tab_tipo_asiento = 1;
            $tab_asiento_contable->mo_haber = $total_pago;
            $tab_asiento_contable->id_tab_usuario = Auth::user()->id;
            $tab_asiento_contable->in_activo = true;
            $tab_asiento_contable->save();

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

        /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function retenciones(Request $request)
    {

        /*$tab_notificacion = tab_notificacion::select( 'id', 'de_notificacion', 'created_at', 'de_icono')
        ->where('id_tab_usuario', '=', Auth::user()->id)
        ->where('in_activo', '=', true);

        $response['success']  = 'true';
        $response['total']  = $tab_notificacion->count();

        $tab_notificacion = $tab_notificacion->orderby('id','DESC')->limit(5)->get()->toArray();

        $registros = null;

        foreach($tab_notificacion as $notificacion) {
            $registros[] = array(
                "id"  => trim($notificacion['id']),
                "de_notificacion"  => trim($notificacion['de_notificacion']),
                "de_icono"  => trim($notificacion['de_icono']),
                "TimeAgo"  => trim(tab_notificacion::getTimeAgo($notificacion['created_at']))
            );
        }

        $response['data']  = $registros;*/

        $monto_factura = $request->cant_factura * $request->valor_unitario;
        $base_imponible = $request->cant_factura * $request->valor_unitario;
        $monto_iva = $monto_factura * ($request->porcentaje_iva_factura / 100);
        $monto_iva_retencion = $monto_iva * ($request->porcentaje_iva_retencion / 100);
        $total_pago = $base_imponible - $monto_iva_retencion;

        //DB::enableQueryLog();

        $tab_retencion_proveedor = tab_retencion_proveedor::select( 'administracion.tab_retencion_proveedor.id', 't03.id_tab_documento', 't01.id_tab_tipo_retencion',
        'de_retencion', 'de_tipo_retencion', 'mo_minimo', 'porcentaje_retencion', 'mo_sustraendo')
        ->join('administracion.tab_retencion as t01', 'administracion.tab_retencion_proveedor.id_tab_retencion', '=', 't01.id')
        ->join('administracion.tab_tipo_retencion as t02', 't01.id_tab_tipo_retencion', '=', 't02.id')
        ->join('administracion.tab_proveedor as t03', 'administracion.tab_retencion_proveedor.id_tab_proveedor', '=', 't03.id')
        ->join('administracion.tab_concepto_retencion as t04',function ($j) {
            $j->on('t04.id_tab_retencion','=','administracion.tab_retencion_proveedor.id_tab_retencion')
            ->on('t04.id_tab_documento','=', 't03.id_tab_documento');
        })
        ->where('id_tab_proveedor', '=', $request->proveedor)
        ->where('id_tab_ramo', '=', $request->ramo)
        ->get();

        //dd(DB::getQueryLog());

        $tipo = array();
        $monto_retencion = 0;

        foreach($tab_retencion_proveedor as $reg){

            if($reg["id_tab_documento"] == 1 && $total_pago > $reg["mo_minimo"] ){              
                $valor = ($base_imponible * ($reg["porcentaje_retencion"]/100)) - $reg["mo_sustraendo"];    
            }else{  
                $valor = $base_imponible *($reg["porcentaje_retencion"]/100);
            }

            $tipo[] = array(
                "id" => $reg["id"],
                "porcentaje_retencion" => $reg["porcentaje_retencion"],
                "mo_retencion" => $valor,
                "id_tab_tipo_retencion" => $reg["id_tab_tipo_retencion"],
                "de_retencion" => $reg["de_retencion"],
                "de_tipo_retencion" => $reg["de_tipo_retencion"]
            );

            $monto_retencion = $monto_retencion + $valor;

        }

        $total_pago = $total_pago - $monto_retencion;
        
        $response['data']  = [
            'monto_factura' => $monto_factura,
            'base_imponible' => $base_imponible,
            'monto_iva' => $monto_iva,
            'monto_iva_retencion' => $monto_iva_retencion,
            'monto_retencion' => $monto_retencion,
            'total_pago' => $total_pago,
            'tipo' => $tipo
        ];

        $response['success']  = 'true';

		return Response::json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function borrarDetalle( $id, Request $request)
    {
        DB::beginTransaction();
        try {

            $tabla = tab_proceso_retencion_factura::find( $request->get("id"));
            $tabla->delete();

            $model = tab_proceso_retencion_detalle::where( 'id_tab_proceso_retencion_factura', $request->get("id"));
            $model->delete();

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro borrado con Exito!');
            return Redirect::to('/proceso/ruta/datos/'.$id);

        }catch (\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());
        }
    }
    
}
