<?php

namespace App\Http\Controllers\Autenticar;
//*******agregar esta linea******//
use App\Models\Autenticar\tab_rol_menu;
use View;
use Validator;
use Response;
use DB;
use Session;
use Redirect;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class rolMenuController extends Controller
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
    public function lista( $id)
    {
        $data = array("id_tab_rol" => $id);
        $arbol = tab_rol_menu::arbol( $id);

        return View::make('autenticar.rol.menu.lista')
        ->with( 'data', $data)
        ->with( 'arbol', $arbol);
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
            var_dump($request->id_tab_rol);
          $sql = "update tab_rol_menu
                                    set in_estatus = true
                                  where id in(
                                         select id_tab_rol_menu from vista_rol_menu where id_tab_rol = $request->id_tab_rol and cantidad_hijo > 0)";

          $update_uno = DB::select($sql);
          
          
          $sql2 = "update tab_rol_menu
                                    set in_estatus = false
                                  where id in(
                                         select id_tab_rol_menu from vista_rol_menu where id_tab_rol = $request->id_tab_rol and cantidad_hijo = 0)";
          $update_dos = DB::select($sql2);
            
            
            $opcion = $request->seleccion;

            foreach ($opcion as $key => $lista){
                $tab_rol_menu = tab_rol_menu::find($key);
                $tab_rol_menu->in_estatus = true;
                $tab_rol_menu->save();
            }

            DB::commit();

            Session::flash('msg_side_overlay', 'Registro Guardado con Exito!');
            return Redirect::to('/autenticar/rol/lista');

        }catch (\Illuminate\Database\QueryException $e){

            DB::rollback();
            return Redirect::back()->withErrors([
                'da_alert_form' => $e->getMessage()
            ])->withInput( $request->all());

        }
    }
}
