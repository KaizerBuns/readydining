@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                  Users
            </header>
            <div class="panel-body table-responsive">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>SMS</th>
                            <th>Provider</th>
                            <th>Created</th>
                            <th>Last Activity</th>
                            <th>Actions</th>
                        </tr>
                        <? foreach($users as $u) {?>
                            <tr>
                                <td><?=$u->id?></td>
                                <td><?=$u->email?></td>
                                <td><?=$u->sms_number?></td>
                                <td><?=$u->sms_provider?></td>
                                <td><?=date("h:i a", strtotime("2016-01-01 {$u->created_at}"))?></td>
                                <td><?=date("F d, Y H:i a", strtotime($u->updated_at))?></td>
                                <td><a href="javascript:void(0)" onclick="login_as({{$u->id}})">Login As</a></td>
                            </tr>
                        <?}?>
                    </tbody>
                </table>
            </div>
    </section>
</div><!--end col-6 -->
<script>
    var login_as = function(id) {
        bootbox.confirm('Are you sure you want to Login as this user?', function(result) { if (result===true) {  
            window.location = "/user/loginas/?id=" + id;
        }});    
    }
</script>
@endsection
