@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <section class="panel">
                <header class="panel-heading">
                    Request
                </header>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" action="{{ url('/queue/save') }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                          <label class="col-lg-3 col-sm-3 control-label">Event</label>
                          <div class="col-lg-9">
                                <select class="form-control queue-select" name="queue[entity_id]">
                                    @foreach($events as $i => $e)
                                        <option value="{{ $i }}">{{ $e['name'] }}</option>
                                    @endforeach 
                                </select>
                          </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Date</label>
                            <div class="col-lg-9">
                               <input id="queue-date" class="form-control queue-date" name="queue[date]" value="{{ date('Y-m-d') }}" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Time</label>
                            <div class="col-lg-9">
                                <select class="form-control queue-select" name="queue[time]">
                                    <!--<option value="Breakfast">Breakfast</option>
                                    <option value="Lunch">Lunch</option>
                                    <option value="Dinner">Dinner</option>
                                    !-->
                                    <?
                                        for($x=6;$x < 12;$x++) {

                                            if($x < 10) {
                                                $value = "0$x";
                                            } else {
                                                $value = $x;
                                            }

                                            echo "<option value=\"{$value}:00\">{$value}:00 AM</option>";
                                            echo "<option value=\"{$value}:30\">{$value}:30 AM</option>";
                                        }   

                                        echo "<option value=\"12:00\">12:00 PM</option>";
                                        echo "<option value=\"12:30\">12:30 PM</option>";

                                        for($x=13;$x < 23;$x++) {

                                            $y = $x - 12;

                                            if($y < 10) {
                                                $value = "0$y";
                                            } else {
                                                $value = $y;
                                            }

                                            echo "<option value=\"{$x}:00\">{$value}:00 PM</option>";
                                            echo "<option value=\"{$x}:30\">{$value}:30 PM</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Party Size</label>
                            <div class="col-lg-9">
                                <select class="form-control queue-select" name="queue[size]">
                                    <?
                                        for($x=1;$x<13;$x++) {
                                            echo "<option value='{$x}'>{$x}</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                          <label class="col-lg-3 col-sm-3 control-label">Description</label>
                          <div class="col-lg-9">
                                <input type="text" class="form-control" name="queue[description]" placeholder="Description">
                          </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                  </form>
                </div>
            </section>
        </div>
    </div>
  <script>
  $(function() {
        $('#queue-date').datepicker({
            format:"yyyy-mm-dd"
        });

        $(".queue-select").select2();
  });
  </script>
@endsection