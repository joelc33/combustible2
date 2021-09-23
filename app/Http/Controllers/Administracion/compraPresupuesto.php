<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Proceso\tab_ruta;
use gobela\Models\Administracion\tab_ejecutor;
use gobela\Models\Administracion\tab_compra;
use gobela\Models\Administracion\tab_compra_detalle;
use gobela\Models\Administracion\tab_catalogo_partida;
use gobela\Models\Administracion\tab_fuente_financiamiento;
use gobela\Models\Administracion\tab_asignar_partida;
use gobela\Models\Administracion\tab_asignar_partida_detalle;
use gobela\Models\Administracion\tab_presupuesto_egreso;
use gobela\Models\Administracion\tab_accion_especifica;
use gobela\Models\Administracion\tab_partida_egreso;
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

class compraPresupuesto extends Controller
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
    public function asignarPartida( $request, $id, $ruta)
    {
        $tab_compra = tab_compra::select( 'administracion.tab_compra.id', 'id_tab_solicitud', 'id_tab_proveedor', 'id_tab_tipo_contrato', 'nu_orden_pre_impresa', 
        DB::raw(" to_char( fe_ini, 'dd-mm-YYYY') as fe_ini"), DB::raw(" to_char( fe_fin, 'dd-mm-YYYY') as fe_fin"), DB::raw(" to_char( fe_entrega, 'dd-mm-YYYY') as fe_entrega"), 'in_compromiso_rs', 'mo_contrato', 'de_garantia', 'de_observacion', DB::raw(" to_char( fe_compra, 'dd-mm-YYYY') as fe_compra"), 'id_tab_iva_factura', 
        'id_tab_ejecutor_entrega', 'administracion.tab_compra.in_activo', 'administracion.tab_compra.created_at', 'administracion.tab_compra.updated_at',
        'id_tab_documento', 'nu_documento', 'de_proveedor', 'tx_direccion', 'de_inicial')
        ->join('administracion.tab_proveedor as t01', 'administracion.tab_compra.id_tab_proveedor', '=', 't01.id')
        ->join('configuracion.tab_documento as t02', 't01.id_tab_documento', '=', 't02.id')
        ->where('id_tab_solicitud', $id)
        ->first();

        $tab_ejecutor = tab_ejecutor::orderBy('id','asc')
        ->get();

        $tab_fuente_financiamiento = tab_fuente_financiamiento::orderBy('id','asc')
        ->get();

        $tab_compra_detalle = tab_compra_detalle::select( 'administracion.tab_compra_detalle.id', 'nu_cantidad', 'de_especificacion', 'nu_producto',
        'de_producto', 'de_unidad_medida', 'mo_precio_unitario', 'mo_precio_total', 'administracion.tab_compra_detalle.id_tab_catalogo_partida', 'co_partida', 'de_partida',
        'id_tab_asignar_partida_detalle', 'administracion.tab_compra_detalle.id_tab_producto')
        ->join('administracion.tab_compra as t01', 'administracion.tab_compra_detalle.id_tab_compra', '=', 't01.id')
        ->join('administracion.tab_producto as t02', 'administracion.tab_compra_detalle.id_tab_producto', '=', 't02.id')
        ->join('administracion.tab_unidad_medida as t03', 'administracion.tab_compra_detalle.id_tab_unidad_medida', '=', 't03.id')
        ->leftJoin('administracion.tab_asignar_partida_detalle as t04', 'administracion.tab_compra_detalle.id_tab_asignar_partida_detalle', '=', 't04.id')
        ->leftJoin('administracion.tab_partida_egreso as t05', 't04.id_tab_partida_egreso', '=', 't05.id')
        ->where('administracion.tab_compra_detalle.in_activo', '=', true)
        ->where('id_tab_solicitud', '=', $id)
        ->get();

        $tab_asignar_partida = tab_asignar_partida::select( 'administracion.tab_asignar_partida.id', 'id_tab_solicitud', 'id_tab_proveedor', 'mo_presupuesto', 'id_tab_ejecutor', 'id_tab_fuente_financiamiento',
        'nu_documento', 'de_proveedor', 'tx_direccion', 'de_inicial')
        ->join('administracion.tab_proveedor as t01', 'administracion.tab_asignar_partida.id_tab_proveedor', '=', 't01.id')
        ->join('configuracion.tab_documento as t02', 't01.id_tab_documento', '=', 't02.id')
        ->join('administracion.tab_fuente_financiamiento as t03', 'administracion.tab_asignar_partida.id_tab_fuente_financiamiento', '=', 't03.id')
        ->where('id_tab_solicitud', $id)
        ->first();

        if( !$tab_asignar_partida){

            return View::make('administracion.compra.asignarPartida')->with([
                'id' => $id,
                'ruta' => $ruta,
                'data'  => $tab_compra,
                'tab_ejecutor'  => $tab_ejecutor,
                'tab_fuente_financiamiento'  => $tab_fuente_financiamiento,
                'tab_compra_detalle'  => $tab_compra_detalle,
            ]);

        }else{

            return View::make('administracion.compra.asignarPartidaEditar')->with([
                'id' => $id,
                'ruta' => $ruta,
                'data'  => $tab_asignar_partida,
                'tab_ejecutor'  => $tab_ejecutor,
                'tab_fuente_financiamiento'  => $tab_fuente_financiamiento,
                'tab_compra_detalle'  => $tab_compra_detalle,
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
  
  
        }else{
  
            try {

                $validator = Validator::make($request->all(), tab_asignar_partida::$validarCrear);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                if (tab_asignar_partida::where('id_tab_solicitud', '=', $request->solicitud)->exists()) {

                    $tab_asignar_partida = tab_asignar_partida::where('id_tab_solicitud', $request->solicitud)->first();

                    $tab_asignar_partida = tab_asignar_partida::find($tab_asignar_partida->id);
                    $tab_asignar_partida->id_tab_proveedor = $request->proveedor;
                    $tab_asignar_partida->save();

                }else{

                    $tab_asignar_partida = new tab_asignar_partida;
                    $tab_asignar_partida->id_tab_solicitud = $request->solicitud;
                    $tab_asignar_partida->id_tab_proveedor = $request->proveedor;
                    $tab_asignar_partida->mo_presupuesto = $request->monto;
                    $tab_asignar_partida->id_tab_ejecutor = $request->ejecutor;
                    $tab_asignar_partida->id_tab_fuente_financiamiento = $request->fuente_financiamiento;
                    $tab_asignar_partida->in_activo = true;
                    $tab_asignar_partida->save();

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
    public function guardarDetalle( Request $request)
    {
        DB::beginTransaction();
  
        try {

            $validator = Validator::make($request->all(), tab_asignar_partida_detalle::$validarCrear);

            if ($validator->fails()){
                Session::flash('msg_alerta', 'Error!');
                return Redirect::back()->withErrors( $validator)->withInput( $request->all());
            }

            if (tab_asignar_partida::where('id_tab_solicitud', '=', $request->solicitud)->exists()) {

                $tab_asignar_partida = tab_asignar_partida::where('id_tab_solicitud', $request->solicitud)->first();

                $asignar_partida = $tab_asignar_partida->id;

                $tab_asignar_partida = tab_asignar_partida::find( $asignar_partida);
                $tab_asignar_partida->id_tab_ejecutor = $request->ejecutor;
                $tab_asignar_partida->id_tab_fuente_financiamiento = $request->fuente_financiamiento;
                $tab_asignar_partida->save();

            }else{

                $tab_asignar_partida = new tab_asignar_partida;
                $tab_asignar_partida->id_tab_solicitud = $request->solicitud;
                $tab_asignar_partida->mo_presupuesto = $request->monto_contrato;
                $tab_asignar_partida->id_tab_proveedor = $request->proveedor;
                $tab_asignar_partida->id_tab_ejecutor = $request->ejecutor;
                $tab_asignar_partida->id_tab_fuente_financiamiento = $request->fuente_financiamiento;
                $tab_asignar_partida->in_activo = true;
                $tab_asignar_partida->save();

                $asignar_partida = $tab_asignar_partida->id;

            }

            $tab_compra_detalle = tab_compra_detalle::find($request->compra_detalle);
            $tab_compra_detalle->id_tab_asignar_partida_detalle = null;
            $tab_compra_detalle->save();

            $partida_detalle = tab_asignar_partida_detalle::where('id_tab_asignar_partida', '=', $asignar_partida)
            ->where('id_tab_compra_detalle', '=', $request->compra_detalle)
            ->delete();

            $tab_asignar_partida_detalle = new tab_asignar_partida_detalle;
            $tab_asignar_partida_detalle->id_tab_asignar_partida = $asignar_partida;
            $tab_asignar_partida_detalle->id_tab_ejecutor = $request->ejecutor;
            $tab_asignar_partida_detalle->id_tab_catalogo_partida = $request->partida_general;
            $tab_asignar_partida_detalle->id_tab_presupuesto_egreso = $request->proyecto_ac;
            $tab_asignar_partida_detalle->id_tab_accion_especifica = $request->accion_especifica;
            $tab_asignar_partida_detalle->id_tab_partida_egreso = $request->partida;
            $tab_asignar_partida_detalle->mo_disponible = $request->monto_disponible;
            $tab_asignar_partida_detalle->id_tab_compra_detalle = $request->compra_detalle;
            $tab_asignar_partida_detalle->id_tab_producto = $request->producto;
            $tab_asignar_partida_detalle->in_activo = true;
            $tab_asignar_partida_detalle->save();

            $tab_compra_detalle = tab_compra_detalle::find($request->compra_detalle);
            $tab_compra_detalle->id_tab_asignar_partida_detalle = $tab_asignar_partida_detalle->id;
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

            $tabla = tab_asignar_partida_detalle::find( $request->get("id"));
            $tabla->delete();

            $tab_compra_detalle = tab_compra_detalle::find($tabla->id_tab_compra_detalle);
            $tab_compra_detalle->id_tab_asignar_partida_detalle = null;
            $tab_compra_detalle->save();

            DB::commit();

            Session::flash('msg_side_overlay', 'Partida liberada con Exito!');
            return Redirect::to('/proceso/ruta/datos/'.$id);

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
    public function catalogo(Request $request)
    {
		$response['success']  = 'true';
        $response['data']  = tab_catalogo_partida::orderby('id','ASC')->get()->toArray();
		return Response::json($response, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function proyectoAc(Request $request)
    {
		$response['success']  = 'true';
        $response['data']  = tab_presupuesto_egreso::where('id_tab_ejercicio_fiscal', Session::get('ejercicio'))
            ->where('id_tab_ejecutor', $request->get("ejecutor"))
            ->orderby('id','ASC')->get()->toArray();

		return Response::json($response, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function proyectoAcAe(Request $request)
    {
		$response['success']  = 'true';
        $response['data']  = tab_accion_especifica::where('id_tab_presupuesto_egreso', $request->get("proyecto_ac"))
            ->orderby('id','ASC')->get()->toArray();

		return Response::json($response, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function partida(Request $request)
    {
		$response['success']  = 'true';
        $response['data']  = tab_partida_egreso::where('id_tab_accion_especifica', $request->get("accion_especifica"))
        ->where('id_tab_ejercicio_fiscal', Session::get('ejercicio'))
        ->orderby('id','ASC')->get()->toArray();

		return Response::json($response, 200);
    }
}
