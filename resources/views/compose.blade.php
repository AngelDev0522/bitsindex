@extends('layouts.app')

@section('content')
<div class="row">
<div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-content mailbox-content">
                <div class="file-manager">
                    <!-- <a class="btn btn-block btn-primary compose-mailbtn btn-block btn-primary compose-mail" href="{{url('compose')}}">Compose Mail</a> -->
                    <div class="space-25"></div>
                    <h5>Folders</h5>
                    <ul class="folder-list m-b-md" style="padding: 0">
                        <li><a href="{{url('inbox')}}"> <i class="fa fa-inbox "></i> Inbox
                            <span class="label label-warning pull-right"><?php echo count($inbox); ?></span>
                        </a></li>
                        <li><a href="{{url('sentmail')}}">
                            <i class="fa fa-envelope-o"></i> Sent Mail
                            <span class="label label-warning pull-right"><?php echo count($outbox); ?></span>
                        </a></li>
                    </ul>

                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9 animated fadeInRight">
    <div class="mail-box-header">
        <h2>
            Compose mail
        </h2>
    </div>
        <div class="mail-box">
            <div class="mail-body">
                <form class="form-horizontal" method="get">
                    <div class="form-group"><label class="col-sm-2 control-label">To:</label>
                        <div class="col-sm-10">
                            <!-- <input id = "receiveEmail" type="text" class="form-control" value=""> -->
                            <input id = "receiveEmail" class="tagsinput form-control" type="text" value=""/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Subject:</label>
                        <div class="col-sm-10">
                            <input id = "subject" type="text" class="form-control" value="">
                        </div>
                    </div>
                </form>
            </div>
            <div class="mail-text h-200">
                <div class="summernote"></div>
            </div>
            <div class="clearfix"></div>
            <div class="mail-body text-right tooltip-demo">
                <button id="save" class="btn btn-primary" onclick="save()" type="button"><i class="fa fa-reply"></i> Send</button>
                <button id="clear" class="btn btn-primary" onclick="cleardata()" type="button"><i class="fa fa-times"></i> Clear</button>
            </div>
            <!-- <button id="save" class="btn btn-primary" onclick="save()" type="button">Save 2</button> -->
        </div>
    </div>
</div>
@stop

@section('scripts')
    <script src="{{ URL::asset('/Inspina/js/plugins/summernote/summernote.min.js ') }}"></script>
    <script>
        $(document).ready(function(){
            $('.summernote').summernote();
            $('.tagsinput').tagsinput({
                tagClass: 'label label-primary'
            });
        });
        var save = function() {
            var markup = $('.summernote').summernote('code');
            var receiver = $('#receiveEmail').val();
            var subject = $('#subject').val();
            $.ajax({
                type:'POST',
                url:'/sendemail',
                data:{receiver:receiver,email:markup,subject:subject},
                success:function(data){
                    window.location.href = "/sentmails";
                    // olivekoko@gmail.com,popov5220@hotmail.com,voljin522@gmail.com,aaa@bitsindex.com
                    // alert(data);
                }
            });
        };
        var cleardata = function() {
            var markupStr = '';
            $('.summernote').summernote('code',markupStr);
        };
    </script>
@endsection
<!-- createevent.blade.php -->
