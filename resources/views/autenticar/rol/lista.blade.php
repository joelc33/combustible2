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
            $("#borrarForm").attr('action','{{ url('/autenticar/rol/eliminar') }}');
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
            <div class="block-options">
                <button type="button" class="btn-block-option mr-2"><a href="{{ URL::to('autenticar/rol/nuevo') }}"><i class="fa fa-plus mr-1"></i> Nuevo</a></button>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="fullscreen_toggle"></button>
                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                    <i class="si si-refresh"></i>
                </button>
            </div>
        </div>
        <div class="block-content">
            
        <form action="{{ url('/autenticar/rol/lista') }}" method="get">
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
        
            <table class="table table-bordered table-striped table-vcenter">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" style="width: 100px;">ID</th>
                        <th>Rol</th>
                        <th class="d-none d-md-table-cell text-center" style="width: 100px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($tab_rol as $key => $value)
                    <tr>
                        <td class="font-w600">{{ $value->id }}</td>
                        <td class="d-none d-sm-table-cell"><em class="text-muted">{{ $value->de_rol }}</em></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ url('/autenticar/rol/editar').'/'. $value->id }}">
                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Editar">
                                        <i class="fa fa-pencil-alt"></i>
                                    </button>
                                </a>
                                @if ($value->in_estatus == true)
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Deshabilitar" onclick="location.href='{{ url('/autenticar/rol/deshabilitar').'/'. $value->id }}'">
                                    <i class="fa fa-toggle-off text-done mr-1"></i>
                                </button>
                                @else
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Habilitar" onclick="location.href='{{ url('/autenticar/rol/habilitar').'/'. $value->id }}'">
                                    <i class="fa fa-toggle-on text-danger mr-1"></i>
                                </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Asignar Menu" onclick="location.href='{{ url('/autenticar/rol/menu').'/'. $value->id }}'">
                                    <i class="fa fa-indent"></i>
                                </button>                                
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $tab_rol->appends(Request::only(['perPage','q']))->render() }}         

        </div>
    </div>
    <!-- END Partial Table -->
</div>
<!-- END Page Content -->

@endsection