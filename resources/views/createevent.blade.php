@extends('layouts.app')

@section('content')


<div class="panel panel-default">
    <div class="panel-heading">Profile</div>

    <div class="panel-body">

    <div class="container">
    <br/>
    <form id = "addForm" method="post" action="{{url('event/add')}}">
        @csrf
        <div class="row">
        <div class="col-md-4"></div>
        <div class="form-group col-md-4">
            <label for="Title">Title:</label>
            <input type="text" class="form-control" name="title">
        </div>
        </div>
        <div class="row">
        <div class="col-md-4"></div>
        <div class="form-group col-md-4">
            <strong> Start Date : </strong>  
            <input class="date form-control"  type="text" id="startdate" name="startdate">   
        </div>
        </div>
        <div class="row">
        <div class="col-md-4"></div>
        <div class="form-group col-md-4">
            <strong> End Date : </strong>  
            <input class="date form-control"  type="text" id="enddate" name="enddate">   
        </div>
        </div>
        <div class="row">
        <div class="col-md-4"></div>
        <div class="form-group col-md-4">
            <button type="button" onclick="add()" class="btn btn-success">Add Event</button>
        </div>
        </div>
    </form>
    </div>
    </div>
</div>


@stop

@section('scripts')
<script type="text/javascript">
    $('#startdate').datepicker({ 
        autoclose: true,   
        format: 'yyyy-mm-dd'  
    });
    $('#enddate').datepicker({ 
        autoclose: true,   
        format: 'yyyy-mm-dd'
    }); 
    var add = function() {
        if($('#startdate').val()>$('#enddate').val())
        {
            alert("Set Correct Date");            
        }        
        else{
            $('#addForm').submit();
        }
    };
</script>
@endsection
<!-- createevent.blade.php -->
