<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Proceso\tab_ruta;
use gobela\Models\Administracion\tab_asignar_partida;
use gobela\Models\Administracion\tab_asignar_partida_detalle;
use gobela\Models\Administracion\tab_partida_egreso;
use gobela\Models\Administracion\tab_compra;
use gobela\Models\Administracion\tab_compra_detalle;
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

class compraOrdenCompra extends Controller
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
    public function ordenCompra( $request, $id, $ruta)
    {

        $tab_compra = tab_compra::select( 'administracion.tab_compra.id', 'id_tab_solicitud', 'id_tab_proveedor', 'id_tab_tipo_contrato', 'nu_orden_pre_impresa', 
        DB::raw(" to_char( fe_ini, 'dd-mm-YYYY') as fe_ini"), DB::raw(" to_char( fe_fin, 'dd-mm-YYYY') as fe_fin"), DB::raw(" to_char( fe_entrega, 'dd-mm-YYYY') as fe_entrega"), 'in_compromiso_rs', 'mo_contrato', 'de_garantia', 'de_observacion', DB::raw(" to_char( fe_compra, 'dd-mm-YYYY') as fe_compra"), 'id_tab_iva_factura', 
        'id_tab_ejecutor_entrega', 'administracion.tab_compra.in_activo', 'administracion.tab_compra.created_at', 'administracion.tab_compra.updated_at',
        'id_tab_documento', 'nu_documento', 'de_proveedor', 'tx_direccion', 'de_inicial', 'nu_iva_factura', 'nu_ejecutor', 'de_ejecutor', 'de_tipo_contrato')
        ->join('administracion.tab_proveedor as t01', 'administracion.tab_compra.id_tab_proveedor', '=', 't01.id')
        ->join('configuracion.tab_documento as t02', 't01.id_tab_documento', '=', 't02.id')
        ->join('administracion.tab_iva_factura as t03', 'administracion.tab_compra.id_tab_iva_factura', '=', 't03.id')
        ->join('administracion.tab_ejecutor as t04', 'administracion.tab_compra.id_tab_ejecutor_entrega', '=', 't04.id')
        ->join('administracion.tab_tipo_contrato as t05', 'administracion.tab_compra.id_tab_tipo_contrato', '=', 't05.id')
        ->where('id_tab_solicitud', $id)
        ->first();

        $tab_compra_detalle = tab_compra_detalle::select( 'administracion.tab_compra_detalle.id', 'nu_cantidad', 'de_especificacion', 'nu_producto',
        'de_producto', 'de_unidad_medida', 'mo_precio_unitario', 'mo_precio_total')
        ->join('administracion.tab_compra as t01', 'administracion.tab_compra_detalle.id_tab_compra', '=', 't01.id')
        ->join('administracion.tab_producto as t02', 'administracion.tab_compra_detalle.id_tab_producto', '=', 't02.id')
        ->join('administracion.tab_unidad_medida as t03', 'administracion.tab_compra_detalle.id_tab_unidad_medida', '=', 't03.id')
        ->where('administracion.tab_compra_detalle.in_activo', '=', true)
        ->where('id_tab_solicitud', '=', $id)
        ->get();

        return View::make('administracion.compra.ordenCompra')->with([
            'id' => $id,
            'ruta' => $ruta,
            'data'  => $tab_compra,
            'tab_compra_detalle'  => $tab_compra_detalle,
        ]);

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
  
        try {

            $validator = Validator::make($request->all(), tab_compra::$validarOrdenCompra);

            if ($validator->fails()){
                return Redirect::back()->withErrors( $validator)->withInput( $request->all());
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
