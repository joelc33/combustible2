@extends('layouts.auth')

@section('css_before')
    <!-- Page JS Plugins CSS -->

@endsection

@section('js_after')
    <!-- Page JS Plugins -->

    <!-- Page JS Code -->

@endsection

@section('content')

<!-- Page Content -->
<div class="bg-image" style="background-image: url('{{ asset('/assets/media/photos/photo21@2x.jpg') }}');">
    <div class="row no-gutters justify-content-center bg-black-75">
        <div class="hero-static col-sm-8 col-md-6 col-xl-4 d-flex align-items-center p-2 px-sm-0">
            <!-- Reminder Block -->
            <div class="block block-transparent block-rounded w-100 mb-0 overflow-hidden">
                <div class="block-content block-content-full px-lg-5 px-xl-6 py-4 py-md-5 py-lg-6 bg-white">
                    <!-- Header -->
                    <div class="mb-2 text-center">
                        <a class="link-fx font-w700 font-size-h1" href="javascript:void(0)">
                            <span class="text-dark">GOBEL</span><span class="text-primary"> Combustible</span>
                        </a>
                       
                    </div>
                    <!-- END Header -->

                    <!-- Reminder Form -->
                    <!-- jQuery Validation (.js-validation-reminder class is initialized in js/pages/op_auth_reminder.min.js which was auto compiled from _es6/pages/op_auth_reminder.js) -->
                    <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                    <form class="js-validation-reminder" action="{{ url('/ejercicio') }}" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
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

                        <div class="block-content bg-body-light">
                            <div class="row justify-content-center push">
                                <div class="col-md-10">
                                    <button type="submit" class="btn btn-alt-primary">
                                        <i class="fa fa-fw fa-save mr-1"></i> Seleccionar
                                    </button>
                                </div>
                            </div>
                        </div>                      
                    </form>
                    <!-- END Reminder Form -->
                </div>
            </div>
            <!-- END Reminder Block -->
        </div>
    </div>
</div>
<!-- END Page Content -->

@endsection