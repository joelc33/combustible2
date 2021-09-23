<?php

namespace App\Http\Controllers\Reporte\Administracion;
//*******agregar esta linea******//
use App\Models\Proceso\tab_ruta;
use App\Models\Administracion\tab_proceso_retencion;
use App\Models\Administracion\tab_proceso_retencion_factura;
use App\Models\Administracion\tab_proceso_retencion_detalle;
use App\Models\Configuracion\tab_empresa;
use App\Models\Administracion\tab_asignar_partida_detalle;
use App\Models\Administracion\tab_asiento_contable;
use DB;
use Session;
use Storage;
use TCPDF;
use File;
use HelperReporte;
use HelperUtiles;
//*******************************//
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reporte\Vertical;

class compraContabilidad extends Controller
{
    //
    public function __construct()
    {
      $this->middleware('auth');
    }

        /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function Factura_SopAntiguoV2( $ruta)
    {
        $tab_ruta = tab_ruta::select( 'id_tab_solicitud')
        ->where('proceso.tab_ruta.id', '=', $ruta)
        ->first();

        $tab_proceso_retencion = tab_proceso_retencion::select( 'administracion.tab_proceso_retencion.id', 'id_tab_solicitud', 'id_tab_proveedor', 'id_tab_compra', 
        'id_tab_ramo', 'nu_documento', 'de_proveedor', 'tx_direccion', 'de_inicial')
        ->join('administracion.tab_proveedor as t01', 'administracion.tab_proceso_retencion.id_tab_proveedor', '=', 't01.id')
        ->join('configuracion.tab_documento as t02', 't01.id_tab_documento', '=', 't02.id')
        ->where('id_tab_solicitud', $tab_ruta->id_tab_solicitud)
        ->first();
        
        $tab_proceso_retencion_factura = tab_proceso_retencion_factura::select( 'id', 'id_tab_proceso_retencion', 'nu_factura', 'nu_control', DB::raw(" to_char( fe_emision, 'dd-mm-YYYY') as fe_emision"), 
        'id_tab_compra_detalle', 'nu_cantidad', 'mo_valor_unitario', 'nu_cant_factura', 
        'mo_factura', 'mo_base_imponible', 'nu_iva', 'mo_iva', 'nu_iva_retencion', 
        'mo_iva_retencion', 'mo_total', 'de_concepto', 'in_activo', 'created_at', 
        'updated_at')
        ->where('id_tab_proceso_retencion', '=', $tab_proceso_retencion->id)
        ->get();

        $pdf = new Vertical("P", PDF_UNIT, 'Letter', true, 'UTF-8', false);   
        $pdf->SetMargins(10,10,10);
        $pdf->SetTopMargin(30);
        $pdf->SetPrintHeader(true);
        $pdf->SetPrintFooter(true);
        $pdf->SetAutoPageBreak(TRUE, 5);
        $pdf->SetTitle('REPORTE');
        $pdf->SetSubject('App');
        $pdf->SetKeywords('App, PDF, REPORTE');
        $pdf->AddPage();

        //******CONTENIDO*******//
        $pdf->SetY(25);
        $pdf->SetFont('','B',11);
        $pdf->MultiCell(190, 5, 'CONSIGNACIÓN', 0, 'C', 0, 0, '', '', true);
        //$pdf->SetY(35);

        foreach($tab_proceso_retencion_factura as $key => $campo){

            $pdf->SetFillColor(209, 209, 209);
            $pdf->setCellPaddings(1, 1, 1, 1);
            $pdf->ln(15);
            $pdf->SetFont('','B',8);
            $pdf->MultiCell(190, 6, 'DATOS DE LA FACTURA', 1, 'C', 1, 0, '', '', true);
            $pdf->ln(6);
            $pdf->SetFont('','',8);
            $pdf->MultiCell(50, 6, 'No.FACTURA: '.$campo->nu_factura, 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(45, 6, 'No.COMPRA: '.$campo->nu_control, 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(45, 6, 'No.CONTROL: '.$campo->nu_control, 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(50, 6, 'MONTO: '.number_format( $campo->mo_total, 2, '.', ','), 1, 'L', 0, 0, '', '', true);
            $pdf->ln(6);
            $pdf->SetFont('','',8);
            $pdf->MultiCell(190, 6, 'FECHA EMISION: '.$campo->fe_emision, 1, 'L', 0, 0, '', '', true);
            $pdf->ln(6);
            $pdf->MultiCell(190, 6, 'SEÑOR(ES): '.strtoupper($tab_proceso_retencion->de_proveedor).' '.$tab_proceso_retencion->de_inicial.'-'.$tab_proceso_retencion->nu_documento, 1, 'L', 0, 0, '', '', true);
            $pdf->ln(6);
            $pdf->MultiCell(190, 6, 'DIRECCIÓN: '.strtoupper($tab_proceso_retencion->tx_direccion), 1, 'L', 0, 0, '', '', true);
            $pdf->ln(6);
            $pdf->MultiCell(190, 6, 'CONCEPTO: '.strtoupper($campo->de_concepto), 1, 'L', 0, 0, '', '', true);

            $pdf->ln(12);
            $pdf->SetFont('','B',8);
            $pdf->MultiCell(190, 6, 'RETENCIONES ASOCIADAS', 1, 'C', 1, 0, '', '', true);
            $pdf->ln(6);
            $pdf->SetFont('','',8);
            $pdf->MultiCell(50, 6, 'BASE IMPONIBLE: '.number_format($campo->mo_base_imponible, 2, '.', ','), 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(45, 6, 'IVA: '.number_format($campo->mo_iva, 2, '.', ','), 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(45, 6, 'MONTO EXENTO: 0.00', 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(50, 6, 'TOTAL A PAGAR: '.number_format($campo->mo_total, 2, '.', ','), 1, 'L', 0, 0, '', '', true);

            $tab_asignar_partida_detalle = tab_asignar_partida_detalle::select( 'administracion.tab_asignar_partida_detalle.id', 'id_tab_asignar_partida',  
            'administracion.tab_asignar_partida_detalle.mo_disponible', 'id_tab_compra_detalle', 
            'id_tab_producto', 'mo_gasto', 'in_comprometer', 'in_causar', 'in_pagar', 'co_partida', 'de_partida')
            ->join('administracion.tab_asignar_partida as t01', 'administracion.tab_asignar_partida_detalle.id_tab_asignar_partida', '=', 't01.id')
            ->join('administracion.tab_partida_egreso as t02', 'administracion.tab_asignar_partida_detalle.id_tab_partida_egreso', '=', 't02.id')
            ->where('id_tab_solicitud', $tab_ruta->id_tab_solicitud)
            ->get();

            $pdf->ln(12);
            $pdf->SetFont('','B',8);
            $pdf->MultiCell(190, 6, 'CATEGORIAS PRESUPUESTARIAS', 1, 'C', 1, 0, '', '', true);
            $pdf->ln(6);
            $pdf->SetFont('','B',8);
            $pdf->MultiCell(60, 6, 'CÓDIGO', 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(80, 6, 'DESCRIPCIÓN DE PARTIDA', 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(50, 6, 'MONTO', 1, 'L', 0, 0, '', '', true);

            foreach($tab_asignar_partida_detalle as $key => $campo_tres){

                $pdf->ln(6);
                $pdf->SetFont('','',8);
                $pdf->MultiCell(60, 6, $campo_tres->co_partida, 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(80, 6, $campo_tres->de_partida, 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(50, 6, number_format( $campo_tres->mo_gasto, 2, '.', ','), 1, 'L', 0, 0, '', '', true);

            }

            $pdf->ln(12);
            $pdf->SetFont('','B',8);
            $pdf->MultiCell(190, 6, 'CODIGOS CONTABLES', 1, 'C', 1, 0, '', '', true);
            $pdf->ln(6);
            $pdf->SetFont('','B',8);
            $pdf->MultiCell(110, 6, 'CUENTA', 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(40, 6, 'DEBITOS', 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(40, 6, 'CREDITOS', 1, 'L', 0, 0, '', '', true);

            $tab_asiento_contable = tab_asiento_contable::select( 'nu_cuenta_contable', 'id_tab_solicitud', 'id_tab_cuenta_contable', 'id_tab_tipo_asiento', 
            'mo_debe', 'mo_haber', 'id_tab_usuario', 'de_cuenta_contable', 'co_cuenta_contable')
            ->join('administracion.tab_cuenta_contable as t01', 'administracion.tab_asiento_contable.id_tab_cuenta_contable', '=', 't01.id')
            ->where('id_tab_solicitud', $tab_ruta->id_tab_solicitud)
            ->where('id_tab_tipo_asiento', 1)
            ->get();

            foreach($tab_asiento_contable as $key => $campo_cuatro){

                $pdf->ln(6);
                $pdf->SetFont('','',8);
                $pdf->MultiCell(110, 6, $campo_cuatro->co_cuenta_contable.' - '.$campo_cuatro->de_cuenta_contable, 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(40, 6, number_format( $campo_cuatro->mo_debe, 2, '.', ','), 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(40, 6, number_format( $campo_cuatro->mo_haber, 2, '.', ','), 1, 'L', 0, 0, '', '', true);

            }

            $tab_proceso_retencion_detalle = tab_proceso_retencion_detalle::select( 'administracion.tab_proceso_retencion_detalle.id', 'id_tab_proceso_retencion_factura', 
            'id_tab_retencion_proveedor', 'nu_porcentaje_retencion', 'administracion.tab_proceso_retencion_detalle.mo_retencion', 'de_tipo_retencion', 't02.de_retencion', 'de_inicial', 
            'nu_documento', 'de_proveedor', 'tx_direccion', 'nu_factura', DB::raw(" to_char( fe_emision, 'dd-mm-YYYY') as fe_emision"), 'de_concepto')
            ->join('administracion.tab_retencion_proveedor as t01', 'administracion.tab_proceso_retencion_detalle.id_tab_retencion_proveedor', '=', 't01.id')
            ->join('administracion.tab_retencion as t02', 't01.id_tab_retencion', '=', 't02.id')
            ->join('administracion.tab_proveedor as t03', 't01.id_tab_proveedor', '=', 't03.id')
            ->join('configuracion.tab_documento as t04', 't03.id_tab_documento', '=', 't04.id')
            ->join('administracion.tab_proceso_retencion_factura as t05', 'administracion.tab_proceso_retencion_detalle.id_tab_proceso_retencion_factura', '=', 't05.id')
            ->where('id_tab_proceso_retencion_factura', '=', $campo->id)
            ->get();

            foreach($tab_proceso_retencion_detalle as $key => $campo_dos){

                $pdf->AddPage();

                $pdf->SetFont('','B',11);
                $pdf->MultiCell(190, 5, 'COMPROBANTE DE RETENCIÓN DE '.strtoupper($campo_dos->de_retencion), 0, 'C', 0, 0, '', '', true);
                //$pdf->SetY(35);
        
                $pdf->SetFillColor(209, 209, 209);
                $pdf->setCellPaddings(1, 1, 1, 1);
                $pdf->ln(15);
                $pdf->SetFont('','B',8);
                $pdf->MultiCell(190, 6, 'SUJETO RETENIDO (PROVEEDOR / BENEFICIARIO)', 1, 'C', 1, 0, '', '', true);
                $pdf->ln(6);
                $pdf->SetFont('','',8);
                $pdf->MultiCell(95, 6, 'Proveedor: '.strtoupper($campo_dos->de_proveedor), 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(45, 6, 'R.I.F: '.$campo_dos->de_inicial.'-'.strtoupper($campo_dos->nu_documento), 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(50, 6, 'NIT: ' , 1, 'L', 0, 0, '', '', true);
                $pdf->ln(6);
                $pdf->SetFont('','',8);
                $pdf->MultiCell(190, 6, 'Dirección: '.strtoupper($campo_dos->tx_direccion), 1, 'L', 0, 0, '', '', true);

                $tab_empresa = tab_empresa::select( 'configuracion.tab_empresa.id', 'id_tab_documento', 'nu_documento', 'nb_empresa', 'de_siglas', 'de_direccion', 'de_telefono', 'de_correo', 
                'de_url', 'im_sup_izquierda', 'in_sup_izquierda', 'im_sup_centro', 'in_sup_centro', 
                'im_sup_derecha', 'in_sup_derecha', 'im_inf_izquierda', 'in_inf_izquierda', 
                'im_inf_centro', 'in_inf_centro', 'im_inf_derecha', 'in_inf_derecha', 'de_inicial')
                ->join('configuracion.tab_documento as t02', 'configuracion.tab_empresa.id_tab_documento', '=', 't02.id')
                ->where('configuracion.tab_empresa.id', '=', 1)
                ->first();
        
                $pdf->ln(12);
                $pdf->SetFont('','B',8);
                $pdf->MultiCell(190, 6, 'AGENTE DE RETENCIÓN (EMPRESA)', 1, 'C', 1, 0, '', '', true);
                $pdf->ln(6);
                $pdf->SetFont('','',8);
                $pdf->MultiCell(140, 6, 'Empresa: '.strtoupper($tab_empresa->nb_empresa), 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(50, 6, 'R.I.F.: '.$tab_empresa->de_inicial.'-'.$tab_empresa->nu_documento, 1, 'L', 0, 0, '', '', true);
                $pdf->ln(6);
                $pdf->SetFont('','',8);
                $pdf->MultiCell(190, 6, 'Dirección: '.strtoupper($tab_empresa->de_direccion), 1, 'L', 0, 0, '', '', true);
        
                $pdf->ln(12);
                $pdf->SetFont('','B',8);
                $pdf->MultiCell(190, 6, 'RETENCIÓN (COMPRAS INTERNAS O IMPORTACIONES)', 1, 'C', 1, 0, '', '', true);
                $pdf->ln(6);
                $pdf->SetFont('','',8);
                $pdf->MultiCell(50, 6, 'Nro. '.strtoupper($campo_dos->nu_factura), 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(45, 6, 'Fecha: '.$campo_dos->fe_emision, 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(45, 6, 'Monto: '.number_format($campo->mo_base_imponible, 2, '.', ','), 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(50, 6, 'Concepto: '.strtoupper($campo_dos->de_concepto), 1, 'L', 0, 0, '', '', true);
                $pdf->ln(6);
                $pdf->SetFont('','',8);
                $pdf->MultiCell(50, 6, 'Base Imponible: '.number_format($campo->mo_base_imponible, 2, '.', ','), 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(45, 6, 'IVA: '.number_format($campo->mo_iva, 2, '.', ','), 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(45, 6, 'Monto Exento: '.number_format($campo->mo_base_imponible, 2, '.', ','), 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(50, 6, 'Total a Pagar: '.number_format($campo->mo_total, 2, '.', ','), 1, 'L', 0, 0, '', '', true);
                $pdf->ln(6);
                $pdf->SetFont('','',8);
                $pdf->MultiCell(95, 6, strtoupper($campo_dos->de_retencion), 1, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(95, 6, number_format($campo_dos->mo_retencion, 2, '.', ','), 1, 'R', 0, 0, '', '', true);

            }

        }

        //******FIN CONTENIDO*******//
        $pdf->lastPage();

        //*******CLASE PARA ANEXAR DOCUMENTOS AL REPORTE********//
        //HelperReporte::importarAnexo($pdf, $ruta);

        $directorio = '/App/reporte';
        $disk = Storage::disk('ftp');
        $disk->makeDirectory($directorio);
        $disk->put($directorio.'/'.$ruta.'.pdf', $pdf->output( $ruta.'.pdf', 'S'));

    }

}
