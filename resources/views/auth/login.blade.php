@extends('layouts.auth')

@section('css_before')
    <!-- Page JS Plugins CSS -->

@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ secure_asset('/assets/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <!-- Page JS Code -->
    <script src="{{ secure_asset('/assets/js/pages/op_auth_signin.min.js') }}"></script>
@endsection

@section('content')

<!-- Page Content -->
<div class="bg-image" style="background-image: url('{{ secure_asset('/assets/media/photos/photo22@2x.jpg') }}');">
    <div class="row no-gutters bg-primary-op">
        <div class="hero-static col-md-6 d-flex align-items-center bg-white">
            <div class="p-3 w-100">
                <div class="mb-3 text-center">
                    <a class="link-fx font-w700 font-size-h1" href="{{ url('/') }}">
                        <span class="text-dark">GOBEL</span><span class="text-primary"> Combustible</span>
                    </a>
                    <p class="text-uppercase font-w700 font-size-sm text-muted">Iniciar Sesión</p>
                </div>
                <div class="row no-gutters justify-content-center">
                    <div class="col-sm-8 col-xl-6">

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

                    @if( $errors->has('da_mensaje') )
                        <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
                            <div class="flex-fill mr-3">
                                <p class="mb-0">{{ $errors->first('da_mensaje') }}</p>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('msg'))
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <div class="flex-00-auto">
                                <i class="fa fa-fw fa-check"></i>
                            </div>
                            <div class="flex-fill ml-3">
                                <p class="mb-0">{{ session('msg') }}</p>
                            </div>
                        </div>
                    @endif
                    
                        <form class="js-validation-signin" action="{{ url('/autenticar') }}" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="py-3">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-lg form-control-alt {!! $errors->has('usuario') ? 'is-invalid' : '' !!}" {!! $errors->has('usuario') ? 'aria-describedby="usuario-error" aria-invalid="true"' : '' !!} id="usuario" name="usuario" placeholder="Usuario" value="{{ old('usuario') }}">
                                    @if( $errors->has('usuario') )
                                        <div id="usuario-error" class="invalid-feedback animated fadeIn">{{ $errors->first('usuario') }}</div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-lg form-control-alt {!! $errors->has('contraseña') ? 'is-invalid' : '' !!}" {!! $errors->has('contraseña') ? 'aria-describedby="contraseña-error" aria-invalid="true"' : '' !!} id="contraseña" name="contraseña" placeholder="Contraseña" value="{{ old('contraseña') }}">
                                    @if( $errors->has('contraseña') )
                                        <div id="contraseña-error" class="invalid-feedback animated fadeIn">{{ $errors->first('contraseña') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-block btn-hero-lg btn-hero-primary">
                                    <i class="fa fa-fw fa-sign-in-alt mr-1"></i> Ingresar
                                </button>
                                <p class="mt-3 mb-0 d-lg-flex justify-content-lg-between">
                                    <a class="btn btn-sm btn-light d-block d-lg-inline-block mb-1" href="{{ url('/recuperar') }}">
                                        <i class="fa fa-exclamation-triangle text-muted mr-1"></i> ¿Olvidó su Contraseña?
                                    </a>
                                    {{--<a class="btn btn-sm btn-light d-block d-lg-inline-block mb-1" href="{{ url('/registrar') }}">
                                        <i class="fa fa-plus text-muted mr-1"></i> Registrarse
                                    </a>--}}
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-static col-md-6 d-none d-md-flex align-items-md-center justify-content-md-center text-md-center">
            <div class="p-3">
                <p class="display-4 font-w700 text-white mb-3">
                    G<span class="text-dark">obe</span>l
                </p>
                <p class="font-size-lg font-w600 text-white-75 mb-0">
                    Copyright &copy; <span data-toggle="year-copy"></span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- END Page Content -->
@endsection