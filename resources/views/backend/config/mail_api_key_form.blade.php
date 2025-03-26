{{ Form::open(array('id'=>'configMailApiKeyForm')) }}
    <div class="form-row">
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>SMTP Username *</label>
                @php
                $smtp_user = "";
                if(!empty($mail_data[0]['smtp_user']))  {
                    $smtp_user = $mail_data[0]['smtp_user'];
                }
                $hidden_id = "";
                if(!empty($mail_data[0]['id']))  {
                    $hidden_id = $mail_data[0]['id'];
                }
                @endphp
                <input name="smtp_user" id="smtp_user" placeholder="" type="text" class="form-control" value="{{$smtp_user}}">
                 <input name="hidden_id" type="hidden" value="{{$hidden_id}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>SMTP Password *</label>
                @php
                $smtp_pass = "";
                if(!empty($mail_data[0]['smtp_pass']))  {
                    $smtp_pass = $mail_data[0]['smtp_pass'];
                }
                @endphp
                <input name="smtp_pass" id="smtp_pass" placeholder="" type="text" class="form-control" value="{{$smtp_pass}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>SMTP Host Name *</label>
                @php
                $smtp_host = "";
                if(!empty($mail_data[0]['smtp_host']))  {
                    $smtp_host = $mail_data[0]['smtp_host'];
                }
                @endphp
                <input name="smtp_host" id="smtp_host" placeholder="" type="text" class="form-control" value="{{$smtp_host}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>SMTP Port *</label>
                @php
                $smtp_port = "";
                if(!empty($mail_data[0]['smtp_port']))  {
                    $smtp_port = $mail_data[0]['smtp_port'];
                }
                @endphp
                <input name="smtp_port" id="smtp_port" placeholder="" type="number" class="form-control" value="{{$smtp_port}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>From Mail *</label>
                @php
                $from_mail = "";
                if(!empty($mail_data[0]['from_mail']))  {
                    $from_mail = $mail_data[0]['from_mail'];
                }
                @endphp
                <input name="from_mail" id="from_mail" placeholder="" type="email" class="form-control" value="{{$from_mail}}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="position-relative form-group">
                <label>From Name *</label>
                @php
                $from_name = "";
                if(!empty($mail_data[0]['from_name']))  {
                    $from_name = $mail_data[0]['from_name'];
                }
                @endphp
                <input name="from_name" id="from_name" placeholder="" type="text" class="form-control" value="{{$from_name}}">
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" name="submit" class="btn-shadow btn btn-info" value="Submit"> Save </button> <button type="button" class="btn-shadow btn btn-cancel" data-dismiss="modal"> Close </button>
    </p>
{{ Form::close() }}