@php
use \App\Helpers\Helper;
$user_role_access = Helper::get_user_role_access();
$display="";
//print_r($user_role_access);
@endphp
<div class="app-sidebar sidebar-shadow">
    <div class="app-header__logo">
        <div class="logo-src"></div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
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

    <div class="scrollbar-sidebar">
        <div class="app-sidebar__inner">
            <ul class="vertical-nav-menu">
                <li class="app-sidebar__heading" style="margin: 0px;">&nbsp;</li>
                <li>
                    <a href="<?php echo url('dashboard'); ?>" class="{{ (request()->segment(1) == 'dashboard') ? 'mm-active' : '' }}">
                        <i class="fa fa-home metismenu-icon" aria-hidden="true"></i> Dashboards
                    </a>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
    					if(!empty($user_role_access[0]['parent_menu_ids'])) {
    						$parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
    						if(in_array(1, $parent_menu_ids)) {
    							$display = 'style=display:block';
    						}
    					}
    				}
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'all-warehouse') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-building metismenu-icon" aria-hidden="true"></i>
                        Warehouse
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class="">
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(2, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('all-warehouse');?>" class="{{ (request()->segment(1) == 'all-warehouse') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Warehouse Management
                            </a>
                        </li>
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
    					if(!empty($user_role_access[0]['parent_menu_ids'])) {
    						$parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
    						if(in_array(4, $parent_menu_ids)) {
    							$display = 'style=display:block';
    						}
    					}
    				}
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'all-user') || (request()->segment(1) == 'all-inactive-user') || (request()->segment(1) == 'role-access') || (request()->segment(1) == 'user-role') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-user metismenu-icon" aria-hidden="true"></i>
                        User
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class="">
                    	@php
                        if(Session::get('user_role') != "1")
                        {
    		                $display = 'style=display:none';
    		                if(!empty($user_role_access)) {
    							if(!empty($user_role_access[0]['submenu_ids'])) {
    								$submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
    								if(in_array(5, $submenu_ids)) {
    									$display = 'style=display:block';
    								}
    							}
    						}
                        }
		                @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('all-user');?>" class="{{ (request()->segment(1) == 'all-user') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> All User
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
    		                $display = 'style=display:none';
    		                if(!empty($user_role_access)) {
    							if(!empty($user_role_access[0]['submenu_ids'])) {
    								$submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
    								if(in_array(6, $submenu_ids)) {
    									$display = 'style=display:block';
    								}
    							}
    						}
                        }
		                @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('all-inactive-user');?>" class="{{ (request()->segment(1) == 'all-inactive-user') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Inactive User
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
    		                $display = 'style=display:none';
    		                if(!empty($user_role_access)) {
    							if(!empty($user_role_access[0]['submenu_ids'])) {
    								$submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
    								if(in_array(7, $submenu_ids)) {
    									$display = 'style=display:block';
    								}
    							}
    						}
                        }
		                @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('user-role'); ?>" class="{{ (request()->segment(1) == 'user-role') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon" aria-hidden="true"></i> User Role
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
    		                $display = 'style=display:none';
    		                if(!empty($user_role_access)) {
    							if(!empty($user_role_access[0]['submenu_ids'])) {
    								$submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
    								if(in_array(8, $submenu_ids)) {
    									$display = 'display:block';
    								}
    							}
    						}
                        }
		                @endphp
                        <li {{$display}}>
		                    <a href="<?php echo url('role-access'); ?>" class="{{ (request()->segment(1) == 'role-access') ? 'mm-active' : '' }}">
		                        <i class="metismenu-icon" aria-hidden="true"></i> Role Access
		                    </a>
		                </li>
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(9, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'inventory-management') || (request()->segment(1) == 'direct-return-bin') || (request()->segment(1) == 'customer-return-bin') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-list-ol metismenu-icon" aria-hidden="true"></i>
                        Inventory
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class="{{ (request()->segment(1) == 'inventory-management') ? 'mm-active' : '' }}">
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(10, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('inventory-management');?>" class="{{ (request()->segment(1) == 'inventory-management') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Inventory Management
                            </a>
                        </li>
                        
                        <li class="{{ (request()->segment(1) == 'direct-return-bin') || (request()->segment(1) == 'customer-return-bin') ? 'mm-active' : '' }}" >
                            <a href="#">
                                <i class="metismenu-icon"></i> Defective Bin
                                <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                            </a>
                            <ul  >
                                <li>
                                <a href="<?php echo url('customer-return-bin');?>" class="{{ (request()->segment(1) == 'customer-return-bin') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Customer Return bin
                                </a>
                                </li>
                                <li>
                                <a href="<?php echo url('direct-return-bin');?>"  class="{{ (request()->segment(1) == 'direct-return-bin') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Direct Return bin
                                </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(11, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'storage-management') || (request()->segment(1) == 'location-management') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-location-arrow metismenu-icon" aria-hidden="true"></i>
                        Storage Management
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class="">
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(3, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('location-management'); ?>" class="{{ (request()->segment(1) == 'location-management') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> 1. Location Management
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(2, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('storage-management');?>" class="{{ (request()->segment(1) == 'storage-management') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> 2. Storage Management
                            </a>
                        </li>
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    // if(!empty($user_role_access)) {
                    //     if(!empty($user_role_access[0]['parent_menu_ids'])) {
                    //         $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                    //         if(in_array(11, $parent_menu_ids)) {
                    //             $display = 'style=display:block';
                    //         }
                    //     }
                    // }
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'zone-master') || (request()->segment(1) == 'row') || (request()->segment(2) == 'rack') || (request()->segment(2) == 'level') || (request()->segment(2) == 'position') ? 'mm-active' : '' }}" {{$display}} style="display:none">
                    <a href="#">
                        <i class="fa fa-location-arrow metismenu-icon" aria-hidden="true"></i>
                        Storage Management
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class="{{ (request()->segment(1) == 'location-management') ? 'mm-active' : '' }}">
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(12, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('zone-master');?>" class="{{ (request()->segment(1) == 'zone-master') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Zone Master
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(13, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('row');?>" class="{{ (request()->segment(1) == 'row') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Row
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(14, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('config/rack');?>" class="{{ (request()->segment(2) == 'rack') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Rack
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(15, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('config/level');?>" class="{{ (request()->segment(2) == 'level') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Level
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(16, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('config/position');?>" class="{{ (request()->segment(2) == 'position') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Position
                            </a>
                        </li>
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(17, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'supplier-management') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-address-book metismenu-icon" aria-hidden="true"></i>
                        Supplier
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class=""> <!-- mm-show -->
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(18, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('supplier-management'); ?>" class="{{ (request()->segment(1) == 'supplier-management') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Supplier Management
                            </a>
                        </li>
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(19, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'customer-management') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-address-card metismenu-icon" aria-hidden="true"></i>
                        Customer 
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class=""> <!-- mm-show -->
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(20, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('customer-management'); ?>" class="{{ (request()->segment(1) == 'customer-management') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Customer Management
                            </a>
                        </li>
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(21, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'sale-order-management') || (request()->segment(1) == 'picking-slip') || (request()->segment(1) == 'no-stock-order') || (request()->segment(1) == 'save-order') || (request()->segment(1) == 'approve-sale-order-management') || (request()->segment(1) == 'print-merged-invoice') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-cart-plus metismenu-icon" aria-hidden="true"></i>
                        Sale Order  
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class=""> <!-- mm-show -->
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(22, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('sale-order-management'); ?>" class="{{ (request()->segment(1) == 'sale-order-management') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Order Management
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(97, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('approve-sale-order-management'); ?>" class="{{ (request()->segment(1) == 'approve-sale-order-management') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Approve Order
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(23, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('picking-slip'); ?>" class="{{ (request()->segment(1) == 'picking-slip') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Picking Slip
                            </a>
                        </li>
                        
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(24, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('no-stock-order'); ?>" class="{{ (request()->segment(1) == 'no-stock-order') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> No Stock Order
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(87, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('save-order'); ?>" class="{{ (request()->segment(1) == 'save-order') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Save Order
                            </a>
                        </li>
                        
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(50, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'packing') || (request()->segment(1) == 'shipping') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-gift metismenu-icon" aria-hidden="true"></i>
                        Packing and Shipping
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class=""> <!-- mm-show -->
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(51, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('packing'); ?>" class="{{ (request()->segment(1) == 'packing') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> 1. Packing
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(52, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('shipping'); ?>" class="{{ (request()->segment(1) == 'shipping') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> 2. Shipping
                            </a>
                        </li>
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(53, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'delivery-management') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-truck metismenu-icon" aria-hidden="true"></i>
                        Delivery  
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class=""> <!-- mm-show -->
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(54, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('delivery-management');?>" class="{{ (request()->segment(1) == 'delivery-management') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> 3. Delivery Management
                            </a>
                        </li>
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(25, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'purchase-order-management') || (request()->segment(1) == 'order-request') || (request()->segment(1) == 'quotation-order') || (request()->segment(1) == 'order-confirmation') || (request()->segment(1) == 'performa-invoice') || (request()->segment(1) == 'save-order-request') || (request()->segment(1) == 'save-purchase-order')  || (request()->segment(1) == 'excess-purchase-order') || (request()->segment(1) == 'damage-purchase-order') || (request()->segment(1) == 'shortage-purchase-order') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-cart-plus metismenu-icon" aria-hidden="true"></i>
                        Purchase Order  
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class=""> <!-- mm-show -->
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(26, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('re-order-view'); ?>" class="{{ (request()->segment(1) == 're-order-view') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Re-Order
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(26, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('order-request'); ?>" class="{{ (request()->segment(1) == 'order-request') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Order Request
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(89, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('save-order-request'); ?>" class="{{ (request()->segment(1) == 'save-order-request') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Save Order Request
                            </a>
                        </li>
                        <!-- <li {{$display}}>
                            <a href="<?php //echo url('quotation-order'); ?>" class="{{ (request()->segment(1) == 'quotation-order') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Order Quotation
                            </a>
                        </li>
                        <li {{$display}}>
                            <a href="<?php //echo url('order-confirmation'); ?>" class="{{ (request()->segment(1) == 'order-confirmation') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Order Confirmation
                            </a>
                        </li>
                        <li {{$display}}>
                            <a href="<?php //echo url('performa-invoice'); ?>" class="{{ (request()->segment(1) == 'performa-invoice') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Performa Invoice
                            </a>
                        </li> -->
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(27, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('purchase-order-management'); ?>" class="{{ (request()->segment(1) == 'purchase-order-management') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Purchase Order Management
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(88, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('save-purchase-order'); ?>" class="{{ (request()->segment(1) == 'save-purchase-order') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Save Purchase Order
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(92, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('excess-purchase-order'); ?>" class="{{ (request()->segment(1) == 'excess-purchase-order') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Excesss Order
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(93, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('damage-purchase-order'); ?>" class="{{ (request()->segment(1) == 'damage-purchase-order') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Damage Order
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(88, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('shortage-purchase-order'); ?>" class="{{ (request()->segment(1) == 'shortage-purchase-order') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Shortage Order
                            </a>
                        </li>
                        
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(43, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'consignment-receipt') || (request()->segment(1) == 'binning-task') || (request()->segment(1) == 'gate-entry') || (request()->segment(1) == 'check-in') || (request()->segment(1) == 'binning-location') || (request()->segment(1) == 'create-bining-advice') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-exchange metismenu-icon" aria-hidden="true"></i>
                        Receiving and Putaway
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class=""> <!-- mm-show -->
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(44, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('gate-entry');?>" class="{{ (request()->segment(1) == 'gate-entry') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> 1. Gate Entry
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(45, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('consignment-receipt');?>" class="{{ (request()->segment(1) == 'consignment-receipt') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> 2. Consignment Receipt
                            </a>
                        </li>
                        <!-- <li {{$display}}>
                            <a href="<?php //echo url('create-bining-advice');?>" class="{{ (request()->segment(1) == 'create-bining-advice') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Create Bining Advice
                            </a>
                        </li> -->
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(95, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('create-bining-advice');?>" class="{{ (request()->segment(1) == 'create-bining-advice') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> 3. Bining Advice
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(46, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('check-in');?>" class="{{ (request()->segment(1) == 'check-in') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> 4. Check In
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(48, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('binning-location');?>" class="{{ (request()->segment(1) == 'binning-location') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> 5. Binning Location
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(49, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('binning-task');?>" class="{{ (request()->segment(1) == 'binning-task') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> 6. Binning Task
                            </a>
                        </li>
                        
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(49, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('barcode-list');?>" class="{{ (request()->segment(1) == 'barcode-list') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> 7. Barcode
                            </a>
                        </li>
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(28, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'item-management') || (request()->segment(1) == 'item-search') || (request()->segment(1) == 'category') || (request()->segment(1) == 'car-model') || (request()->segment(1) == 'group') || (request()->segment(1) == 'subcategory') || (request()->segment(1) == 'car-manufacture') || (request()->segment(1) == 'car-name') || (request()->segment(1) == 'part-brand') || (request()->segment(1) == 'part-name') || (request()->segment(1) == 'engine')? 'mm-active' : '' }}" {{$display}}> <!-- mm-active -->
                    <a href="#">
                        <i class="fa fa-certificate metismenu-icon" aria-hidden="true"></i>
                        Item  
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class=""> <!-- mm-show -->
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(29, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('item-management');?>" class="{{ (request()->segment(1) == 'item-management') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Item Management
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(30, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('item-search');?>" class="{{ (request()->segment(1) == 'item-search') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Item Search
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(31, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('category');?>" class="{{ (request()->segment(1) == 'category') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Category
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(31, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('subcategory');?>" class="{{ (request()->segment(1) == 'subcategory') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Subcategory
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(33, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li style="display:none">
                            <a href="<?php echo url('oem-no');?>" class="{{ (request()->segment(1) == 'oem-no') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> OEM No.
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(34, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li>
                            <a href="<?php echo url('group');?>" class="{{ (request()->segment(1) == 'group') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Group
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(35, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li>
                            <a href="<?php echo url('car-manufacture');?>" class="{{ (request()->segment(1) == 'car-manufacture') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Car Manufacture
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1") {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(36, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('car-model');?>" class="{{ (request()->segment(1) == 'car-model') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Car Model
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(37, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('car-name');?>" class="{{ (request()->segment(1) == 'car-name') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Car Name
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(38, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li>
                            <a href="<?php echo url('part-brand');?>" class="{{ (request()->segment(1) == 'part-brand') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Part Brand
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(39, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li>
                            <a href="<?php echo url('part-name');?>" class="{{ (request()->segment(1) == 'part-name') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Part Name
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(40, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li style="display: none">
                            <a href="<?php echo url('engine');?>" class="{{ (request()->segment(1) == 'engine') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Engine
                            </a>
                        </li>
                    </ul>
                </li>
                
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(28, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'sale-order-outstanding') || (request()->segment(1) == 'sale-order-partial-outstanding') || (request()->segment(1) == 'sale-order-receipt') || (request()->segment(1) == 'purchase-order-outstanding') || (request()->segment(1) == 'purchase-partial-outstanding') || (request()->segment(1) == 'purchase-receipt') || (request()->segment(1) == 'sale-order-sales-report') || (request()->segment(1) == 'purchase-order-purchase-report') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-suitcase metismenu-icon" aria-hidden="true"></i>
                        Accounting
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class="{{ (request()->segment(1) == 'inventory-management') ? 'mm-active' : '' }}">
                        
                        <li class="{{ (request()->segment(1) == 'sale-order-outstanding') || (request()->segment(1) == 'sale-order-partial-outstanding') || (request()->segment(1) == 'sale-order-receipt') || (request()->segment(1) == 'sale-order-sales-report') ? 'mm-active' : '' }}" >
                            <a href="#">
                                <i class="metismenu-icon"></i> Sales
                                <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                            </a>
                            <ul  >
                                <li>
                                    <a href="<?php echo url('sale-order-outstanding'); ?>" class="{{ (request()->segment(1) == 'sale-order-outstanding') ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon"></i> Outstanding
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo url('sale-order-partial-outstanding'); ?>" class="{{ (request()->segment(1) == 'sale-order-partial-outstanding') ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon"></i> Partial Outstanding
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo url('sale-order-receipt'); ?>" class="{{ (request()->segment(1) == 'sale-order-receipt') ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon"></i> Receipt
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo url('sale-order-sales-report'); ?>" class="{{ (request()->segment(1) == 'sale-order-sales-report') ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon"></i> Sales Report
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="{{ (request()->segment(1) == 'purchase-order-outstanding') || (request()->segment(1) == 'purchase-partial-outstanding') || (request()->segment(1) == 'purchase-receipt') || (request()->segment(1) == 'purchase-order-purchase-report') ? 'mm-active' : '' }}" >
                            <a href="#">
                                <i class="metismenu-icon"></i> Purchase
                                <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                            </a>
                            <ul  >
                                <li>
                                    <a href="<?php echo url('purchase-order-outstanding'); ?>" class="{{ (request()->segment(1) == 'purchase-order-outstanding') ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon"></i> Outstanding
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo url('purchase-partial-outstanding'); ?>" class="{{ (request()->segment(1) == 'purchase-partial-outstanding') ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon"></i> Partial Outstanding
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo url('purchase-receipt'); ?>" class="{{ (request()->segment(1) == 'purchase-receipt') ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon"></i> Receipt
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo url('purchase-order-purchase-report'); ?>" class="{{ (request()->segment(1) == 'purchase-order-purchase-report') ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon"></i> Purchase Report
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                 @php
                 if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(41, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <!-- Phase -3  -->
                <li class="{{ (request()->segment(1) == 'reporting-management') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-bar-chart metismenu-icon" aria-hidden="true"></i>
                        Reporting   
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class=""> <!-- mm-show -->
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(42, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('reporting-management'); ?>" class="{{ (request()->segment(1) == 'reporting-management') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Reporting Management
                            </a>
                        </li>
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(55, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'returns') || (request()->segment(1) == 'purchase-order-return') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-retweet metismenu-icon" aria-hidden="true"></i>
                        Returns 
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class=""> <!-- mm-show -->
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(56, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('returns');?>" class="{{ (request()->segment(1) == 'returns') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Sales Returns
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(47, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('purchase-order-return');?>" class="{{ (request()->segment(1) == 'purchase-order-return') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Purchase Return
                            </a>
                        </li>
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(57, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'config-currency') || (request()->segment(1) == 'config-exchange-rate') || (request()->segment(1) == 'config-class') || (request()->segment(1) == 'cities') || (request()->segment(1) == 'countries') || (request()->segment(1) == 'payment') || (request()->segment(1) == 'functional-area') || (request()->segment(1) == 'unit')|| (request()->segment(1) == 'unit-load') || (request()->segment(1) == 'lots') || (request()->segment(1) == 'product-rate') || (request()->segment(1) == 'product-tax') || (request()->segment(1) == 'state') || (request()->segment(2) == 'delivery-method') || (request()->segment(2) == 'sms-api-key') || (request()->segment(2) == 'mail-config') || (request()->segment(2) == 'transport-mode') || (request()->segment(2) == 'courier-company') || (request()->segment(2) == 'additional-charges') || (request()->segment(2) == 'expenses') || (request()->segment(2) == 'vat-type') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-cogs metismenu-icon" aria-hidden="true"></i>
                        Config 
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class=""> <!-- mm-show -->
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(58, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('config-currency');?>" class="{{ (request()->segment(1) == 'config-currency') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Currency
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(59, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('config-exchange-rate');?>" class="{{ (request()->segment(1) == 'config-exchange-rate') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Exchange Rate
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(60, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li style="display:none">
                            <a href="<?php echo url('config-class');?>" class="{{ (request()->segment(1) == 'config-class') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Class
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(61, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li style="display:none">
                            <a href="<?php echo url('countries');?>" class="{{ (request()->segment(1) == 'countries') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Countries
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(62, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li style="display:none">
                            <a href="<?php echo url('state');?>" class="{{ (request()->segment(1) == 'state') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> States
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(63, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li style="display:none">
                            <a href="<?php echo url('cities');?>" class="{{ (request()->segment(1) == 'cities') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Cities
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(64, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                         <li {{$display}}>
                            <a href="<?php echo url('payment');?>" class="{{ (request()->segment(1) == 'payment') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Payment
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(65, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('functional-area');?>" class="{{ (request()->segment(1) == 'functional-area') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Functional Area
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(66, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('unit');?>" class="{{ (request()->segment(1) == 'unit') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Unit
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(67, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('unit-load');?>" class="{{ (request()->segment(1) == 'unit-load') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Unit Load
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(68, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('lots');?>" class="{{ (request()->segment(1) == 'lots') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Lots
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(69, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('product-rate');?>" class="{{ (request()->segment(1) == 'product-rate') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Product Rate
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(70, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('product-tax');?>" class="{{ (request()->segment(1) == 'product-tax') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Product tax
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(71, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('config/delivery-method');?>" class="{{ (request()->segment(2) == 'delivery-method') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Delivery Method
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(72, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li style="display:none">
                            <a href="<?php echo url('config/sms-api-key');?>" class="{{ (request()->segment(2) == 'sms-api-key') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> SMS API Key
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(73, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li style="display:none">
                            <a href="<?php echo url('config/mail-config');?>" class="{{ (request()->segment(2) == 'mail-config') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Mail Config
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(74, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('config/transport-mode');?>" class="{{ (request()->segment(2) == 'transport-mode') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Transport Mode
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(75, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('config/courier-company');?>" class="{{ (request()->segment(2) == 'courier-company') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Courier Company
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(90, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li style="display:none">
                            <a href="<?php echo url('config/additional-charges');?>" class="{{ (request()->segment(2) == 'additional-charges') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Additional Charges
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(91, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('config/expenses');?>" class="{{ (request()->segment(2) == 'expenses') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Expenses
                            </a>
                        </li>
                        <li {{$display}}>
                            <a href="<?php echo url('config/vat-type');?>" class="{{ (request()->segment(2) == 'vat-type') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> VAT Type
                            </a>
                        </li>
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(76, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <li class="{{ (request()->segment(1) == 'stock') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-cubes metismenu-icon" aria-hidden="true"></i>
                        Stock 
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class="">
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(77, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('stock');?>" class="{{ (request()->segment(1) == 'stock') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Stock Management
                            </a>
                        </li>
                    </ul>
                </li>
                @php
                if(Session::get('user_role') != "1")
                {
                    $display = 'style=display:none';
                    if(!empty($user_role_access)) {
                        if(!empty($user_role_access[0]['parent_menu_ids'])) {
                            $parent_menu_ids = json_decode($user_role_access[0]['parent_menu_ids']);
                            if(in_array(78, $parent_menu_ids)) {
                                $display = 'style=display:block';
                            }
                        }
                    }
                }
                @endphp
                <li class="{{ (request()->segment(2) == 'inventory-report') || (request()->segment(2) == 'supplier-report') || (request()->segment(2) == 'customer-report') || (request()->segment(2) == 'sales-order-report') || (request()->segment(2) == 'purchase-order-report') || (request()->segment(2) == 'receiving-put-away-report') || (request()->segment(2) == 'returns-management-report') || (request()->segment(2) == 'stock-management-report') || (request()->segment(2) == 'sales-order-outstanding-report') || (request()->segment(2) == 'sales-order-ageing-report') || (request()->segment(2) == 'sales-order-invoice-report') ? 'mm-active' : '' }}" {{$display}}>
                    <a href="#">
                        <i class="fa fa-download metismenu-icon" aria-hidden="true"></i>
                        Reports 
                        <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                    </a>
                    <ul class="">
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(79, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('report/inventory-report');?>" class="{{ (request()->segment(2) == 'inventory-report') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Inventory Report
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(80, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('report/supplier-report');?>" class="{{ (request()->segment(2) == 'supplier-report') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Supplier Report
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(81, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('report/customer-report');?>" class="{{ (request()->segment(2) == 'customer-report') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Customer Report
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(82, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <!--<li {{$display}}>-->
                        <!--    <a href="<?php //echo url('report/sales-order-report');?>" class="{{ (request()->segment(2) == 'sales-order-report') ? 'mm-active' : '' }}">-->
                        <!--        <i class="metismenu-icon"></i> Sales Order Report-->
                        <!--    </a>-->
                        <!--</li>-->
                        <li class="{{ (request()->segment(2) == 'sales-order-report') || (request()->segment(2) == 'sales-order-outstanding-report') || (request()->segment(2) == 'sales-order-ageing-report') || (request()->segment(2) == 'sales-order-invoice-report') ? 'mm-active' : '' }}" >
                            <a href="#">
                                <i class="metismenu-icon"></i> Sales Order Report
                                <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                            </a>
                            <ul  >
                                <li>
                                    <a href="<?php echo url('report/sales-order-report');?>" class="{{ (request()->segment(2) == 'sales-order-report') ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon"></i> Order Report
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo url('report/sales-order-outstanding-report');?>"  class="{{ (request()->segment(2) == 'sales-order-outstanding-report') ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon"></i> Outstanding Report
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo url('report/sales-order-ageing-report');?>"  class="{{ (request()->segment(2) == 'sales-order-ageing-report') ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon"></i> Ageing Report
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo url('report/sales-order-invoice-report');?>"  class="{{ (request()->segment(2) == 'sales-order-invoice-report') ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon"></i> Invoice Report
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(83, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li class="{{ (request()->segment(2) == 'purchase-order-report') || (request()->segment(2) == 'purchase-order-outstanding-report') || (request()->segment(2) == 'purchase-order-ageing-report') || (request()->segment(2) == 'purchase-order-invoice-report') ? 'mm-active' : '' }}" {{$display}}>
                            <a href="#">
                                <i class="metismenu-icon"></i> Purchase Order Report
                                <i class="metismenu-state-icon fa fa-chevron-down caret-left"></i>
                            </a>
                            <ul  >
                                <li>
                                    <a href="<?php echo url('report/purchase-order-report');?>" class="{{ (request()->segment(2) == 'purchase-order-report') ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon"></i> Order Report
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo url('report/purchase-order-outstanding-report');?>" class="{{ (request()->segment(2) == 'purchase-order-outstanding-report') ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon"></i> Outstanding Report
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo url('report/purchase-order-ageing-report');?>" class="{{ (request()->segment(2) == 'purchase-order-ageing-report') ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon"></i> Ageing Report
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo url('report/purchase-order-invoice-report');?>" class="{{ (request()->segment(2) == 'purchase-order-invoice-report') ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon"></i> Invoice Report
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(84, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('report/receiving-put-away-report');?>" class="{{ (request()->segment(2) == 'receiving-put-away-report') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Receiving & Put Away Report
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(85, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('report/returns-management-report');?>" class="{{ (request()->segment(2) == 'returns-management-report') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Returns Management Report
                            </a>
                        </li>
                        @php
                        if(Session::get('user_role') != "1")
                        {
                            $display = 'style=display:none';
                            if(!empty($user_role_access)) {
                                if(!empty($user_role_access[0]['submenu_ids'])) {
                                    $submenu_ids = json_decode($user_role_access[0]['submenu_ids']);
                                    if(in_array(86, $submenu_ids)) {
                                        $display = 'display:block';
                                    }
                                }
                            }
                        }
                        @endphp
                        <li {{$display}}>
                            <a href="<?php echo url('report/stock-management-report');?>" class="{{ (request()->segment(2) == 'stock-management-report') ? 'mm-active' : '' }}">
                                <i class="metismenu-icon"></i> Stock Management Report
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>