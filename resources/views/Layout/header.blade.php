<?php
$user_name=Helper::user_name();
?>
<div class="app-header header-shadow">
    <div class="app-header__logo">
        <div class="logo-src"></div>
       <!--  <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div> -->
    </div>
    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </div>
    <div class="app-header__menu">
        <span>
            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i> 
                </span>
            </button>
        </span>
    </div>
    <div class="app-header__content">
        <div>
            <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>&nbsp; <span class="hamburger-text">Collapse sidebar</span>
            </button>

            <button type="button" class="btn-shadow btn btn-info full_screen" id="full_screen" data-full-screen="0">
                <i class="fa fa-expand" aria-hidden="true"></i> Full screen
            </button>

        </div>
        <div class="app-header-right">
            <div class="header-dots" style="display:none">
                <div class="dropdown">
                    <button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="p-0 mr-2 btn btn-link">
                        <span class="icon-wrapper icon-wrapper-alt rounded-circle">
                            <span class="icon-wrapper-bg"></span>
                            <i class="fa fa-cart-plus"></i>
                            <input type="hidden" id="hidden_cart_count" value="0">
                            <span class="number-notify" id="cart_count">0</span>
                        </span>
                    </button>
                    <button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="p-0 mr-2 btn btn-link">
                        <span class="icon-wrapper icon-wrapper-alt rounded-circle">
                            <span class="icon-wrapper-bg"></span>
                            <i class="fa fa-envelope"></i>
                            <span class="number-notify">2</span>
                        </span>
                    </button>
                    <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu-xl rm-pointers dropdown-menu dropdown-menu-right notification-mobile">
                        <div class="dropdown-menu-header">
                            <div class="dropdown-menu-header-inner bg-plum-plate">
                                <div class="menu-header-image"></div>
                                <div class="menu-header-content text-white">
                                    <h5 class="menu-header-title">List</h5>
                                </div>
                            </div>
                        </div>
                        <div class="grid-menu grid-menu-xl grid-menu-3col">
                            <div class="no-gutters row">
                                <div class="col-sm-12 col-xl-12">
                                    <button class="btn-icon-vertical btn-square btn-transition btn btn-outline-link">
                                        <i class="pe-7s-world icon-gradient bg-night-fade btn-icon-wrapper btn-icon-lg mb-3"></i> Automation
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dropdown">
                    <button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="p-0 mr-2 btn btn-link">
                        <span class="icon-wrapper icon-wrapper-alt rounded-circle">
                            <span class="icon-wrapper-bg"></span>
                            <i class="fa fa-bell" aria-hidden="true"></i>
                            <span class="number-notify">2</span>
                        </span>
                    </button>
                    <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu-xl rm-pointers dropdown-menu dropdown-menu-right notification-mobile">
                        <div class="dropdown-menu-header mb-0">
                            <div class="dropdown-menu-header-inner bg-plum-plate">
                                <div class="menu-header-image"></div>
                                <div class="menu-header-content text-dark">
                                    <h5 class="menu-header-title">Notifications</h5>
                                </div>
                            </div>
                        </div>
                        <div class="grid-menu grid-menu-xl grid-menu-3col">
                            <div class="no-gutters row">
                                <div class="col-sm-12 col-xl-12">
                                    <button class="btn-icon-vertical btn-square btn-transition btn btn-outline-link">
                                        <i class="pe-7s-world icon-gradient bg-night-fade btn-icon-wrapper btn-icon-lg mb-3"></i> Automation
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-btn-lg pr-0">
                <div class="widget-content p-0">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="btn-group">
                                <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
                                    <img width="30" class="rounded-circle" src="{{URL::asset('public/backend/images/default-user.png')}}" alt="">
                                    <i class="fa fa-angle-down ml-2 opacity-8"></i>
                                </a>
                                <div tabindex="-1" role="menu" aria-hidden="true" class="rm-pointers dropdown-menu-lg dropdown-menu dropdown-menu-right logout-panel">
                                    <div class="dropdown-menu-header">
                                        <div class="dropdown-menu-header-inner bg-info">
                                            <div class="menu-header-image opacity-2" style="background-image: url('assets/images/dropdown-header/city3.jpg');"></div>
                                            <div class="menu-header-content text-left">
                                                <div class="widget-content p-0">
                                                    <div class="widget-content-wrapper">
                                                        <div class="widget-content-left mr-3">
                                                            <img width="42" class="rounded-circle" src="{{URL::asset('public/backend/images/default-user.png')}}" alt="">
                                                        </div>
                                                        <div class="widget-content-left">
                                                            <div class="widget-heading">{{$user_name}}</div>
                                                            <!-- <div class="widget-subheading opacity-8">A short profile description</div> -->
                                                        </div>
                                                        <div class="widget-content-right mr-2">
                                                            <a href="{{url('logout')}}" class="btn-pill btn-shadow btn-shine btn btn-focus">Logout</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="dropdown-item" href="<?php echo url('profile'); ?>" role="menuitem"><i class="fa fa-user" aria-hidden="true" style="margin-right: 5px;"></i>  Profile</a>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="widget-content-left  ml-3 header-user-info">
                            <div class="widget-heading"> Admin </div>
                        </div>
                        <div class="widget-content-right header-user-info ml-3">
                            <button type="button" class="btn-shadow p-1 btn btn-primary btn-sm show-toastr-example">
                                <i class="fa text-white fa-calendar pr-1 pl-1"></i>
                            </button>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
