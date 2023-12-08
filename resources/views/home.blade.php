@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">

        <section class="panel">
            <header class="panel-heading">
                  Dashboard
            </header>
            <div class="panel-body table-responsive">
                 @include('layouts.errors')
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Party Size</th>
                            <th>Last Checked</th>
                            <th>Availability</th>
                            <th>Alert Sent</th>
                            <th>Actions</th>
                        </tr>
                        <? foreach((array)$queues as $q) {?>
                            <tr>
                                <td><?=$q->id?></td>
                                <td title="{{ $q->entity_id }}"><?=$q->name?></td>
                                <td><input type="text" data-id="{{ $q->id }}" class="queue-description" value="{{ $q->description }}" placeholder="Add a description"></td>
                                <td><?=$q->date?></td>
                                <td><?=date("h:i a", strtotime("2016-01-01 {$q->time}"))?></td>
                                <td><?=$q->size?></td>
                                <td><?=date("F d, Y h:i a", strtotime($q->updated_at))?></td>
                                <td>
                                    <?if($q->availability == 'pending'){?>
                                    <span class="label label-warning">Pending</span>
                                    <? } else if ($q->error == 1){ ?>
                                        <span class="label label-danger">{{ $q->availability }}</span>  
                                     <? } else { ?>
                                        <span class="label label-success">{{ $q->availability }}</span>
                                    <?}?>   
                                </td>
                                <td>
                                    <?if($q->alert == 0){?>
                                    <span class="label label-info">No</span>
                                    <? }else{ ?>
                                        <span class="label label-success" title="<?=date("F d, Y h:i a", strtotime($q->alerted_at))?>">Yes</span>
                                    <?}?>   
                                </td>
                                <td><a href="javascript:void(0)" onclick="reset_queue({{$q->id}})">Reset</a> | <a href="javascript:void(0)" onclick="delete_queue({{$q->id}})">Delete</a></td>
                            </tr>
                        <?}?>
                    </tbody>
                </table>
            </div>
    </section>
</div><!--end col-6 -->
<script>


    var delete_queue = function(id) {
        bootbox.confirm('Are you sure you want to DELETE this request?', function(result) { if (result===true) {  
            window.location = "/queue/delete/?id=" + id;
        }});    
    }
    var reset_queue = function(id) {
        bootbox.confirm('Are you sure you want to RESET this request?', function(result) { if (result===true) {  
            window.location = "/queue/reset/?id=" + id;
        }});    
    }

    $(document).ready(function(){

        var token = '{!! csrf_token() !!}';

        $('.queue-description').change(function(){
            $.ajax({ 
                type: 'POST',
                url: '/queue/update',
                data: { 
                    'id' : $(this).attr('data-id'),
                    'description' : $(this).val(),
                    '_token' : token
                },
                async: false
            });

        });
    })
</script>
@endsection
