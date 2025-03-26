@extends('backend.admin_after_login')

@section('title', 'Dashboard')

@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa-home metismenu-icon" aria-hidden="true"></i>
                    </div>
                    <div>Dashboard
                    </div>
                </div>
            </div>
        </div>
        <div class="top-widget mb-4">
            <div class="row">
                <div class="col-md-6 col-lg-3 m-mb-3">
                    <a href="{{URL::to('item-management')}}">
                        <div class="widget-chart widget-chart2 text-left card-shadow-primary card green-liner-gradient">
                            <div class="widget-chat-wrapper-outer">
                                <div class="widget-chart-content ">
                                    <div class="row">
                                        <div class="col-lg-8 col-sm-8 col-8 border-right-1 con"> 
                                            <p class="number mb-0">@php echo $total_size; @endphp</p>
                                            <p class="title mb-0">Product</p>
                                        </div>
                                        <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                            <p class="icon mb-0"><i class="fa fa-gift"></i></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3 m-mb-3">
                    <a href="{{URL::to('inventory-management?stock_status=l')}}">
                        <div class="widget-chart widget-chart2 text-left card-shadow-primary card purple-liner-gradient">
                            <div class="widget-chat-wrapper-outer">
                                <div class="widget-chart-content ">
                                    <div class="row">
                                        <div class="col-lg-8 col-sm-8 col-8 border-right-1 con">
                                            <p class="number mb-0">@php echo $stock_alert; @endphp</p>
                                            <p class="title mb-0">Stock Alert</p>
                                        </div>
                                        <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                            <p class="icon mb-0"><i class="fa fa-cube"></i></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3 m-mb-3">
                    <a href="{{URL::to('inventory-management?stock_status=o')}}">
                        <div class="widget-chart widget-chart2 text-left card-shadow-primary card blue-liner-gradient">
                            <div class="widget-chat-wrapper-outer">
                                <div class="widget-chart-content ">
                                    <div class="row">
                                        <div class="col-lg-8 col-sm-8 col-8 border-right-1 con">
                                            <p class="number mb-0">@php echo $out_of_stock; @endphp
                                            </p>
                                            <p class="title mb-0">Out Of Stock</p>
                                        </div>
                                        <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                            <p class="icon mb-0"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3 m-mb-3" style="display:none">
                    <div class="widget-chart widget-chart2 text-left card-shadow-primary card orange-liner-gradient">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content ">
                                <div class="row">
                                    <div class="col-lg-8 col-sm-8 col-8 border-right-1 con">
                                        <p class="number mb-0">1</p>
                                        <p class="title mb-0">Notification</p>
                                    </div>
                                    <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                        <p class="icon mb-0"><i class="fa fa-bell" aria-hidden="true"></i></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="row bottom-widget">
            <div class="col-lg-12">
                <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <a href="{{URL::to('all-user')}}">
                            <div class="widget-chart widget-chart2 text-left mb-3 card-shadow-primary card sky-blue-liner-gradient">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="widget-chart-content ">
                                        <div class="row">
                                            <div class="col-lg-7 col-sm-7 col-7 con">
                                                <p class="title mb-0">Users</p>
                                            </div>
                                            <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                                <p class="icon mb-0"><i class="fa fa-users" aria-hidden="true"></i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <a href="{{URL::to('item-management')}}">
                            <div class="widget-chart widget-chart2 text-left mb-3 card-shadow-primary card dip-blue-liner-gradient">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="widget-chart-content ">
                                        <div class="row">
                                            <div class="col-lg-7 col-sm-7 col-7 con">
                                                <p class="title mb-0">Product</p>
                                            </div>
                                            <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                                <p class="icon mb-0"><i class="fa fa-hdd-o" aria-hidden="true"></i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <a href="{{URL::to('category')}}">
                            <div class="widget-chart widget-chart2 text-left mb-3 card-shadow-primary card black-liner-gradient">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="widget-chart-content ">
                                        <div class="row">
                                            <div class="col-lg-7 col-sm-7 col-7 con">
                                                <p class="title mb-0">Categories</p>
                                            </div>
                                            <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                                <p class="icon mb-0"><i class="fa fa-th" aria-hidden="true"></i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <a href="{{URL::to('sale-order-management')}}">
                            <div class="widget-chart widget-chart2 text-left mb-3 card-shadow-primary card yello-liner-gradient">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="widget-chart-content ">
                                        <div class="row">
                                            <div class="col-lg-7 col-sm-7 col-7 con">
                                                <p class="title mb-0">Orders</p>
                                            </div>
                                            <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                                <p class="icon mb-0"><i class="fa fa-shopping-cart" aria-hidden="true"></i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <a href="{{URL::to('delivery-management')}}">
                            <div class="widget-chart widget-chart2 text-left mb-3 card-shadow-primary card red-liner-gradient">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="widget-chart-content ">
                                        <div class="row">
                                            <div class="col-lg-7 col-sm-7 col-7 con">
                                                <p class="title mb-0">Deliveries</p>
                                            </div>
                                            <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                                <p class="icon mb-0"><i class="fa fa-truck" aria-hidden="true"></i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <a href="{{URl::to('stock')}}">
                            <div class="widget-chart widget-chart2 text-left mb-3 card-shadow-primary card purple-liner-gradient">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="widget-chart-content ">
                                        <div class="row">
                                            <div class="col-lg-7 col-sm-7 col-7 con">
                                                <p class="title mb-0">Stock</p>
                                            </div>
                                            <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                                <p class="icon mb-0"><i class="fa fa-gift"></i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div>
                        <div class="col-md-6 col-lg-4" style="display:none">
                            <div class="widget-chart widget-chart2 text-left mb-3 card-shadow-primary card lite-green-liner-gradient">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="widget-chart-content ">
                                        <div class="row">
                                            <div class="col-lg-7 col-sm-7 col-7 con">
                                                <p class="title mb-0">Transfers</p>
                                            </div>
                                            <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                                <p class="icon mb-0"><i class="fa fa-random" aria-hidden="true"></i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <a href="{{URl::to('supplier-management')}}">
                                <div class="widget-chart widget-chart2 text-left mb-3 card-shadow-primary card dip-orange-liner-gradient">
                                    <div class="widget-chat-wrapper-outer">
                                        <div class="widget-chart-content ">
                                            <div class="row">
                                                <div class="col-lg-7 col-sm-7 col-7 con">
                                                    <p class="title mb-0">Suppliers</p>
                                                </div>
                                                <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                                    <p class="icon mb-0"><i class="fa fa-id-card-o" aria-hidden="true"></i></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <a href="{{URL::to('customer-management')}}">
                            <div class="widget-chart widget-chart2 text-left mb-3 card-shadow-primary card blue-liner-gradient">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="widget-chart-content ">
                                        <div class="row">
                                            <div class="col-lg-7 col-sm-7 col-7 con">
                                                <p class="title mb-0">Customers</p>
                                            </div>
                                            <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                                <p class="icon mb-0"><i class="fa fa-id-badge" aria-hidden="true"></i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div>
                        <div class="col-md-6 col-lg-4" style="display:none">
                            <div class="widget-chart widget-chart2 text-left mb-3 card-shadow-primary card lite-blue-liner-gradient">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="widget-chart-content ">
                                        <div class="row">
                                            <div class="col-lg-7 col-sm-7 col-7 con">
                                                <p class="title mb-0">Receptions</p>
                                            </div>
                                            <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                                <p class="icon mb-0"><i class="fa fa-user" aria-hidden="true"></i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <a href="{{URL::to('returns')}}">
                            <div class="widget-chart widget-chart2 text-left mb-3 card-shadow-primary card dip-purple-liner-gradient">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="widget-chart-content ">
                                        <div class="row">
                                            <div class="col-lg-7 col-sm-7 col-7 con">
                                                <p class="title mb-0">Sales Returns</p>
                                            </div>
                                            <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                                <p class="icon mb-0"><i class="fa fa-cubes" aria-hidden="true"></i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <a href="{{URL::to('purchase-order-return')}}">
                            <div class="widget-chart widget-chart2 text-left mb-3 card-shadow-primary card dip-purple-liner-gradient">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="widget-chart-content ">
                                        <div class="row">
                                            <div class="col-lg-7 col-sm-7 col-7 con">
                                                <p class="title mb-0">Purchase Returns</p>
                                            </div>
                                            <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                                <p class="icon mb-0"><i class="fa fa-cubes" aria-hidden="true"></i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <a href="{{URL::to('reporting-management')}}">
                            <div class="widget-chart widget-chart2 text-left mb-3 card-shadow-primary card black-liner-gradient">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="widget-chart-content ">
                                        <div class="row">
                                            <div class="col-lg-7 col-sm-7 col-7 con">
                                                <p class="title mb-0">Reports</p>
                                            </div>
                                            <div class="col-lg-4 col-sm-4 col-4 con-ico">
                                                <p class="icon mb-0"><i class="fa fa-pie-chart" aria-hidden="true"></i></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
            </div>
            <div class="col-lg-3" style="display:none">
                <div class="card-hover-shadow-2x mb-3 card">
                    <div class="card-header-tab card-header">
                        <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                             Activity
                        </div>
                    </div>
                    <div class="scroll-area-lg">
                        <div class="scrollbar-container">
                            <div class="p-2">
                                <ul class="todo-list-wrapper list-group list-group-flush">
                                   
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="d-block text-right card-footer">
                        <p>&nbsp;</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection