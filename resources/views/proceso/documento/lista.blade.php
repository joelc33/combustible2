@extends('layouts.dashboard')

@section('css_before')
    <!-- Page JS Plugins CSS -->

@endsection

@section('js_after')
    <!-- Page JS Plugins -->

    <!-- Page JS Code -->
    <script>
        $('.pagination').addClass('justify-content-end');
        $('.pagination li').addClass('page-item');
        $('.pagination li a').addClass('page-link');
        $('.pagination span').addClass('page-link');
    </script>

    <script>
        $('#borrar').on('show.bs.modal', function (event) {
            $("#borrarForm").attr('action','{{ url('/proceso/documento/eliminar'). '/'. $id }}');
            var button = $(event.relatedTarget);
            var item_id = button.data('item_id');
            var modal = $(this);
            modal.find('.modal-content #registro_id').val(item_id);
        });
    </script>

@endsection

@section('content')

<!-- Page Content -->
<div class="content content-full content-boxed">
    <!-- Partial Table -->
    <div class="block block-rounded block-bordered">
        <div class="block-header block-header-default">
            <a class="btn btn-light" href="{{ URL::to('proceso/ruta/lista'). '/'. $id }}">
                <i class="fa fa-arrow-left mr-1"></i> Volver
            </a>
            <div class="block-options">
                <button type="button" class="btn-block-option mr-2"><a href="{{ URL::to('proceso/documento/nuevo'). '/'. $ruta }}"><i class="fa fa-plus mr-1"></i> Nuevo</a></button>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="fullscreen_toggle"></button>
            </div>
        </div>
        <div class="block-content bg-body-dark">

            <div class="row items-push">
                @foreach($tab_documento as $key => $value)
                <div class="col-sm-6 col-md-4 col-xl-3 d-flex align-items-center">
                    <!-- Example File -->
                    <div class="options-container w-100">
                        <!-- Example File Block -->
                        <div class="options-item block block-rounded block-transparent mb-0">
                            <div class="block-content text-center">
                                <p class="mb-2 overflow-hidden">
                                @if ($value->mime == 'application/pdf')
                                    <i class="fa fa-fw fa-4x fa-file-pdf text-danger"></i>
                                @elseif ($value->mime == 'image/jpeg')
                                    <i class="fa fa-fw fa-4x fa-file-image text-default"></i>
                                @elseif ($value->mime == 'image/png')
                                    <i class="fa fa-fw fa-4x fa-file-image text-warning"></i>
                                @endif
                                </p>
                                <p class="font-w600 mb-0">
                                    {{ $value->nb_archivo }}
                                </p>
                                <p class="font-size-sm text-muted">
                                    {{ $value->de_documento }}
                                </p>
                            </div>
                        </div>
                        <!-- END Example File Block -->

                        <!-- Example File Hover Options -->
                        <div class="options-overlay rounded-lg bg-white-50">
                            <div class="options-overlay-content">
                                <div class="mb-3">
                                    <a class="btn btn-hero-light" data-toggle="tooltip" title="Ver Documento" onClick="this.href='{{ url('/proceso/documento/ver').'/'. $value->id }}/' + (new Date().getTime());" target="_blank">
                                        <i class="fa fa-eye text-primary mr-1"></i> Ver
                                    </a>
                                </div>
                                <div class="btn-group">
                                    <a class="btn btn-sm btn-light" data-toggle="tooltip" title="Editar" href="{{ url('/proceso/documento/editar').'/'. $value->id }}">
                                        <i class="fa fa-pencil-alt text-black mr-1"></i>
                                    </a>
                                    <a class="btn btn-sm btn-light" data-toggle="modal" title="Borrar" data-target="#borrar" data-item_id="{{ $value->id }}">
                                        <i class="fa fa-trash text-danger mr-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- END Example File Hover Options -->
                    </div>
                    <!-- END Example File -->
                </div>
                @endforeach
            </div>  

        </div>
    </div>
    <!-- END Partial Table -->
</div>
<!-- END Page Content -->

@endsection