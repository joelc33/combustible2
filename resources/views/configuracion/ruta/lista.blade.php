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
            $("#borrarForm").attr('action','{{ url('/configuracion/ruta/eliminar') }}');
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
            <a class="btn btn-light" href="{{ URL::to('configuracion/solicitud/lista') }}">
                <i class="fa fa-arrow-left mr-1"></i> Volver
            </a>
            <div class="block-options">
                <button type="button" class="btn-block-option mr-2"><a href="{{ URL::to('configuracion/ruta/nuevo').'/'.$id }}"><i class="fa fa-plus mr-1"></i> Nuevo</a></button>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="fullscreen_toggle"></button>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                    <i class="si si-refresh"></i>
                </button>
            </div>
        </div>
        <div class="block-content">
        
            <table class="table table-bordered table-striped table-vcenter">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" style="width: 100px;">ID</th>
                        <th>Proceso</th>
                        <th>Orden</th>
                        <th>Carga Datos</th>
                        <th class="text-center" style="width: 100px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($tab_ruta as $key => $value)
                    <tr>
                        <td class="font-w600">{{ $value->id }}</td>
                        <td class="font-w600">{{ $value->de_proceso }}</td>
                        <td class="d-none d-sm-table-cell"><em class="text-muted">{{ $value->nu_orden }}</em></td>
                        <td class="font-w600">
                        @if ($value->in_datos == true)
                            Si
                        @else
                            No
                        @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ url('/configuracion/ruta/editar').'/'. $value->id }}">
                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Editar">
                                        <i class="fa fa-pencil-alt"></i>
                                    </button>
                                </a>
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" title="Borrar" data-target="#borrar" data-item_id="{{ $value->id }}" >
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $tab_ruta->appends(Request::only(['perPage','q']))->render() }}         

        </div>
    </div>
    <!-- END Partial Table -->
</div>
<!-- END Page Content -->

@endsection