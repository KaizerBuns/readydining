@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <section class="panel">
                <header class="panel-heading">
                    Profile
                </header>
                <div class="panel-body">

                   @include('layouts.errors')
                    <form class="form-horizontal" role="form" action="{{ url('/profile/save') }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                          <label class="col-lg-4 col-sm-2 control-label">Name</label>
                          <div class="col-lg-8">
                               <input id="profile-name" class="form-control queue-date" name="profile[name]" value="{{ $profile->name }}" >
                          </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 col-sm-3 control-label">Email</label>
                            <div class="col-lg-8">
                               <input id="profile-email" class="form-control queue-date" name="profile[email]" value="{{ $profile->email }}" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 col-sm-3 control-label">SMS Carrier</label>
                            <div class="col-lg-8">
                                <select class="form-control queue-select" name="profile[sms_provider_id]">
                                	<option value="0">Select one</option>
                                    @foreach($providers as $e)
                                        <option value="{{ $e['id'] }}" {{ $profile->sms_provider_id == $e['id'] ? "selected" : "" }}>{{ $e['name'] }}</option>
                                    @endforeach 
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 col-sm-3 control-label">SMS Number</label>
                            <div class="col-lg-8">
	                             <input id="profile-number" class="form-control" name="profile[sms_number]" value="{{ $profile->sms_number }}" placeholder="10 Digit phone number no spaces">   
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 col-sm-3 control-label">New Password</label>
                            <div class="col-lg-8">
                                 <input id="profile-password" class="form-control" name="profile[password]" value="" placeholder="New Password" type="password">   
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 col-sm-3 control-label">Confirm Password</label>
                            <div class="col-lg-8">
                                 <input id="profile-confirm-password" class="form-control" name="profile[confirm_password]" value="" placeholder="Confirm Password" type="password">   
                            </div>
                        </div>
                        @if(!$user->is_active() && $user->is_carrier_set())
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Verify Code</label>
                            <div class="col-lg-8">
                               <input id="profile-code" class="form-control" name="profile[verify_code]" value="" >
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <div class="col-lg-offset-4 col-lg-8">
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </div>
                  </form>
                </div>
            </section>
        </div>
    </div>
 <script>
    $(function() {
        $(".queue-select").select2({
            theme: "bootstrap",
            width: "resolve"
        });
    });
</script>
@endsection