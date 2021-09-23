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
                    <li class="breadcrumb-item">Registro de Procesos</li>
                    <!--<li class="breadcrumb-item active" aria-current="page">Pendientes</li>-->
                </ol>
            </nav>
        </div>            
            <div class="block-options">
                <button type="button" class="btn-block-option mr-2"><a href="{{ URL::to('telemedicina/persona/nuevo') }}"><i class="fa fa-plus mr-1"></i> Nuevo Paciente</a></button>
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
            
        <form action="{{ url('/consulta/listapaciente') }}" method="get">
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
                        <input type="text" class="form-control" id="q" name="q" value="{{ $q }}" placeholder="Cédula de Identidad...">
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
                        <th class="text-center" style="width: 150px;">Nombre</th>
                        <th>Edad</th>
                        <th>Sexo</th>
                        <th>Telefono</th>
                        <th class="text-center" style="width: 100px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($tab_persona))
                        @foreach($tab_persona as $key => $value)
                            <tr>
                                <td class="font-w600">{{ $value->nombres.' '. $value->apellidos}}</td>
                                <td class="font-w600">{{ $value->edad }}</td>
                                <td class="d-none d-sm-table-cell"><em class="text-muted">{{ $value->de_sexo }}</em></td>
                                <td class="font-w600">{{ $value->telefono }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Nueva Solicitud" onclick="location.href='{{ URL::to('proceso/solicitud/nuevo').'/'. $value->id }}'">
                                            <i class="fa fa-list-ol"></i>
                                        </button>    
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" title="Ver Expediente"onclick="location.href='{{ url('/proceso/ruta/expediente').'/'. $value->id }}'">
                                            <i class="fa fa-book"></i>
                                        </button>                                   
                                    </div>                                    
                                </td>
                            </tr>
                        @endforeach
                @endif
                </tbody>
            </table>
            

        </div>
    </div>
    <!-- END Partial Table -->
</div>
<!-- END Page Content -->

@endsection