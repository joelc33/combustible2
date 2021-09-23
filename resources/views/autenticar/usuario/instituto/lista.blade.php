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
            var button = $(event.relatedTarget);
            var item_id = button.data('item_id');
            var modal = $(this);
            $("#borrarForm").attr('action','{{ url('/configuracion/usuario/instituto/eliminar').'/'}}'+item_id);
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
            <a class="btn btn-light" href="{{ URL::to('autenticar/usuario/lista') }}">
                <i class="fa fa-arrow-left mr-1"></i> Volver
            </a>
            <div class="block-options">
                <button type="button" class="btn-block-option mr-2"><a href="{{ URL::to('configuracion/usuario/instituto/nuevo').'/'.$id }}"><i class="fa fa-plus mr-1"></i> Nuevo</a></button>
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
                        <th>Institución</th>
                        <th class="text-center" style="width: 100px;">Principal</th>
                        <th class="text-center" style="width: 100px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($tab_usuario_instituto as $key => $value)
                    <tr>
                        <td class="font-w600">{{ $value->de_instituto }}</td>
                        <td class="font-w600">
                        @if ($value->in_principal == true)
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Desmarcar" onclick="location.href='{{ url('/configuracion/usuario/instituto/deshabilitar').'/'. $value->id }}'">
                                <i class="fa fa-toggle-off text-done mr-1"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Marcar Principal" onclick="location.href='{{ url('/configuracion/usuario/instituto/habilitar').'/'. $value->id }}'">
                                <i class="fa fa-toggle-on text-danger mr-1"></i>
                            </button>
                        @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" title="Borrar" data-target="#borrar" data-item_id="{{ $value->id }}" >
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $tab_usuario_instituto->appends(Request::only(['perPage','q']))->render() }}         

        </div>
    </div>
    <!-- END Partial Table -->
</div>
<!-- END Page Content -->

@endsection