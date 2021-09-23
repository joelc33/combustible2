<?php

namespace App\Http\Controllers\Reporte;
//*******agregar esta linea******//
use App\Models\Configuracion\tab_empresa;
use DB;
use Session;
use TCPDF;
use setasign\Fpdi\Tcpdf\Fpdi;
use File;
//*******************************//
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//*******clase extendida TCPDF******//
class Vertical extends TCPDF
//class Vertical extends Fpdi
{
	public function Header()	
	{

        $tab_empresa = tab_empresa::select( 'im_sup_izquierda', 'in_sup_izquierda', 'im_sup_centro', 'in_sup_centro', 
        'im_sup_derecha', 'in_sup_derecha')
        ->where('id', '=', Session::get('empresa'))
        ->first();

        if($tab_empresa->in_sup_izquierda==true){

            $extension= File::extension(basename($tab_empresa->im_sup_izquierda));
            $this->Image(public_path().'/images/reporte/'.$tab_empresa->im_sup_izquierda, 10, 5, 40, 25, $extension, '', '', true, 200, '', false, false, 0, false, false, false);

        }

        if($tab_empresa->in_sup_centro==true){

            $extension= File::extension(basename($tab_empresa->im_sup_centro));
            $this->Image(public_path().'/images/reporte/'.$tab_empresa->im_sup_centro, 80, 5, 40, 25, $extension, '', '', true, 200, '', false, false, 0, false, false, false);

        }

        if($tab_empresa->in_sup_derecha==true){

            $extension= File::extension(basename($tab_empresa->im_sup_derecha));
            $this->Image(public_path().'/images/reporte/'.$tab_empresa->im_sup_derecha, 160, 5, 40, 25, $extension, '', '', true, 200, '', false, false, 0, false, false, false);

        }
        
    }
    
    public function Footer()
	{
        $tab_empresa = tab_empresa::select( 'im_inf_izquierda', 'in_inf_izquierda', 
        'im_inf_centro', 'in_inf_centro', 'im_inf_derecha', 'in_inf_derecha')
        ->where('id', '=', Session::get('empresa'))
        ->first();

        if($tab_empresa->in_inf_izquierda==true){

            $extension= File::extension(basename($tab_empresa->im_inf_izquierda));
            $this->Image(public_path().'/images/reporte/'.$tab_empresa->im_inf_izquierda, 10, 270, 40, 25, $extension, '', '', true, 200, '', false, false, 0, false, false, false);

        }

        if($tab_empresa->in_inf_centro==true){

            $extension= File::extension(basename($tab_empresa->im_inf_centro));
            $this->Image(public_path().'/images/reporte/'.$tab_empresa->im_inf_centro, 80, 270, 40, 25, $extension, '', '', true, 200, '', false, false, 0, false, false, false);

        }

        if($tab_empresa->in_inf_derecha==true){

            $extension= File::extension(basename($tab_empresa->im_inf_derecha));
            $this->Image(public_path().'/images/reporte/'.$tab_empresa->im_inf_derecha, 160, 270, 40, 25, $extension, '', '', true, 200, '', false, false, 0, false, false, false);

        }

	}
}