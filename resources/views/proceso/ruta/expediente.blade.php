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
    </script>

@endsection

@section('content')

<!-- Page Content -->
<div class="content content-full content-boxed">
    <!-- Partial Table -->
    <div class="block block-rounded block-bordered">
        <div class="block-header block-header-default">
            <a class="btn btn-light" href="javascript:history.back()">
                <i class="fa fa-arrow-left mr-1"></i> Volver
            </a>
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Expediente del Paciente {{$tab_persona->nombre}}</li>
                </ol>
            </nav>
        </div>              
            
        </div>
        <div class="block-content">
            
        <form action="{{ url('/proceso/solicitud/lista') }}" method="get">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <label>
                        <select name="perPage" class="custom-select" value="{{ $perPage }}">
                            @foreach(['10','20'] as $page)
                            <option @if($page == $perPage) selected @endif value="{{ $page }}">{{ $page }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
               
            </div>
        </form>
        
            <table class="table table-bordered table-striped table-vcenter">
                <thead class="thead-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Tramite</th>
                        <th>Institución</th>
                        <th class="font-w600 text-center">Datos</th>
                        <th class="font-w600 text-center">Documento</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($tab_ruta as $key => $value)
                    <tr>
                    <td class="d-none d-sm-table-cell"><em >{{ $value->fe_creado }}</em></td>
                        <td>{{ $value->de_solicitud }}</td>
                        <td>{{ $value->de_instituto }}</td>
                        <td class="font-w600 text-center">
                        @if ($value->in_reporte == true)
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Ver Documento" onclick="window.open('{{ url('/proceso/reporte/ver').'/'. $value->id }}/' + (new Date().getTime()), '_blank')">
                                <i class="fa fa-print"></i>
                            </button>
                        @endif
                        </td>
                        <td class="font-w600 text-center">
                        @if ($value->in_anexo == true)
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Documentos Anexos" onclick="location.href='{{ url('/proceso/documento/lista/ver').'/'. $value->id }}'">
                                <i class="fa fa-book "></i>
                            </button>
                        @else
                            No
                        @endif
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