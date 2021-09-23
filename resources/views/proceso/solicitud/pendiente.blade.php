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
            $("#borrarForm").attr('action','{{ url('/proceso/solicitud/eliminar') }}');
            var button = $(event.relatedTarget);
            var item_id = button.data('item_id');
            var modal = $(this);
            modal.find('.modal-content #registro_id').val(item_id);
        });
        $('#avanzar').on('show.bs.modal', function (event) {
            $("#avanzarForm").attr('action','{{ url('/proceso/solicitud/avanzar') }}');
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
            <a class="btn btn-light" href="{{ URL::to('inicio') }}">
                <i class="fa fa-arrow-left mr-1"></i> Volver
            </a>
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Tareas</li>
                    <li class="breadcrumb-item active" aria-current="page">En Progreso</li>
                </ol>
            </nav>
        </div>            
            <div class="block-options">
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="fullscreen_toggle"></button>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                    <i class="si si-refresh"></i>
                </button>
            </div>
        </div>
        <div class="block-content">

        @if( $errors->has('da_alert_form') )
        <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
            <div class="flex-fill mr-3">
                <p class="mb-0">{{ $errors->first('da_alert_form') }}</p>
            </div>
            <div class="flex-00-auto">
                <i class="fa fa-fw fa-times-circle"></i>
            </div>
        </div>
        @endif
            
        <form action="{{ url('/proceso/solicitud/pendiente') }}" method="get">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <label>
                        <select name="perPage" class="custom-select" value="{{ $perPage }}">
                            @foreach(['5','10','20'] as $page)
                            <option @if($page == $perPage) selected @endif value="{{ $page }}">{{ $page }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="q" name="q" value="{{ $q }}" placeholder="Buscar...">
                        <div class="input-group-append">
                            <button type="submit" class="input-group-text">
                                <i class="fa fa-fw fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        
            <table class="table table-hover table-bordered table-striped table-vcenter">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" style="width: 100px;">Cédula</th>
                        <th class="text-center" style="width: 200px;">Nombre y Apellido</th>
                        <th class="text-center" style="width: 150px;">Municipio</th>
                        <th>Descripción</th>
                        <th class="text-center" style="width: 150px;">Instituto</th>
                        <th>Fecha</th>
                        <th class="text-center" style="width: 100px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($tab_solicitud as $key => $value)
                    <tr>
                        <td class="font-w600">{{ $value->cedula }}</td>
                        <td class="font-w600">{{ $value->nombres.' '.$value->apellidos}}</td>
                        <td class="font-w600">{{ $value->de_municipio }}</td>
                        <td class="font-w600">{{ $value->nu_identificador }}-{{ $value->de_solicitud }}</td>
                        <td class="font-w600">{{ $value->de_instituto }}</td>
                       <!-- <td class="d-none d-sm-table-cell"><em class="text-muted">{{ $value->nb_usuario }}</em></td> -->
                        <td class="font-w600">{{ $value->fe_creado }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Detalle" onclick="location.href='{{ url('/proceso/ruta/lista').'/'. $value->id_ruta}}'">
                                    <i class="fa fa-list-ol"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Avanzar Tramite" onclick="location.href='{{ url('/proceso/ruta/enviar').'/'. $value->id_ruta}}'">
                                    <i class="fa fa-vote-yea"></i>
                                </button>
                                 <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" title="Ver Expediente"onclick="location.href='{{ url('/proceso/ruta/expediente').'/'. $value->id_persona }}'">
                                    <i class="fa fa-book"></i>
                                </button>                               
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $tab_solicitud->appends(Request::only(['perPage','q']))->render() }}         

        </div>
    </div>
    <!-- END Partial Table -->
</div>
<!-- END Page Content -->

@endsection