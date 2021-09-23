@extends('layouts.dashboard')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('assets/js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script>

        jQuery(function(){ Dashmix.helpers([ 'flatpickr', 'select2']); });

    </script>    
    <!-- Page JS Code -->
@endsection

@section('content')

<!-- Page Content -->
<div class="content content-full content-boxed">
    <!-- Partial Table -->
    <div class="block block-rounded block-bordered">
    <!-- New Post -->
    <form action="{{ URL::to('combustible/despacho/guardarDespachoGerencia') }}" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="id_tab_despacho" id="id_tab_despacho" value="{{$id}}">
        <div class="block">
            <div class="block-header block-header-default">
                <a class="btn btn-light" href="javascript:history.back()">
                    <i class="fa fa-arrow-left mr-1"></i> Volver
                </a>
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Agregar</li>
                    <li class="breadcrumb-item active" aria-current="page">Gerencia</li>
                </ol>
            </nav>
        </div>                
                {{--<div class="block-options">
                    <div class="custom-control custom-switch custom-control-success">
                        <input type="checkbox" class="custom-control-input" id="dm-post-edit-active" name="dm-post-edit-active" checked>
                        <label class="custom-control-label" for="dm-post-edit-active">Set post as active</label>
                    </div>
                </div>--}}
            </div>
            <div class="block-content">
                <div class="row justify-content-center push">
                    <div class="col-md-10">

                        {{--
                        @if (count($errors) > 0)
                            <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
                                <div class="flex-fill mr-3">
                                    <p class="mb-0">Hay problemas con su validacion!</p>
                                </div>
                                <div class="flex-00-auto">
                                    <i class="fa fa-fw fa-times-circle"></i>
                                </div>
                            </div>
                        @endif
                        --}}
                        @if( $errors->has('da_alert_form') )
                        <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
                            <div class="flex-fill mr-3">
                                <p class="mb-0">{{ $errors->first('da_alert_form') }}</p>
                            </div>
                        </div>
                        @endif
                       
                        <div class="form-group form-row"> 
                          <div id="resultado"></div>
                        </div>
                        
                        <div class="form-group form-row"> 

                            <div class="col-12">
                                <label for="gerencia">Gerencia</label>
                                <select class="custom-select {!! $errors->has('gerencia') ? 'is-invalid' : '' !!}" name="gerencia" id="gerencia" {{ $errors->has('gerencia') ? 'aria-describedby="gerencia-error" aria-invalid="true"' : '' }}>
                                    <option value="0" >Seleccione...</option>
                                    @foreach($tab_gerencia as $gerencia)
                                        <option value="{{ $gerencia->id }}" {{ $gerencia->id == old('gerencia') ? 'selected' : '' }}>{{ $gerencia->de_gerencia }}</option>
                                    @endforeach
                                </select>
                                @if( $errors->has('gerencia') )
                                    <div id="gerencia-error" class="invalid-feedback animated fadeIn">{{ $errors->first('gerencia') }}</div>
                                @endif
                            </div>  
                        </div>   
                        <div class="form-group form-row"> 

                            <div class="col-3">
                                <label for="litro">Litros por Vehículo</label>
                                <div class="input-group">
                                    <input type="text" class="form-control {!! $errors->has('litro') ? 'is-invalid' : '' !!}" id="litro" name="litro" placeholder="Cantidad..." value="{{ old('litro') }}" {{ $errors->has('litro') ? 'aria-describedby="litro-error" aria-invalid="true"' : '' }}>
<!--                                    <div class="input-group-append">
                                        <span class="input-group-text btn-hapus">
                                            <i class="fa fa-search"></i>
                                        </span>
                                    </div>-->
                                    @if( $errors->has('litro') )
                                        <div id="litro-error" class="invalid-feedback animated fadeIn">{{ $errors->first('litro') }}</div>
                                    @endif
                                </div>
                            </div>

                                          
                         </div>                           
                    </div>
                </div>
            </div>
        </div>
        <div class="block-content bg-body-light">
            <div class="row justify-content-center push">
                <div class="col-md-10">
                    <button type="submit" class="btn btn-alt-primary">
                        <i class="fa fa-fw fa-save mr-1"></i> Guardar
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