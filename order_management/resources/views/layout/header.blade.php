<?php
$model = new App\Clients;
$user_details = user_details($model);
?>
<div class="horizontal-menu">
        <nav class="navbar top-navbar col-lg-12 col-12 p-0">
          <div class="container">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
              <a class="navbar-brand brand-logo" href="{{URL::to('/dashboard')}}">
                <h6>Order Management</h6>
              </a>
              <a class="navbar-brand brand-logo-mini" href="index.html"><img src="{{URL::asset('public/backend/images/logo-mini.svg')}}" alt="logo" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
              <!-- <ul class="navbar-nav mr-lg-2">
                <li class="nav-item nav-search d-none d-lg-block">
                  <div class="input-group">
                    <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                      <span class="input-group-text" id="search">
                        <i class="mdi mdi-magnify"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control" id="navbar-search-input" placeholder="Search" aria-label="search" aria-describedby="search" />
                  </div>
                </li>
              </ul> -->
              <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item nav-profile dropdown">
                  <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                    <div class="nav-profile-img">
                      <img src="{{URL::asset('public/backend/images/faces/face21.jpg')}}" alt="image" />
                    </div>
                    <div class="nav-profile-text">
                      <p class="text-black font-weight-semibold m-0"> {{$user_details[0]['customer_name']}}</p>
                      <span class="font-13 online-color">online <i class="mdi mdi-chevron-down"></i></span>
                    </div>
                  </a>
                  <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                    <!-- <a class="dropdown-item" href="#">
                      <i class="mdi mdi-cached mr-2 text-success"></i> Activity Log </a> -->
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{URL::to('logout')}}">
                      <i class="mdi mdi-logout mr-2 text-primary"></i> Signout </a>
                  </div>
                </li>
              </ul>
              <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="horizontal-menu-toggle">
                <span class="mdi mdi-menu"></span>
              </button>
            </div>
          </div>
        </nav>
        <nav class="bottom-navbar">
          <div class="container">
            <ul class="nav page-navigation">
              @if(empty(Session::get('order_status')))
              <li class="nav-item">
                <a class="nav-link" href="<?php echo url('dashboard');?>">
                  <i class="mdi mdi-compass-outline menu-icon"></i>
                  <span class="menu-title">Dashboard</span>
                </a>
              </li>
              @endif
              <!-- <li class="nav-item">
                <a class="nav-link" href="<?php //echo url('item');?>">
                  <i class="mdi mdi-magnify"></i>&nbsp;
                  <span class="menu-title">Item</span>
                </a>
              </li> -->
              <li class="nav-item">
                <a class="nav-link" href="<?php echo url('item-list');?>">
                  <i class="mdi mdi-cube-send"></i>&nbsp;
                  <span class="menu-title">Items/Parts List</span>
                </a>
              </li>
              @if(empty(Session::get('order_status')))
              <li class="nav-item">
                <a class="nav-link" href="<?php echo url('item-search');?>">
                  <i class="mdi mdi-magnify"></i>&nbsp;
                  <span class="menu-title">Item Search</span>
                </a>
              </li>
              @endif
              <li class="nav-item">
                <a class="nav-link" href="<?php echo url('order');?>">
                  <i class="mdi mdi-calculator"></i>&nbsp;
                  <span class="menu-title">Orders</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo url('cart');?>">
                  <i class="mdi mdi-cart"></i>&nbsp;
                  <span class="menu-title">Cart</span>
                </a>
              </li>
              <!-- <li class="nav-item">
                <a class="nav-link" href="<?php //echo url('clients');?>">
                  <i class="mdi mdi-account"></i>&nbsp;
                  <span class="menu-title">Clients</span>
                </a>
              </li> -->
              @if(empty(Session::get('order_status')))
              <li class="nav-item">
                <a class="nav-link" href="<?php echo url('profile');?>">
                  <i class="mdi mdi-settings"></i>&nbsp;
                  <span class="menu-title">Profile</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo url('logout');?>">
                  <i class="mdi mdi-arrow-right-bold-circle-outline"></i>&nbsp;
                  <span class="menu-title">Logout</span>
                </a>
              </li>
              @endif
            </ul>
          </div>
        </nav>
      </div>