<!-- Settings Tab -->
<div class="tab-pane pull-x fade fade-up show active" id="so-settings" role="tabpanel">
    <div class="block mb-0">
        <!-- Color Themes -->
        <!-- Toggle Themes functionality initialized in Template._uiHandleTheme() -->
        <div class="block-content block-content-sm block-content-full bg-body">
            <span class="text-uppercase font-size-sm font-w700">Color Themes</span>
        </div>
        <div class="block-content block-content-full">
            <div class="row gutters-tiny text-center">
                <div class="col-4 mb-1">
                    <a class="d-block py-3 text-white font-size-sm font-w600 bg-default" data-toggle="theme" data-theme="default" href="#">
                        Default
                    </a>
                </div>
                <div class="col-4 mb-1">
                    <a class="d-block py-3 text-white font-size-sm font-w600 bg-xwork" data-toggle="theme" data-theme="{{ asset('assets/css/themes/xwork.min.css') }}" href="#">
                        xWork
                    </a>
                </div>
                <div class="col-4 mb-1">
                    <a class="d-block py-3 text-white font-size-sm font-w600 bg-xmodern" data-toggle="theme" data-theme="{{ asset('assets/css/themes/xmodern.min.css') }}" href="#">
                        xModern
                    </a>
                </div>
                <div class="col-4 mb-1">
                    <a class="d-block py-3 text-white font-size-sm font-w600 bg-xeco" data-toggle="theme" data-theme="{{ asset('assets/css/themes/xeco.min.css') }}" href="#">
                        xEco
                    </a>
                </div>
                <div class="col-4 mb-1">
                    <a class="d-block py-3 text-white font-size-sm font-w600 bg-xsmooth" data-toggle="theme" data-theme="{{ asset('assets/css/themes/xsmooth.min.css') }}" href="#">
                        xSmooth
                    </a>
                </div>
                <div class="col-4 mb-1">
                    <a class="d-block py-3 text-white font-size-sm font-w600 bg-xinspire" data-toggle="theme" data-theme="{{ asset('assets/css/themes/xinspire.min.css') }}" href="#">
                        xInspire
                    </a>
                </div>
                <div class="col-4 mb-1">
                    <a class="d-block py-3 text-white font-size-sm font-w600 bg-xdream" data-toggle="theme" data-theme="{{ asset('assets/css/themes/xdream.min.css') }}" href="#">
                        xDream
                    </a>
                </div>
                <div class="col-4 mb-1">
                    <a class="d-block py-3 text-white font-size-sm font-w600 bg-xpro" data-toggle="theme" data-theme="{{ asset('assets/css/themes/xpro.min.css') }}" href="#">
                        xPro
                    </a>
                </div>
                <div class="col-4 mb-1">
                    <a class="d-block py-3 text-white font-size-sm font-w600 bg-xplay" data-toggle="theme" data-theme="{{ asset('assets/css/themes/xplay.min.css') }}" href="#">
                        xPlay
                    </a>
                </div>
                <div class="col-12">
                    <a class="d-block py-3 bg-body-dark font-w600 text-dark" href="be_ui_color_themes.html">All Color Themes</a>
                </div>
            </div>
        </div>
        <!-- END Color Themes -->

        <!-- Sidebar -->
        <div class="block-content block-content-sm block-content-full bg-body">
            <span class="text-uppercase font-size-sm font-w700">Sidebar</span>
        </div>
        <div class="block-content block-content-full">
            <div class="row gutters-tiny text-center">
                <div class="col-6 mb-1">
                    <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout" data-action="sidebar_style_dark" href="javascript:void(0)">Dark</a>
                </div>
                <div class="col-6 mb-1">
                    <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout" data-action="sidebar_style_light" href="javascript:void(0)">Light</a>
                </div>
            </div>
        </div>
        <!-- END Sidebar -->

        <!-- Header -->
        <div class="block-content block-content-sm block-content-full bg-body">
            <span class="text-uppercase font-size-sm font-w700">Header</span>
        </div>
        <div class="block-content block-content-full">
            <div class="row gutters-tiny text-center mb-2">
                <div class="col-6 mb-1">
                    <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout" data-action="header_style_dark" href="javascript:void(0)">Dark</a>
                </div>
                <div class="col-6 mb-1">
                    <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout" data-action="header_style_light" href="javascript:void(0)">Light</a>
                </div>
                <div class="col-6 mb-1">
                    <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout" data-action="header_mode_fixed" href="javascript:void(0)">Fixed</a>
                </div>
                <div class="col-6 mb-1">
                    <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout" data-action="header_mode_static" href="javascript:void(0)">Static</a>
                </div>
            </div>
        </div>
        <!-- END Header -->

        <!-- Content -->
        <div class="block-content block-content-sm block-content-full bg-body">
            <span class="text-uppercase font-size-sm font-w700">Content</span>
        </div>
        <div class="block-content block-content-full">
            <div class="row gutters-tiny text-center">
                <div class="col-6 mb-1">
                    <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout" data-action="content_layout_boxed" href="javascript:void(0)">Boxed</a>
                </div>
                <div class="col-6 mb-1">
                    <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout" data-action="content_layout_narrow" href="javascript:void(0)">Narrow</a>
                </div>
                <div class="col-12 mb-1">
                    <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout" data-action="content_layout_full_width" href="javascript:void(0)">Full Width</a>
                </div>
            </div>
        </div>
        <!-- END Content -->

        <!-- Layout API -->
        <div class="block-content row justify-content-center border-top">
            <div class="col-9">
                <a class="btn btn-block btn-hero-primary" href="be_layout_api.html">
                    <i class="fa fa-fw fa-flask mr-1"></i> Layout API
                </a>
            </div>
        </div>
        <!-- END Layout API -->
    </div>
</div>
<!-- END Settings Tab -->