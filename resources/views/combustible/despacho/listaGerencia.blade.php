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
            $("#borrarForm").attr('action','{{ url('/combustible/despacho/eliminarGerencia') }}');
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
            <a class="btn btn-light" href="{{ URL::to('/combustible/despacho/registrar') }}">
                <i class="fa fa-arrow-left mr-1"></i> Volver
            </a>
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Lista de Gerencias</li>
                </ol>
            </nav>
        </div>            
            <div class="block-options">
                <button type="button" class="btn-block-option mr-2"><a href="{{ URL::to('combustible/despacho/agregarGerencia').'/'.$id  }}"><i class="fa fa-plus mr-1"></i> Agregar Gerencia</a></button>
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
            
        <form action="{{ url('/combustible/despacho/listaGerencia').'/'.$id }}" method="get">
             <input type="hidden" name="_token" value="{{ csrf_token() }}">
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
                        <input type="text" class="form-control" id="q" name="q" value="{{ $q }}" placeholder="Buscar Gerencia...">
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
                        <th>Gerencia</th>
                        <th>Litros</th>
                        <th class="text-center" style="width: 100px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($tab_gerencia as $key => $value)
                    <tr>
                        <td class="font-w600">{{ $value->de_gerencia}}</td>
                        <td class="font-w600">{{ $value->nu_litro}}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Detalle de Vehiculos" onclick="location.href='{{ url('/combustible/despacho/detalleVehiculo').'/'. $value->id.'/'.$id }}'">
                                    <i class="fa fa-car"></i>
                                </button>                                        
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" title="Quitar Gerencia" data-target="#borrar" data-item_id="{{ $value->id.'-'.$id }}">
                                    <i class="fa fa-times"></i>
                                </button>                                                       
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $tab_gerencia->appends(Request::only(['perPage','q']))->render() }}         

        </div>
    </div>
    <!-- END Partial Table -->
</div>
<!-- END Page Content -->

@endsection