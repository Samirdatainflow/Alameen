{{ Form::open(array('id'=>'userRoleAccessForm')) }}
@php
$hidden_id = "";
@endphp
@if(!empty($role_access_data))
    @if($role_access_data[0]['role_access_id'])
        @php
        $hidden_id = $role_access_data[0]['role_access_id']
        @endphp
    @endif
@endif
<input type="hidden" name="hidden_id" value="{{$hidden_id}}">
<div class="card mb-2">
    <div data-parent="#accordion" id="collapseOne" aria-labelledby="headingOne" class="collapse show role_access" style="height: 500px;overflow-y: scroll;">
        <div class="card-body">
            <div class="form-row">
                <div class="col-md-12">
                    <div class="position-relative form-group">
                        <div class="row">
                            <select name="warehouse_id" class="form-control">
                                <option value="">Select Warehouse</option>
                                @if(!empty($warehouse_data))
                                    @foreach($warehouse_data as $data)
                                        @php $sel = ""; @endphp
                                        @if(!empty($role_access_data))
                                            @if($role_access_data[0]['warehouse_id'] == $data['warehouse_id'])
                                                @php $sel = 'selected="selected"'; @endphp
                                            @endif
                                        @endif
                                        <option value="{{$data['warehouse_id']}}" {{$sel}}>{{$data['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div><br/>
                        <div class="row">
                            <select name="fk_role_id" class="form-control">
                                <option value="">Select Role</option>
                                @if(!empty($user_role_data))
                                    @foreach($user_role_data as $rdata)
                                        @php $sel = ""; @endphp
                                        @if(!empty($role_access_data))
                                            @if($role_access_data[0]['fk_role_id'] == $rdata['user_role_id'])
                                                @php $sel = 'selected="selected"'; @endphp
                                            @endif
                                        @endif
                                        <option value="{{$rdata['user_role_id']}}" {{$sel}}>{{$rdata['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <br/>
                        <div class="row">
                            <ul class="vertical-nav-menu metismenu">
                                @if(!empty($menu_data))
                                    @foreach($menu_data as $menu)
                                        @if($menu['name'] != 'Warehouse')
                                        <li class="mm-active">
                                            <a href="javascript:void(0)" aria-expanded="true">
                                                <div class="position-relative form-check">
                                                    @php
                                                    $checked = "";
                                                    @endphp
                                                    @if(!empty($role_access_data))
                                                        @if(!empty($role_access_data[0]['parent_menu_id']))
                                                            @php $parent_menu_ids = json_decode($role_access_data[0]['parent_menu_id']); @endphp
                                                            @if(in_array($menu['menu_id'], $parent_menu_ids))
                                                                @php $checked = 'checked="checked"'; @endphp
                                                            @endif
                                                        @endif
                                                    @endif
                                                    <input name="menu[]" id="menu{{$menu['menu_id']}}" type="checkbox" value="{{$menu['menu_id']}}" {{$checked}} class="parent_menu">
                                                    <label for="exampleCheck" class="form-check-label">{{$menu['name']}}</label>
                                                </div>
                                            </a>
                                            @if(!empty($menu['submenu_data']))
                                                @foreach($menu['submenu_data'] as $submenu)
                                                <ul class="mm-collapse mm-show">
                                                    <li>
                                                        <a href="javascript:void(0)" class="">
                                                            <i class="metismenu-icon"></i>
                                                            <div class="position-relative form-check">
                                                                @php
                                                                $checked = "";
                                                                if(!empty($role_access_data)) {
                                                                    if(!empty($role_access_data[0]['submenu_id'])) {
                                                                        $submenu_ids = json_decode($role_access_data[0]['submenu_id']);
                                                                        if(in_array($submenu['menu_id'], $submenu_ids)) $checked = 'checked="checked"';
                                                                    }
                                                                }
                                                                @endphp
                                                                <input name="submenu[]" id="submenu{{$submenu['menu_id']}}" type="checkbox" data-parent="{{$menu['menu_id']}}" class="submenu" value="{{$submenu['menu_id']}}" {{$checked}}>
                                                                <label for="exampleCheck" class="form-check-label">{{$submenu['name']}}</label>
                                                            </div>
                                                        </a>
                                                    </li>
                                                </ul>
                                                @endforeach
                                            @endif
                                        </li>
                                        @endif
                                    @endforeach
                                @endif
                            </ul>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><br>
<p class="text-right"><button type="submit" id="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button></p>
{{ Form::close() }}