<?php

namespace App\Models\Autenticar;

use Illuminate\Database\Eloquent\Model;

class tab_rol_menu extends Model
{
    
    protected $connection= 'principal';

    //Todos los modelos deben extender la clase Eloquent
    protected $table = 'autenticacion.tab_rol_menu';

    
    public static function sp_catidad_menu_hijo($id_padre, $id_tab_rol) {
        return DB::select('SELECT autenticacion.sp_catidad_menu_hijo(?, ?)', array($id_padre, $id_tab_rol));
    }
  
    public static function sp_catidad_menu_privilegio($id_padre, $id_tab_rol) {
        return DB::select('SELECT autenticacion.sp_catidad_menu_privilegio(?, ?)', array($id_padre, $id_tab_rol));
    }

    protected function arbol( $rol)
    {

        $menu = tab_menu::select('id_tab_menu', 'de_icono', 'de_menu', 'autenticacion.tab_menu.id as id_menu')
        ->join('autenticacion.tab_rol_menu AS rol_menu', 'rol_menu.id_tab_menu', '=', 'autenticacion.tab_menu.id')
        ->where('id_padre', '=', 0)
        ->where('rol_menu.id_tab_rol', '=', $rol)
        //->where('rol_menu.in_estatus', '=', true)
        ->orderBy('nu_orden', 'ASC')
        ->get();

        $arbol = '';

        foreach($menu as $item){
//            $cantidad = tab_menu::sp_catidad_menu_hijo( $item->id_tab_menu, $rol);
//            if($cantidad[0]->sp_catidad_menu_hijo > 0)
//            {
  
                $arbol.= '<li class="nav-main-item">
                            <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
                                <i class="nav-main-link-icon '.$item->de_icono.'"></i>
                                <span class="nav-main-link-name">'.$item->de_menu.'</span>
                            </a>
                            '.self::ArmaSubmenu( $item->id_tab_menu, $rol).'
                        </li>';
  
//            }
        }

        return  $arbol;

    }

    static public function ArmaSubmenu( $co_padre, $co_rol){

        $menu = tab_menu::select('t04.id','id_tab_menu','de_menu','de_icono','da_url','nu_margen', 'de_detalle','t04.in_estatus')
        ->join('autenticacion.tab_rol_menu as t04', 'autenticacion.tab_menu.id', '=', 't04.id_tab_menu')
        ->where('id_padre', '=', $co_padre)
        ->where('t04.id_tab_rol', '=', $co_rol)
        ->where('autenticacion.tab_menu.in_estatus', '=', true)
        ->orderBy('nu_orden', 'ASC')->get();
    
        $submenu = '';
        foreach($menu as $items){
            $cantidad = tab_menu::sp_catidad_menu_privilegio($items->id_tab_menu, $co_rol);
            if($cantidad[0]->sp_catidad_menu_privilegio > 0)
            {

                $submenu.= '<ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
                            <i class="nav-main-link-icon fa fa-th-list"></i>
                            <span class="nav-main-link-name">'.$items->de_menu.'</span>
                        </a>
                        '.self::ArmaSubmenu( $items->id_tab_menu, $co_rol).'
                    </li>
                </ul>';

            }else{
                
            if($items->in_estatus){
                $checked = 'checked';
            }else{
                $checked = '';
            }                

                $submenu.= '<ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <div class="custom-control custom-switch mb-1">
                            <input type="checkbox" class="custom-control-input" id="seleccion['.$items->id.']" name="seleccion['.$items->id.']" '.$checked.'>
                            <label class="custom-control-label" for="seleccion['.$items->id.']">'.$items->de_menu.'</label>
                        </div>                        
                    </li>
                </ul>';
            }
        }

        return  $submenu;
    }    
    
    //
}
