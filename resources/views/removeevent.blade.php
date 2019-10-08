@extends('layouts.app')

@section('content')
<div class="col-lg-6">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>Calendar Table</h5>            
        </div>
        <div class="ibox-content">

            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Remove</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    for($i=0;$i<count($events);$i++)
                    {
                        echo "<tr>";
                        echo "<td>".($i+1)."</td>";
                        echo "<td>".$events[$i]->title."</td>";
                        echo "<td>".$events[$i]->start_date."</td>";
                        echo "<td>".$events[$i]->end_date."</td>";
                        echo "<td><a href='".url('/event/remove/'.$events[$i]->id)."'><i class='fa fa-trash'></a></td>";
                        echo "</tr>";
                    }
                ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

@stop

@section('scripts')

@endsection
<!-- createevent.blade.php -->
