<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Proceso\tab_ruta;
use gobela\Models\Administracion\tab_producto;
use gobela\Models\Administracion\tab_unidad_medida;
use gobela\Models\Administracion\tab_requisicion;
use gobela\Models\Administracion\tab_requisicion_detalle;
use gobela\Models\Administracion\tab_ejecutor;
use gobela\Models\Configuracion\tab_documento;
use gobela\Models\Administracion\tab_tipo_contrato;
use gobela\Models\Administracion\tab_iva_factura;
use gobela\Models\Administracion\tab_compra;
use gobela\Models\Administracion\tab_compra_detalle;
use gobela\Models\Administracion\tab_catalogo_partida;
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
use HelperReporte;
//*******************************//
use Illuminate\Http\Request;

use gobela\Http\Requests;
use gobela\Http\Controllers\Controller;

class compraContrato extends Controller
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

        $tab_documento = tab_documento::orderBy('id','asc')
        ->where('in_activo', '=', true)
        ->get();

        $tab_tipo_contrato = tab_tipo_contrato::orderBy('id','asc')
        ->where('in_activo', '=', true)
        ->get();

        $tab_iva_factura = tab_iva_factura::orderBy('id','asc')
        ->where('in_activo', '=', true)
        ->get();

        $tab_ejecutor = tab_ejecutor::orderBy('id','asc')
        ->get();

        $tab_producto = tab_producto::orderBy('id','asc')
        ->get();

        $tab_unidad_medida = tab_unidad_medida::orderBy('id','asc')
        ->get();

        $tab_compra = tab_compra::select( 'administracion.tab_compra.id', 'id_tab_solicitud', 'id_tab_proveedor', 'id_tab_tipo_contrato', 'nu_orden_pre_impresa', 
        DB::raw(" to_char( fe_ini, 'dd-mm-YYYY') as fe_ini"), DB::raw(" to_char( fe_fin, 'dd-mm-YYYY') as fe_fin"), DB::raw(" to_char( fe_entrega, 'dd-mm-YYYY') as fe_entrega"), 'in_compromiso_rs', 'mo_contrato', 'de_garantia', 'de_observacion', DB::raw(" to_char( fe_compra, 'dd-mm-YYYY') as fe_compra"), 'id_tab_iva_factura', 
        'id_tab_ejecutor_entrega', 'administracion.tab_compra.in_activo', 'administracion.tab_compra.created_at', 'administracion.tab_compra.updated_at',
        'id_tab_documento', 'nu_documento', 'de_proveedor', 'tx_direccion')
        ->join('administracion.tab_proveedor as t01', 'administracion.tab_compra.id_tab_proveedor', '=', 't01.id')
        ->where('id_tab_solicitud', $id)
        ->first();

        $tab_catalogo_partida = tab_catalogo_partida::where('in_activo', true)
        ->get();

        $tab_compra_detalle = tab_compra_detalle::select( 'administracion.tab_compra_detalle.id', 'nu_cantidad', 'de_especificacion', 'nu_producto',
        'de_producto', 'de_unidad_medida', 'mo_precio_unitario', 'mo_precio_total')
        ->join('administracion.tab_compra as t01', 'administracion.tab_compra_detalle.id_tab_compra', '=', 't01.id')
        ->join('administracion.tab_producto as t02', 'administracion.tab_compra_detalle.id_tab_producto', '=', 't02.id')
        ->join('administracion.tab_unidad_medida as t03', 'administracion.tab_compra_detalle.id_tab_unidad_medida', '=', 't03.id')
        ->where('administracion.tab_compra_detalle.in_activo', '=', true)
        ->where('id_tab_solicitud', '=', $id)
        ->get();

        $tab_requisicion_detalle = tab_requisicion_detalle::select( 'administracion.tab_requisicion_detalle.id', 'nu_cantidad', 'de_especificacion', 'nu_producto',
        'de_producto', 'de_unidad_medida')
        ->join('administracion.tab_requisicion as t01', 'administracion.tab_requisicion_detalle.id_tab_requisicion', '=', 't01.id')
        ->join('administracion.tab_producto as t02', 'administracion.tab_requisicion_detalle.id_tab_producto', '=', 't02.id')
        ->join('administracion.tab_unidad_medida as t03', 'administracion.tab_requisicion_detalle.id_tab_unidad_medida', '=', 't03.id')
        ->where('administracion.tab_requisicion_detalle.in_activo', '=', true)
        ->where('id_tab_solicitud', '=', $id)
        ->get();

        if( !$tab_compra){

            return View::make('administracion.compra.contrato')->with([
                'id' => $id,
                'ruta' => $ruta,
                'tab_documento'  => $tab_documento,
                'tab_tipo_contrato'  => $tab_tipo_contrato,
                'tab_ejecutor'  => $tab_ejecutor,
                'tab_iva_factura'  => $tab_iva_factura,
                'tab_compra_detalle'  => $tab_compra_detalle,
                'tab_requisicion_detalle'  => $tab_requisicion_detalle,
                'tab_producto'  => $tab_producto,
                'tab_unidad_medida'  => $tab_unidad_medida,
                'tab_catalogo_partida'  => $tab_catalogo_partida,
            ]);

        }else{

            return View::make('administracion.compra.contratoEditar')->with([
                'id' => $id,
                'ruta' => $ruta,
                'data'  => $tab_compra,
                'tab_documento'  => $tab_documento,
                'tab_tipo_contrato'  => $tab_tipo_contrato,
                'tab_ejecutor'  => $tab_ejecutor,
                'tab_iva_factura'  => $tab_iva_factura,
                'tab_compra_detalle'  => $tab_compra_detalle,
                'tab_requisicion_detalle'  => $tab_requisicion_detalle,
                'tab_producto'  => $tab_producto,
                'tab_unidad_medida'  => $tab_unidad_medida,
                'tab_catalogo_partida'  => $tab_catalogo_partida,
            ]);

        }

    }

        /**
    * Update the specified resource in storage.
    *
    * @param  int  $id
    * @return Response
    */
    public function guardar( Request $request, $id = NULL)
    {
        DB::beginTransaction();

        if($id!=''||$id!=null){
  
            try {

                $validator= Validator::make($request->all(), tab_compra::$validarEditar);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                if (tab_compra::where('id_tab_solicitud', '=', $request->solicitud)->exists()) {

                    $tab_compra = tab_compra::where('id_tab_solicitud', $request->solicitud)->first();

                    $mo_compra_detalle = tab_compra_detalle::moTotal($tab_compra->id);

                    $tab_compra = tab_compra::find($tab_compra->id);
                    $tab_compra->id_tab_proveedor = $request->proveedor;
                    $tab_compra->id_tab_tipo_contrato = $request->tipo_contrato;
                    $tab_compra->nu_orden_pre_impresa = $request->orden_preimpresa;
                    $tab_compra->fe_ini = $request->fecha_inicio;
                    $tab_compra->fe_fin = $request->fecha_fin;
                    $tab_compra->fe_entrega = $request->fecha_entrega;
                    if (array_key_exists('in_compromiso', $request->all())) {
                        $tab_compra->in_compromiso_rs = true;
                    }else{
                        $tab_compra->in_compromiso_rs = false;
                    }
                    //$tab_compra->in_compromiso_rs = $request->in_compromiso;
                    $tab_compra->mo_contrato = $request->monto_contrato;
                    $tab_compra->de_garantia = $request->garantia;
                    $tab_compra->de_observacion = $request->observacion;
                    $tab_compra->fe_compra = $request->fecha_compra;
                    $tab_compra->id_tab_iva_factura = $request->iva;
                    $tab_compra->id_tab_ejecutor_entrega = $request->ejecutor_entrega;
                    $tab_compra->mo_total_compra = $mo_compra_detalle;
                    $tab_compra->save();

                }else{

                    $tab_compra = new tab_compra;
                    $tab_compra->id_tab_solicitud = $request->solicitud;
                    $tab_compra->id_tab_proveedor = $request->proveedor;
                    $tab_compra->id_tab_tipo_contrato = $request->tipo_contrato;
                    $tab_compra->nu_orden_pre_impresa = $request->orden_preimpresa;
                    $tab_compra->fe_ini = $request->fecha_inicio;
                    $tab_compra->fe_fin = $request->fecha_fin;
                    $tab_compra->fe_entrega = $request->fecha_entrega;
                    if (array_key_exists('in_compromiso', $request->all())) {
                        $tab_compra->in_compromiso_rs = true;
                    }else{
                        $tab_compra->in_compromiso_rs = false;
                    }
                    //$tab_compra->in_compromiso_rs = $request->in_compromiso;
                    $tab_compra->mo_contrato = $request->monto_contrato;
                    $tab_compra->de_garantia = $request->garantia;
                    $tab_compra->de_observacion = $request->observacion;
                    $tab_compra->fe_compra = $request->fecha_compra;
                    $tab_compra->id_tab_iva_factura = $request->iva;
                    $tab_compra->id_tab_ejecutor_entrega = $request->ejecutor_entrega;
                    $tab_compra->in_activo = true;
                    $tab_compra->save();

                }

                $tab_ruta = tab_ruta::find( $request->ruta);
                $tab_ruta->in_datos = true;
                $tab_ruta->save();

                HelperReporte::generarReporte($request->solicitud);

                DB::commit();
                
                Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
                return Redirect::to('/proceso/ruta/lista/'.$request->solicitud);

            }catch (\Illuminate\Database\QueryException $e){

                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());

            }
  
        }else{
  
            try {

                $validator = Validator::make($request->all(), tab_compra::$validarCrear);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                if (tab_compra::where('id_tab_solicitud', '=', $request->solicitud)->exists()) {

                    $tab_compra = tab_compra::where('id_tab_solicitud', $request->solicitud)->first();

                    $mo_compra_detalle = tab_compra_detalle::moTotal( $tab_compra->id);

                    $mo_compra_iva = tab_compra_detalle::moTotalIva( $tab_compra->id, $request->iva);

                    $tab_compra = tab_compra::find($tab_compra->id);
                    $tab_compra->id_tab_proveedor = $request->proveedor;
                    $tab_compra->id_tab_tipo_contrato = $request->tipo_contrato;
                    $tab_compra->nu_orden_pre_impresa = $request->orden_preimpresa;
                    $tab_compra->fe_ini = $request->fecha_inicio;
                    $tab_compra->fe_fin = $request->fecha_fin;
                    $tab_compra->fe_entrega = $request->fecha_entrega;
                    if (array_key_exists('in_compromiso', $request->all())) {
                        $tab_compra->in_compromiso_rs = true;
                    }else{
                        $tab_compra->in_compromiso_rs = false;
                    }
                    //$tab_compra->in_compromiso_rs = $request->in_compromiso;
                    $tab_compra->mo_contrato = $request->monto_contrato;
                    $tab_compra->de_garantia = $request->garantia;
                    $tab_compra->de_observacion = $request->observacion;
                    $tab_compra->fe_compra = $request->fecha_compra;
                    $tab_compra->id_tab_iva_factura = $request->iva;
                    $tab_compra->id_tab_ejecutor_entrega = $request->ejecutor_entrega;
                    $tab_compra->mo_total_compra = $mo_compra_detalle;
                    $tab_compra->mo_iva_compra = $mo_compra_iva;
                    $tab_compra->save();

                }else{

                    $tab_compra = new tab_compra;
                    $tab_compra->id_tab_solicitud = $request->solicitud;
                    $tab_compra->id_tab_proveedor = $request->proveedor;
                    $tab_compra->id_tab_tipo_contrato = $request->tipo_contrato;
                    $tab_compra->nu_orden_pre_impresa = $request->orden_preimpresa;
                    $tab_compra->fe_ini = $request->fecha_inicio;
                    $tab_compra->fe_fin = $request->fecha_fin;
                    $tab_compra->fe_entrega = $request->fecha_entrega;
                    if (array_key_exists('in_compromiso', $request->all())) {
                        $tab_compra->in_compromiso_rs = true;
                    }else{
                        $tab_compra->in_compromiso_rs = false;
                    }
                    //$tab_compra->in_compromiso_rs = $request->in_compromiso;
                    $tab_compra->mo_contrato = $request->monto_contrato;
                    $tab_compra->de_garantia = $request->garantia;
                    $tab_compra->de_observacion = $request->observacion;
                    $tab_compra->fe_compra = $request->fecha_compra;
                    $tab_compra->id_tab_iva_factura = $request->iva;
                    $tab_compra->id_tab_ejecutor_entrega = $request->ejecutor_entrega;
                    $tab_compra->in_activo = true;
                    $tab_compra->save();

                }

                $tab_ruta = tab_ruta::find( $request->ruta);
                $tab_ruta->in_datos = true;
                $tab_ruta->save();

                HelperReporte::generarReporte($request->solicitud);

                DB::commit();

                Session::flash('msg_side_overlay', 'Registro Guardado con Exito!');
                return Redirect::to('/proceso/ruta/lista/'.$request->solicitud);

            }catch (\Illuminate\Database\QueryException $e){

                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());

            }
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

        if($id!=''||$id!=null){
  
            try {

                $validator= Validator::make($request->all(), tab_compra_detalle::$validarEditar);

                if ($validator->fails()){
                    Session::flash('msg_alerta', 'Error!');
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                $tab_compra_detalle = tab_compra_detalle::find($id);
                $tab_compra_detalle->save();

                DB::commit();
                
                Session::flash('msg_side_overlay', 'Registro Editado con Exito!');
                return Redirect::to('/proceso/ruta/datos/'.$request->solicitud);

            }catch (\Illuminate\Database\QueryException $e){

                DB::rollback();
                return Redirect::back()->withErrors([
                    'da_alert_form' => $e->getMessage()
                ])->withInput( $request->all());

            }
  
        }else{
  
            try {

                $validator = Validator::make($request->all(), tab_compra_detalle::$validarCrear);

                if ($validator->fails()){
                    Session::flash('msg_alerta', 'Error!');
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                if (tab_compra::where('id_tab_solicitud', '=', $request->solicitud)->exists()) {

                    $tab_compra = tab_compra::where('id_tab_solicitud', $request->solicitud)->first();

                    $compra = $tab_compra->id;

                }else{

                    $tab_compra = new tab_compra;
                    $tab_compra->id_tab_solicitud = $request->solicitud;
                    $tab_compra->in_activo = true;
                    $tab_compra->save();

                    $compra = $tab_compra->id;

                }

                $tab_compra_detalle = new tab_compra_detalle;
                $tab_compra_detalle->id_tab_compra = $compra;
                $tab_compra_detalle->id_tab_producto = $request->id_tab_producto;
                $tab_compra_detalle->nu_cantidad = $request->cantidad;
                $tab_compra_detalle->id_tab_unidad_medida = $request->unidad;
                $tab_compra_detalle->de_especificacion = $request->especificacion;
                if (array_key_exists('excento_iva', $request->all())) {
                    $tab_compra_detalle->in_excento_iva = true;
                }else{
                    $tab_compra_detalle->in_excento_iva = false;
                }
                $tab_compra_detalle->mo_precio_unitario = $request->precio_unitario;
                $tab_compra_detalle->mo_precio_total = $request->cantidad*$request->precio_unitario;
                $tab_compra_detalle->id_tab_catalogo_partida = $request->catalogo_partida;
                $tab_compra_detalle->in_activo = true;
                $tab_compra_detalle->save();

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

            $tabla = tab_compra_detalle::find( $request->get("id"));
            $tabla->delete();

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
