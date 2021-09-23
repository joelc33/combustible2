<!-- Pop In Block Modal -->
<div class="modal fade" id="modal-block-popin" tabindex="-1" role="dialog" aria-labelledby="modal-block-popin" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popin" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">Aviso</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <p>¿Desea salir del sistema?</p>
                </div>
                <div class="block-content block-content-full text-right bg-light">
                    {{--<button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">SI</button>--}}
                    <a href="{{ url('/autenticar') }}" class="btn btn-sm btn-primary">SI</a>
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END Pop In Block Modal -->

<!-- Bootstrap Toasts -->
<div style="position: fixed; top: 3rem; right: 3rem; z-index: 9999999;">
    <!-- Toast Aviso -->
    <div id="toast-aviso" class="toast fade hide" data-delay="4000" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="fa fa-info-circle text-success mr-2"></i>
            <strong class="mr-auto">Aviso</strong>
            <small class="text-muted">Justo Ahora</small>
            <button type="button" class="ml-2 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            {{ session('msg_side_overlay') }}
        </div>
    </div>
    <!-- END Toast Aviso -->
</div>
<!-- END Bootstrap Toasts -->

<!-- Pop Out Block Modal -->
<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="borrar" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
            <form action="#" method="post" id="borrarForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title" id="borrar">Aviso</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <p>¿Estás seguro de que quieres eliminar este registro?</p>
                </div>
                <input type="hidden" name="id" id="registro_id" value="">
                <div class="block-content block-content-full text-right bg-light">
                    <button type="submit" class="btn btn-sm btn-primary">Si</button>
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">No</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="borrarRetencion" tabindex="-1" role="dialog" aria-labelledby="borrarRetencion" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
            <form action="#" method="post" id="borrarRetencionForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title" id="borrarRetencion">Aviso</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <p>¿Estás seguro de que quieres eliminar este registro?</p>
                </div>
                <input type="hidden" name="id" id="registro_id" value="">
                <div class="block-content block-content-full text-right bg-light">
                    <button type="submit" class="btn btn-sm btn-primary">Si</button>
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">No</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

<!-- Pop Out Block Modal -->
<div class="modal fade" id="avanzar" tabindex="-1" role="dialog" aria-labelledby="avanzar" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
            <form action="#" method="post" id="avanzarForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title" id="avanzar">Aviso</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <p>¿Realmente desea avanzar el Proceso?</p>
                </div>
                <input type="hidden" name="id" id="registro_id" value="">
                <div class="block-content block-content-full text-right bg-light">
                    <button type="submit" class="btn btn-sm btn-primary">Si</button>
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">No</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rechazar" tabindex="-1" role="dialog" aria-labelledby="rechazar" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
            <form action="#" method="post" id="rechazarForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title" id="rechazar">Aviso</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <p>¿Realmente desea Rechazar el Proceso?</p>
                </div>
                <input type="hidden" name="id" id="registro_id" value="">
                <div class="block-content block-content-full text-right bg-light">
                    <button type="submit" class="btn btn-sm btn-primary">Si</button>
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">No</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="generar" tabindex="-1" role="dialog" aria-labelledby="borrar" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
            <form action="#" method="post" id="generarForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title" id="borrar">Aviso</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <p>¿Estás seguro que desea generar presupuesto? Nota: No podra hacer mas mdificaciones</p>
                </div>
                <input type="hidden" name="id" id="registro_id" value="">
                <div class="block-content block-content-full text-right bg-light">
                    <button type="submit" class="btn btn-sm btn-primary">Si</button>
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">No</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="generarCreditoAdicional" tabindex="-1" role="dialog" aria-labelledby="generarCreditoAdicional" aria-hidden="true">
    <div class="modal-dialog modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
            <form action="#" method="post" id="generarCreditoAdicionalForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title" id="borrar">Aviso</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <p>¿Estás seguro que desea generar el credito adicional?</p>
                    <p>Nota: No podra hacer mas mdificaciones</p>
                </div>
                <input type="hidden" name="id" id="id_tab_credito_adicional" value="">
                <div class="block-content block-content-full text-right bg-light">
                    <button type="submit" class="btn btn-sm btn-primary">Si</button>
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">No</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
<!-- END Pop Out Block Modal -->

@yield('modal_after')