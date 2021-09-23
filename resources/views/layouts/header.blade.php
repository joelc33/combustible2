<!-- Header -->
<header id="page-header">
    <!-- Header Content -->
    <div class="content-header">
        <!-- Left Section -->
        <div>
            <!-- Toggle Sidebar -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout()-->
            <button type="button" class="btn btn-dual mr-1" data-toggle="layout" data-action="sidebar_toggle">
                <i class="fa fa-fw fa-bars"></i>
            </button>
            <!-- END Toggle Sidebar -->

            <!-- Website Dropdown -->
            {{--<div class="dropdown d-inline-block">
                <button type="button" class="btn btn-dual" id="page-header-website-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-fw fa-home"></i>
                    <span class="d-none d-sm-inline ml-1">Sitio Web</span>
                </button>
                <div class="dropdown-menu p-0" aria-labelledby="page-header-website-dropdown">
                    <div class="p-2">
                        <a class="dropdown-item mb-0" href="{{ URL::to('/') }}" target="_blank">
                            <i class="fa fa-fw fa-globe mr-1"></i> Ver Sitio
                        </a>
                    </div>
                </div>
            </div>--}}
            <!-- END Website Dropdown -->
            {{--
            <!-- Open Search Section -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <button type="button" class="btn btn-dual" data-toggle="layout" data-action="header_search_on">
                <i class="fa fa-fw fa-search"></i> <span class="ml-1 d-none d-sm-inline-block">Buscar</span>
            </button>
            <!-- END Open Search Section -->
            --}}
        </div>
        <!-- END Left Section -->

        <!-- Right Section -->
        <div>

            <!-- Ejercicio -->
           <!-- <div class="dropdown d-inline-block">
                <button type="button" class="btn btn-dual" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-fw fa-hospital"></i>
                    {!! Session::get('nb_instituto') !!} / <i class="fa fa-fw fa-user-md"></i>{!! Session::get('nb_especialidad') !!}
                </button>
                <div class="dropdown-menu dropdown-menu-right p-0">
                    <div class="p-2">
                        <a class="btn btn-light btn-block text-center" href="{{ url('/ejercicio') }}">
                            <i class="fa fa-fw fa-undo-alt mr-1"></i> Cambiar
                        </a>
                    </div>
                </div>
            </div>-->
            <!-- END Notifications Dropdown -->

            <!-- User Dropdown -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn btn-dual" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-fw fa-user d-sm-none"></i>
                    <span class="d-none d-sm-inline-block"><i class="far fa-fw fa-user-circle mr-1"></i> {{ Auth::user()->nb_usuario }}</span>
                    <i class="fa fa-fw fa-angle-down ml-1 d-none d-sm-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right p-0" aria-labelledby="page-header-user-dropdown">
                    <div class="bg-primary-darker rounded-top font-w600 text-white text-center p-3">
                        Opciones
                    </div>
                    <div class="p-2">
{{--
                        <a class="dropdown-item" href="be_pages_generic_profile.html">
                            <i class="far fa-fw fa-user mr-1"></i> Profile
                        </a>
                        <a class="dropdown-item d-flex align-items-center justify-content-between" href="be_pages_generic_inbox.html">
                            <span><i class="far fa-fw fa-envelope mr-1"></i> Inbox</span>
                            <span class="badge badge-primary badge-pill">3</span>
                        </a>
                        <a class="dropdown-item" href="be_pages_generic_invoice.html">
                            <i class="far fa-fw fa-file-alt mr-1"></i> Invoices
                        </a>
                        <div role="separator" class="dropdown-divider"></div>
--}}
                        <!-- Toggle Side Overlay -->
                        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="layout" data-action="side_overlay_toggle">
                            <i class="far fa-fw fa-user mr-1"></i> Perfil
                        </a>
                        <!-- END Side Overlay -->

                        <div role="separator" class="dropdown-divider"></div>
                        <a class="dropdown-item push" href="javascript:void(0)" data-toggle="modal" data-target="#modal-block-popin">
                            <i class="far fa-fw fa-arrow-alt-circle-left mr-1"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
            <!-- END User Dropdown -->

            <!-- Notifications Dropdown -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn btn-dual" id="page-header-notifications-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-fw fa-bell"></i>
                    <span class="badge badge-secondary badge-pill"><div  id="notificacion_total">0</div></span>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0" aria-labelledby="page-header-notifications-dropdown">
                    <div class="bg-primary-darker rounded-top font-w600 text-white text-center p-3">
                        Notificaciones
                    </div>
                    <ul class="nav-items my-2" id="notificacion_lista">
                    </ul>
                    <div class="p-2 border-top">
                        <a class="btn btn-light btn-block text-center" href="javascript:void(0)">
                            <i class="fa fa-fw fa-eye mr-1"></i> Ver todo
                        </a>
                    </div>
                </div>
            </div>
            <!-- END Notifications Dropdown -->

            @section('js_notificacion_lista')
                <script>

                $.get("{{ URL::to('inicio/notificacion') }}", { }, function(data){
                    $("#notificacion_total").html(data.total);
                    $.each(data.data, function(i,f) {
                        $("#notificacion_lista").append(
                        '<li>'+ 
                            '<a class="text-dark media py-2" href="javascript:void(0)">'+ 
                                '<div class="mx-3">'+ 
                                    '<i class="fa fa-fw ' + f.de_icono + '"></i>'+ 
                                '</div>'+ 
                                '<div class="media-body font-size-sm pr-2">'+ 
                                    '<div class="font-w600">' + f.de_notificacion + '</div>'+ 
                                    '<div class="text-muted font-italic">' + f.TimeAgo + '</div>'+ 
                                '</div>'+ 
                            '</a>'+ 
                        '</li>'
                        );
                    });
                });

                </script>
            @endsection

            <!-- Toggle Side Overlay -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <button type="button" class="btn btn-dual" data-toggle="layout" data-action="side_overlay_toggle">
                <i class="far fa-fw fa-list-alt"></i>
            </button>
            <!-- END Toggle Side Overlay -->
        </div>
        <!-- END Right Section -->
    </div>
    <!-- END Header Content -->

    <!-- Header Search -->
    <div id="page-header-search" class="overlay-header bg-primary">
        <div class="content-header">
            <form class="w-100" action="{{ url('/inicio') }}" method="POST">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                        <button type="button" class="btn btn-primary" data-toggle="layout" data-action="header_search_off">
                            <i class="fa fa-fw fa-times-circle"></i>
                        </button>
                    </div>
                    <input type="text" class="form-control border-0" placeholder="Buscar..." id="page-header-search-input" name="page-header-search-input">
                </div>
            </form>
        </div>
    </div>
    <!-- END Header Search -->

    <!-- Header Loader -->
    <!-- Please check out the Loaders page under Components category to see examples of showing/hiding it -->
    <div id="page-header-loader" class="overlay-header bg-primary-darker">
        <div class="content-header">
            <div class="w-100 text-center">
                <i class="fa fa-fw fa-2x fa-sun fa-spin text-white"></i>
            </div>
        </div>
    </div>
    <!-- END Header Loader -->
</header>
<!-- END Header -->