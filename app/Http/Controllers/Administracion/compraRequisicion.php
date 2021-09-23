<?php

namespace gobela\Http\Controllers\Administracion;
//*******agregar esta linea******//
use gobela\Models\Proceso\tab_ruta;
use gobela\Models\Administracion\tab_producto;
use gobela\Models\Administracion\tab_unidad_medida;
use gobela\Models\Administracion\tab_requisicion;
use gobela\Models\Administracion\tab_requisicion_detalle;
use gobela\Models\Administracion\tab_ejecutor;
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

class compraRequisicion extends Controller
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
    public function requisicion( $request, $id, $ruta)
    {

        $tab_ejecutor = tab_ejecutor::orderBy('id','asc')
        ->get();

        $tab_producto = tab_producto::orderBy('id','asc')
        ->get();

        $tab_unidad_medida = tab_unidad_medida::orderBy('id','asc')
        ->get();

        $tab_requisicion = tab_requisicion::where('id_tab_solicitud', $id)
        ->first();

        $tab_requisicion_detalle = tab_requisicion_detalle::select( 'administracion.tab_requisicion_detalle.id', 'nu_cantidad', 'de_especificacion', 'nu_producto',
        'de_producto', 'de_unidad_medida')
        ->join('administracion.tab_requisicion as t01', 'administracion.tab_requisicion_detalle.id_tab_requisicion', '=', 't01.id')
        ->join('administracion.tab_producto as t02', 'administracion.tab_requisicion_detalle.id_tab_producto', '=', 't02.id')
        ->join('administracion.tab_unidad_medida as t03', 'administracion.tab_requisicion_detalle.id_tab_unidad_medida', '=', 't03.id')
        ->where('administracion.tab_requisicion_detalle.in_activo', '=', true)
        ->where('id_tab_solicitud', '=', $id)
        ->get();

        if( !$tab_requisicion){

            return View::make('administracion.compra.requisicionNuevo')->with([
                'id' => $id,
                'ruta' => $ruta,
                'tab_requisicion_detalle'  => $tab_requisicion_detalle,
                'tab_ejecutor'  => $tab_ejecutor,
                'tab_producto'  => $tab_producto,
                'tab_unidad_medida'  => $tab_unidad_medida,
            ]);

        }else{

            return View::make('administracion.compra.requisicion')->with([
                'id' => $id,
                'ruta' => $ruta,
                'data'  => $tab_requisicion,
                'tab_requisicion_detalle'  => $tab_requisicion_detalle,
                'tab_ejecutor'  => $tab_ejecutor,
                'tab_producto'  => $tab_producto,
                'tab_unidad_medida'  => $tab_unidad_medida,
            ]);

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

                $validator= Validator::make($request->all(), tab_requisicion_detalle::$validarEditar);

                if ($validator->fails()){
                    Session::flash('msg_alerta', 'Error!');
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                $tab_requisicion_detalle = tab_requisicion_detalle::find($id);
                $tab_requisicion_detalle->save();

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

                $validator = Validator::make($request->all(), tab_requisicion_detalle::$validarCrear);

                if ($validator->fails()){
                    Session::flash('msg_alerta', 'Error!');
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                if (tab_requisicion::where('id_tab_solicitud', '=', $request->solicitud)->exists()) {

                    $tab_requisicion = tab_requisicion::where('id_tab_solicitud', $request->solicitud)->first();

                    $requisicion = $tab_requisicion->id;

                }else{

                    $tab_requisicion = new tab_requisicion;
                    $tab_requisicion->id_tab_solicitud = $request->solicitud;
                    $tab_requisicion->in_activo = true;
                    $tab_requisicion->save();

                    $requisicion = $tab_requisicion->id;

                }

                $tab_requisicion_detalle = new tab_requisicion_detalle;
                $tab_requisicion_detalle->id_tab_requisicion = $requisicion;
                $tab_requisicion_detalle->id_tab_producto = $request->producto;
                $tab_requisicion_detalle->nu_cantidad = $request->cantidad;
                $tab_requisicion_detalle->id_tab_unidad_medida = $request->unidad;
                $tab_requisicion_detalle->de_especificacion = $request->especificacion;
                $tab_requisicion_detalle->in_activo = true;
                $tab_requisicion_detalle->save();

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

                $validator= Validator::make($request->all(), tab_requisicion::$validarEditar);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                if (tab_requisicion::where('id_tab_solicitud', '=', $request->solicitud)->exists()) {

                    $tab_requisicion = tab_requisicion::where('id_tab_solicitud', $request->solicitud)->first();

                    $tab_requisicion = tab_requisicion::find($tab_requisicion->id);
                    $tab_requisicion->id_tab_ejecutor = $request->ejecutor;
                    $tab_requisicion->de_concepto = $request->concepto;
                    $tab_requisicion->de_observacion = $request->observacion;
                    $tab_requisicion->save();

                }else{

                    $tab_requisicion = new tab_requisicion;
                    $tab_requisicion->id_tab_solicitud = $request->solicitud;
                    $tab_requisicion->id_tab_ejecutor = $request->ejecutor;
                    $tab_requisicion->de_concepto = $request->concepto;
                    $tab_requisicion->de_observacion = $request->observacion;
                    $tab_requisicion->in_activo = true;
                    $tab_requisicion->save();

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

                $validator = Validator::make($request->all(), tab_requisicion::$validarCrear);

                if ($validator->fails()){
                    return Redirect::back()->withErrors( $validator)->withInput( $request->all());
                }

                if (tab_requisicion::where('id_tab_solicitud', '=', $request->solicitud)->exists()) {

                    $tab_requisicion = tab_requisicion::where('id_tab_solicitud', $request->solicitud)->first();

                    $tab_requisicion = tab_requisicion::find($tab_requisicion->id);
                    $tab_requisicion->id_tab_ejecutor = $request->ejecutor;
                    $tab_requisicion->de_concepto = $request->concepto;
                    $tab_requisicion->de_observacion = $request->observacion;
                    $tab_requisicion->save();

                }else{

                    $tab_requisicion = new tab_requisicion;
                    $tab_requisicion->id_tab_solicitud = $request->solicitud;
                    $tab_requisicion->id_tab_ejecutor = $request->ejecutor;
                    $tab_requisicion->de_concepto = $request->concepto;
                    $tab_requisicion->de_observacion = $request->observacion;
                    $tab_requisicion->in_activo = true;
                    $tab_requisicion->save();

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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function borrarDetalle( $id, Request $request)
    {
        DB::beginTransaction();
        try {

            $tabla = tab_requisicion_detalle::find( $request->get("id"));
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
