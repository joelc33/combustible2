@extends('layouts.dashboard')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" id="css-main" href="{{ asset('css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <!-- Page JS Code -->

    <script>
        jQuery(function(){ Dashmix.helpers([ 'flatpickr', 'select2']); });
    </script>
    
<script type="text/javascript">
$(function () {
             

    });
</script>    
    
@endsection

@section('content')

<!-- Page Content -->
<div class="content content-full content-boxed">
    <!-- Partial Table -->
    <div class="block block-rounded block-bordered">
    <!-- New Post -->
    <form action="{{ URL::to('reporte/imprimirmorbilidad')}}"  method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="block">
            <div class="block-header block-header-default">
               
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Reporte de Morbilidad Diaria</li>
                </ol>
            </nav>
        </div> 
            </div>
            <div class="block-content">
                <div class="row justify-content-center push">
                    <div class="col-md-10">

                     
                        <div class="form-group form-row">
                            <div class="col-5">
                                <label for="fecha">Fecha Desde</label>
                                <input type="text" class="js-flatpickr form-control {!! $errors->has('fecha') ? 'is-invalid' : '' !!}" id="fecha_desde" name="fecha_desde" placeholder="d-m-Y" data-date-format="Y-m-d" data-enable-time="true" >
                               
                            </div>

                            <div class="col-5">
                                <label for="fecha">Fecha Hasta</label>
                                <input type="text" class="js-flatpickr form-control {!! $errors->has('fecha') ? 'is-invalid' : '' !!}" id="fecha_hasta" name="fecha_hasta" placeholder="d-m-Y" data-date-format="Y-m-d" data-enable-time="true" >
                               
                            </div>
                           
                        </div>   

                         <div class="form-group form-row">                           
                                  <label for="cedula">Institución</label>
                            </div>
                            <div class="form-group form-row">
                                <select class="custom-select {!! $errors->has('instituto') ? 'is-invalid' : '' !!}" name="instituto" id="instituto" {{ $errors->has('instituto') ? 'aria-describedby="instituto-error" aria-invalid="true"' : '' }}>
                                    <option value="" >Seleccione...</option>
                                    @foreach($tab_instituto as $instituto)
                                        <option value="{{ $instituto->id }}" {{ $instituto->id == old('instituto') ? 'selected' : '' }}>{{ $instituto->de_instituto}}</option>
                                    @endforeach
                                </select>
                                @if( $errors->has('instituto') )
                                    <div id="instituto-error" class="invalid-feedback animated fadeIn">{{ $errors->first('instituto') }}</div>
                                @endif
                        </div>
                         <div class="form-group form-row">
                                 <label for="cedula">Especialidad</label>
                        </div>      
                        <div class="form-group form-row">
                                <select class="custom-select {!! $errors->has('especialidad') ? 'is-invalid' : '' !!}" name="especialidad" id="especialidad" {{ $errors->has('especialidad') ? 'aria-describedby="especialidad-error" aria-invalid="true"' : '' }}>
                                    <option value="" >Seleccione...</option>
                                    @foreach($tab_especialidad as $especialidad)
                                        <option value="{{ $especialidad->id }}" {{ $especialidad->id == old('especialidad') ? 'selected' : '' }}>{{ $especialidad->de_especialidad}}</option>
                                    @endforeach
                                </select>
                                @if( $errors->has('especialidad') )
                                    <div id="especialidad-error" class="invalid-feedback animated fadeIn">{{ $errors->first('especialidad') }}</div>
                                @endif
                        </div>  

                    </div>
                </div>
            </div>
        <div class="block-content bg-body-light">
            <div class="row justify-content-center push">
                <div class="col-md-10">
                    <button type="submit" name='pdf' value="pdf" class="btn btn-sm btn-outline-warning btn-rounded px-3 mr-1 my-1"">
                        <i class="fa fa-fw fa-print mr-1"></i> Impirmir PDF
                    </button>
                </div>
            </div>
        </div>
    </form>
    </div>
    <!-- END New Post -->
</div>
<!-- END Page Content -->

@endsection