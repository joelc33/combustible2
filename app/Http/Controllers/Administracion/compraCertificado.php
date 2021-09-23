<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Proceso\tab_ruta;
use gobela\Models\Administracion\tab_asignar_partida;
use gobela\Models\Administracion\tab_asignar_partida_detalle;
use gobela\Models\Administracion\tab_ejecutor;
use gobela\Models\Administracion\tab_accion_especifica;
use gobela\Models\Administracion\tab_partida_egreso;
use gobela\Models\Administracion\tab_presupuesto_egreso;
use gobela\Models\Administracion\tab_partida_egreso_movimiento;
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

class compraCertificado extends Controller
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
    public function materiales( $request, $id, $ruta)
    {

        $tab_asignar_partida = tab_asignar_partida::select( 'administracion.tab_asignar_partida.id', 'id_tab_solicitud', 'id_tab_proveedor', 'mo_presupuesto', 'id_tab_ejecutor', 'id_tab_fuente_financiamiento',
        'nu_documento', 'de_proveedor', 'tx_direccion', 'de_inicial', 'nu_ejecutor', 'de_ejecutor')
        ->join('administracion.tab_proveedor as t01', 'administracion.tab_asignar_partida.id_tab_proveedor', '=', 't01.id')
        ->join('configuracion.tab_documento as t02', 't01.id_tab_documento', '=', 't02.id')
        ->join('administracion.tab_ejecutor as t03', 'administracion.tab_asignar_partida.id_tab_ejecutor', '=', 't03.id')
        ->where('id_tab_solicitud', $id)
        ->first();

        $tab_asignar_partida_detalle = tab_asignar_partida_detalle::select( 'administracion.tab_asignar_partida_detalle.id', 'nu_producto',
        'de_producto', 'co_partida', 'de_partida', 'mo_gasto','in_comprometer', 'in_causar', 'in_pagar', 'administracion.tab_asignar_partida_detalle.id_tab_ejecutor',
        'nu_ejecutor', 'de_ejecutor', 'administracion.tab_asignar_partida_detalle.id_tab_catalogo_partida', 'administracion.tab_asignar_partida_detalle.id_tab_presupuesto_egreso',
        'administracion.tab_asignar_partida_detalle.id_tab_accion_especifica', 'administracion.tab_asignar_partida_detalle.id_tab_partida_egreso', 'administracion.tab_asignar_partida_detalle.mo_disponible')
        ->join('administracion.tab_producto as t02', 'administracion.tab_asignar_partida_detalle.id_tab_producto', '=', 't02.id')
        ->join('administracion.tab_partida_egreso as t05', 'administracion.tab_asignar_partida_detalle.id_tab_partida_egreso', '=', 't05.id')
        ->join('administracion.tab_ejecutor as t03', 'administracion.tab_asignar_partida_detalle.id_tab_ejecutor', '=', 't03.id')
        ->where('administracion.tab_asignar_partida_detalle.in_activo', '=', true)
        ->where('id_tab_asignar_partida', '=', $tab_asignar_partida->id)
        ->get();

        return View::make('administracion.compra.compraCertificado')->with([
            'id' => $id,
            'ruta' => $ruta,
            'data'  => $tab_asignar_partida,
            'tab_asignar_partida_detalle'  => $tab_asignar_partida_detalle,
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function comprometer( Request $request)
    {
        DB::beginTransaction();
        try {

            $egreso_movimiento = tab_partida_egreso_movimiento::where('id_tab_solicitud', '=', $request->solicitud)
            ->delete();

            $tab_asignar_partida_detalle = tab_asignar_partida_detalle::select( 'administracion.tab_asignar_partida_detalle.id', 'nu_producto',
            'de_producto', 'co_partida', 'de_partida', 'mo_gasto', 'id_tab_partida_egreso')
            ->join('administracion.tab_producto as t02', 'administracion.tab_asignar_partida_detalle.id_tab_producto', '=', 't02.id')
            ->join('administracion.tab_partida_egreso as t05', 'administracion.tab_asignar_partida_detalle.id_tab_partida_egreso', '=', 't05.id')
            ->join('administracion.tab_asignar_partida as t01', 'administracion.tab_asignar_partida_detalle.id_tab_asignar_partida', '=', 't01.id')
            ->where('administracion.tab_asignar_partida_detalle.in_activo', '=', true)
            ->where('id_tab_solicitud', '=', $request->solicitud)
            ->get();

            foreach($tab_asignar_partida_detalle as $key => $campo){

                $tab_partida_egreso_movimiento = new tab_partida_egreso_movimiento;
                $tab_partida_egreso_movimiento->id_tab_solicitud = $request->solicitud;
                $tab_partida_egreso_movimiento->id_tab_partida_egreso = $campo->id_tab_partida_egreso;
                $tab_partida_egreso_movimiento->mo_movimiento = $campo->mo_gasto;
                $tab_partida_egreso_movimiento->id_tab_tipo_movimiento_egreso = 1;
                $tab_partida_egreso_movimiento->id_tab_ejercicio_fiscal = Session::get('ejercicio');
                $tab_partida_egreso_movimiento->id_tab_usuario = Auth::user()->id;
                $tab_partida_egreso_movimiento->in_activo = true;
                $tab_partida_egreso_movimiento->save();

                $asignar_partida_detalle = tab_asignar_partida_detalle::find( $campo->id);
                $asignar_partida_detalle->in_comprometer = true;
                $asignar_partida_detalle->save();

            }

            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = true;
            $tab_ruta->save();

            HelperReporte::generarReporte($request->solicitud);

            DB::commit();

            Session::flash('msg_side_overlay', 'Partidas comprometidas con Exito!');
            return Redirect::to('/proceso/ruta/datos/'.$request->solicitud);

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
    public function descomprometer( Request $request)
    {
        DB::beginTransaction();
        try {

            /*$tabla = tab_tipo_retencion::find( $id);
            $tabla->in_activo = false;
            $tabla->save();*/

            $egreso_movimiento = tab_partida_egreso_movimiento::where('id_tab_solicitud', '=', $request->solicitud)
            ->delete();

            $tab_asignar_partida_detalle = tab_asignar_partida_detalle::select( 'administracion.tab_asignar_partida_detalle.id', 'nu_producto',
            'de_producto', 'co_partida', 'de_partida', 'mo_gasto', 'id_tab_partida_egreso', 'in_comprometer', 'in_causar', 'in_pagar')
            ->join('administracion.tab_producto as t02', 'administracion.tab_asignar_partida_detalle.id_tab_producto', '=', 't02.id')
            ->join('administracion.tab_partida_egreso as t05', 'administracion.tab_asignar_partida_detalle.id_tab_partida_egreso', '=', 't05.id')
            ->join('administracion.tab_asignar_partida as t01', 'administracion.tab_asignar_partida_detalle.id_tab_asignar_partida', '=', 't01.id')
            ->where('administracion.tab_asignar_partida_detalle.in_activo', '=', true)
            ->where('id_tab_solicitud', '=', $request->solicitud)
            ->get();

            foreach($tab_asignar_partida_detalle as $key => $campo){

                $asignar_partida_detalle = tab_asignar_partida_detalle::find( $campo->id);
                $asignar_partida_detalle->in_comprometer = false;
                $asignar_partida_detalle->save();

            }

            $tab_ruta = tab_ruta::find( $request->ruta);
            $tab_ruta->in_datos = false;
            $tab_ruta->in_reporte = false;
            $tab_ruta->save();

            //HelperReporte::generarReporte($request->solicitud);

            DB::commit();

            Session::flash('msg_side_overlay', 'Partidas descomprometidas con Exito!');
            return Redirect::to('/proceso/ruta/datos/'.$request->solicitud);

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
    public function editarDetalle( Request $request)
    {
        DB::beginTransaction();
  
        try {

            $validator = Validator::make($request->all(), tab_asignar_partida_detalle::$validarCrear);

            if ($validator->fails()){
                Session::flash('msg_alerta', 'Error!');
                return Redirect::back()->withErrors( $validator)->withInput( $request->all());
            }

            $tab_asignar_partida_detalle = tab_asignar_partida_detalle::find( $request->compra_detalle);
            $tab_asignar_partida_detalle->id_tab_ejecutor = $request->ejecutor;
            $tab_asignar_partida_detalle->id_tab_catalogo_partida = $request->partida_general;
            $tab_asignar_partida_detalle->id_tab_presupuesto_egreso = $request->proyecto_ac;
            $tab_asignar_partida_detalle->id_tab_accion_especifica = $request->accion_especifica;
            $tab_asignar_partida_detalle->id_tab_partida_egreso = $request->partida;
            $tab_asignar_partida_detalle->mo_disponible = $request->monto_disponible;
            $tab_asignar_partida_detalle->save();

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
